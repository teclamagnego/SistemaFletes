Este es un README.md diseñado específicamente para que una IA (o un equipo de desarrolladores) entienda la lógica de negocio, la arquitectura de datos y el flujo de trabajo de tu sistema de fletes en Laravel.

# ---

**🚚 Sistema de Gestión de Fletes y Logística (SGF)**

## **📌 Visión General**

El objetivo de este sistema es gestionar el ciclo de vida completo del transporte de mercancías entre múltiples agencias (sucursales). El sistema permite el registro de envíos (Guías), la asignación de transportistas y el control financiero de las comisiones por agencia.

## ---

**🏗️ Arquitectura de Datos (Modelos Principales)**

Para que la IA comprenda la estructura, definimos las entidades clave:

* **Agency (Agencia):** Puntos físicos de recepción/entrega. Son las encargadas de originar el envío y cobrar comisiones.  
* **Client (Cliente):** Actúa como **Remitente** o **Destinatario**. Un cliente puede tener ambos roles en distintas guías.  
* **Item (Artículo):** Descripción de la carga (peso, dimensiones, tipo de mercancía).  
* **Carrier (Transportista):** Entidad (chofer/vehículo) que traslada la carga desde el origen al destino.  
* **Shipment (Guía):** El corazón del sistema. Une al remitente, destinatario, artículos, transportista y agencias.

## ---

**🔄 El Circuito Logístico (Workflow)**

El proceso se divide en cuatro etapas críticas que la IA debe implementar:

## **1\. Admisión y Cotización**

* **Origen:** Puede ser "Recepción en Agencia" o "Retiro a Domicilio".  
* **Documentación:** Se genera la **Guía** con un número de seguimiento único.  
* **Pago:** Se debe definir el payment\_mode:  
  * **PP (Pagado en Origen):** El remitente paga al dejar el paquete.  
  * **CC (Pago en Destino / Cobro Revertido):** El destinatario paga al recibir.  
* **Comisión:** La agencia que registra la guía se marca como commission\_agent\_id.

## **2\. Almacenamiento y Consolidación**

* El paquete permanece en el inventario de la agencia de origen con estado In Office.  
* Se agrupan guías según la localidad de destino.

## **3\. Asignación y Despacho**

* Se selecciona un **Transportista**.  
* Se cambia el estado de la guía a In Transit.  
* Se genera una "Hoja de Ruta" para el transportista.

## **4\. Entrega y Liquidación**

* El paquete llega a la agencia de destino o al domicilio del cliente.  
* Estado final: Delivered.  
* Si el pago era "En Destino", la agencia de destino recauda el dinero y se registra el cierre de caja.

## ---

**🛠️ Plan de Ejecución Técnica (Roadmap)**

## **Fase 1: Estructura Base (Backend)**

* \[ \] Crear migraciones con relaciones de clave foránea (Ej: sender\_id, receiver\_id).  
* \[ \] Implementar **Polymorphic Relations** si los artículos pueden ser de diferentes tipos (Cajas, Pallets, Sobres).  
* \[ \] Configurar **Laravel Sanctum** para la API de los transportistas.

## **Fase 2: Lógica de Negocio**

* \[ \] **Service Layer:** Crear un ShipmentService para manejar la lógica de cambio de estados y validación de pagos.  
* \[ \] **Cálculo de Comisiones:** Script que calcule el porcentaje para la agencia basado en el total de la guía.

## **Fase 3: Interfaz y Seguimiento**

* \[ \] Panel administrativo para agencias (Filtrado por localidad).  
* \[ \] Módulo de asignación rápida de transportistas (Drag & Drop).

## ---

**🧮 Lógica de Comisiones (Referencia)**

La comisión se calcula sobre el valor neto del flete ($V\_f$):

$$C \= V\_f \\times P\_a$$  
Donde:

* $C$: Comisión de la agencia.  
* $V\_f$: Valor del flete.  
* $P\_a$: Porcentaje asignado a la agencia (ej. 0.10 para 10%).

## ---

**🤖 Instrucciones para la IA**

Cuando trabajes en este proyecto, prioriza:

1. **Atomicidad:** Un envío no puede existir sin un remitente y un destinatario válidos.  
2. **Trazabilidad:** Cada cambio de estado debe quedar registrado en una tabla shipment\_logs.  
3. **Seguridad:** Las agencias solo deben ver las guías donde son Origen o Destino.

---

¿Te gustaría que desarrolle las migraciones de base de datos específicas para este modelo de Laravel?