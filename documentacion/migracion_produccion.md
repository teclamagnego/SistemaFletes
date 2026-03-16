# Guía de Migración de Datos Legacy a Producción

Este documento detalla los pasos necesarios para migrar los datos históricos (clientes, facturas e ítems) desde los archivos SQL legacy al sistema actual.

## 1. Requisitos Previos

Asegúrese de que los siguientes archivos SQL se encuentren en la carpeta `documentacion/`:

- `agencias.sql` (Base de agencias)
- `dobleg_articulos.sql` (Catálogo de artículos/servicios)
- `dobleg_clientes.sql` (Padron de clientes legacy)
- `dobleg_cliente_facturas.sql` (Cabeceras de facturas históricas)
- `dobleg_cliente_item_facturas.sql` (Detalle de ítems de facturas)

## 2. Estrategia de Migración (Staging)

Dada la gran cantidad de datos (115,000+ facturas y 240,000+ ítems), no se procesan fila por fila en PHP para evitar agotamiento de memoria.
El script `DobleGGuiaSeeder` realiza los siguientes pasos automáticos:

1.  **Importación Raw**: Carga los archivos SQL en tablas temporales `temp_clientes`, `temp_facturas` y `temp_items`.
2.  **Transformación SQL**: Ejecuta sentencias `INSERT INTO ... SELECT` directamente en el motor de base de datos para mapear los campos legados a la estructura actual.
3.  **Unicidad**: Se utiliza el prefijo `OLD-` seguido del ID único de la factura original para el campo `tracking_number`, evitando colisiones por numeración duplicada de sucursales legacy.
4.  **Limpieza**: Elimina las tablas temporales una vez finalizado el proceso.

## 3. Pasos para la Ejecución

En el servidor de producción, ejecute los seeders en el siguiente orden:

### Paso A: Catálogos Base
Primero cargamos las agencias y artículos, ya que las guías dependen de estos IDs.

```bash
php artisan db:seed --class=AgencySeeder
php artisan db:seed --class=ArticuloSeeder
```

### Paso B: Clientes y Guías (Migración Masiva)
Este paso sincroniza los clientes e importa todo el historial de facturación. Puede tardar entre 1 y 3 minutos dependiendo del hardware del servidor.

```bash
php artisan db:seed --class=DobleGGuiaSeeder
```

## 4. Consideraciones Técnicas

- **Memoria**: El script eleva el `memory_limit` a `1GB` temporalmente para procesar los archivos SQL de gran tamaño (~40MB c/u).
- **Integridad**: Se desactivan los `FOREIGN_KEY_CHECKS` durante la carga de las tablas `temp_*` para permitir la importación de dumps legacy que podrían tener inconsistencias de integridad.
- **Estado de Guías**: Todas las facturas importadas se marcan con `status_id = 5` (Delivered) por ser datos históricos.
- **Carrier**: Se asigna el transportista por defecto (ID 1) y la forma de pago se mapea según el campo `contado` del legacy (1 -> Contado, otros -> Cuenta Corriente).

## 5. Verificación de Datos

Una vez terminado, puede verificar la carga ejecutando en consola:

```bash
php artisan tinker
>>> App\Models\Shipment::count(); // Debería devolver 115,000+
>>> App\Models\Cliente::count();  // Debería devolver 20,000+
```
