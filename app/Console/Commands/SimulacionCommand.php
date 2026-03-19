<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ShipmentController;
use Illuminate\Http\Request;
use App\Models\Agency;
use App\Models\Articulo;
use App\Models\Carrier;
use App\Models\FormaPago;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Faker\Factory as Faker;
use Exception;

class SimulacionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simulacion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta una simulación: Crea 10 clientes y 20 envíos entre ellos con precios y productos al azar.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $faker = Faker::create('es_AR');

        // Logueamos un usuario porque ShipmentController usa Auth::id() en las transacciones
        $user = User::first();
        if (!$user) {
            $this->error('No hay usuarios en la base de datos para simular Auth::id(). Por favor crea uno primero.');
            return;
        }
        Auth::login($user);

        $this->info("=== INICIANDO SIMULACIÓN ===");

        // Escenario 1: Crear 10 clientes
        $this->newLine();
        $this->info("--> Escenario 1: Creando 10 clientes simulados...");

        $clienteController = new ClienteController();
        $clientesCreados = [];

        for ($i = 1; $i <= 10; $i++) {
            $nombreFantasia = $faker->company . ' (Simulado)';
            $direccion = $faker->streetAddress;

            $request = Request::create('/clientes/quick', 'POST', [
                'nombre_fantasia' => $nombreFantasia,
                'direccion' => $direccion,
            ]);

            try {
                $response = $clienteController->storeQuick($request);
                $clienteData = json_decode($response->getContent());

                if (isset($clienteData->id)) {
                    $clientesCreados[] = $clienteData->id;
                    $this->line("    ✓ Cliente [$clienteData->id] creado: $nombreFantasia");
                }
                else {
                    $this->error("    ✗ Error al crear cliente $i: Falló la respuesta del controlador.");
                }
            }
            catch (\Illuminate\Validation\ValidationException $e) {
                $this->error("    ✗ Error de validación al crear cliente $i: " . json_encode($e->errors()));
            }
            catch (Exception $e) {
                $this->error("    ✗ Excepción al crear cliente $i: " . $e->getMessage());
            }
        }

        if (count($clientesCreados) < 2) {
            $this->error('No se crearon suficientes clientes para realizar los envíos (mínimo 2). Simulacion abortada.');
            return;
        }

        // Escenario 2: 20 envíos
        $this->newLine();
        $this->info("--> Escenario 2: Creando 20 envíos aleatorios entre los clientes creados...");

        $shipmentController = new ShipmentController();

        $agencias = Agency::where('activa', true)->get();
        if ($agencias->isEmpty()) {
            $this->error('No hay agencias activas para asignar origen y destino.');
            return;
        }
        $agenciasIds = $agencias->pluck('id')->toArray();

        $articulos = Articulo::all();
        if ($articulos->isEmpty()) {
            $this->error('No hay artículos cargados para agregar a las guías.');
            return;
        }

        $carriers = Carrier::where('activo', true)->get();
        $carrierIds = $carriers->pluck('id')->toArray();
        $singleCarrierId = count($carrierIds) === 1 ? $carrierIds[0] : null;

        $formaPago = FormaPago::first();
        if (!$formaPago) {
            $this->error('No hay formas de pago cargadas.');
            return;
        }

        $exitos = 0;
        for ($i = 1; $i <= 20; $i++) {
            // Seleccionar remitente y destinatario aleatorio y distinto
            $senderId = $faker->randomElement($clientesCreados);
            $receiverId = $faker->randomElement(array_diff($clientesCreados, [$senderId]));

            $origenId = $faker->randomElement($agenciasIds);
            $destinoId = $faker->randomElement($agenciasIds);

            // Carrier random (si hay mas de uno) o null si el controller permite mandarlo null para resolverse como el single default
            $carrierId = null;
            if (!$singleCarrierId && !empty($carrierIds)) {
                $carrierId = $faker->randomElement($carrierIds);
            }

            // Seleccionar artículos (entre 1 a 4 por guía)
            $cantidadItems = $faker->numberBetween(1, 4);
            $itemsRequest = [];
            for ($j = 0; $j < $cantidadItems; $j++) {
                $art = $articulos->random();
                $qty = $faker->numberBetween(1, 10);
                $precioUnitario = $faker->randomFloat(2, 50, 5000); // Precio random entre 50 y 5000
                // Nueva lógica: bonif suele ser 100 (100% del precio), o un porcentaje pequeño si es comisión/seguro
                $bonif = $faker->boolean(80) ? 100 : $faker->randomFloat(2, 0.1, 5); 
                $totalLinea = ($qty * $precioUnitario * $bonif) / 100;

                $itemsRequest[] = [
                    'articulo_id' => $art->id,
                    'descripcion' => $art->nombre ?? $art->codigo ?? 'Artículo aleatorio',
                    'cantidad' => $qty,
                    'precio_unitario' => $precioUnitario,
                    'bonificacion' => $bonif,
                    'total' => round($totalLinea, 2),
                    'iva' => 0,
                ];
            }

            $shipmentData = [
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'origin_agency_id' => $origenId,
                'destination_agency_id' => $destinoId,
                'carrier_id' => $carrierId,
                'fecha' => now()->format('Y-m-d'),
                'direccion_entrega' => $faker->boolean(50) ? $faker->streetAddress : null,
                'forma_pago_id' => $formaPago->id,
                'payer' => $faker->randomElement(['sender', 'receiver']),
                'items' => $itemsRequest,
                'notas' => 'Cargado por comando simulacion.',
            ];

            // Pasamos esta data en un request falso
            // Need to make sure the route actually is parsed correctly if we don't supply fully fleshed Request
            // but we don't need `route()` here. We just manually pass a request to the controller.
            $request = Request::create('foo', 'POST', $shipmentData);

            try {
                // To avoid Route facade acting up inside store(), let's try calling it natively: 
                // Wait! ShipmentController@store has a return redirect()->route('shipments.index')
                // This will fail if the request isn't attached to a router context, or maybe it works if the route exists globally. Let's find out!
                $shipmentController->store($request);
                $exitos++;
                $this->line("    ✓ Envío $i creado: de cliente [$senderId] a [$receiverId]");
            }
            catch (\Illuminate\Validation\ValidationException $e) {
                $this->error("    ✗ Error de validación al crear envío $i: " . json_encode($e->errors()));
            }
            catch (Exception $e) {
                $this->error("    ✗ Excepción al crear envío $i: " . $e->getMessage() . " => File: " . $e->getFile() . " Line: " . $e->getLine());
            }
        }

        $this->newLine();
        $this->info("=== SIMULACIÓN FINALIZADA ===");
        $this->info("Se crearon exitosamente " . count($clientesCreados) . " clientes y $exitos envíos.");
    }
}