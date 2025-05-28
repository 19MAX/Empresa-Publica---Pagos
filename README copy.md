# SISTEMA DE GESTIÓN DE CONGRESOS

## Descripción General
Este sistema permite la inscripción de usuarios a diversos eventos, gestionando tanto el registro como el cobro de las inscripciones. Los eventos pueden tener diferentes categorías, y los usuarios tienen la opción de pagar a través de varios métodos, incluyendo pagos en línea, depósitos manuales y pagos en puntos físicos.

### Funcionalidades Principales:
1. **Inscripción de usuarios en eventos**: Los usuarios pueden registrarse en diferentes eventos y categorías n cantidad de veces a través de un formulario en línea.
2. **Gestión de pagos**: El sistema admite tres métodos de pago:
   - **Pago en línea**: Los usuarios pueden realizar pagos de forma automática a través de payphone.
   - **Depósito bancario**: Los usuarios pueden subir el comprobante de su depósito, y el administrador podrá revisar y validar manualmente estos pagos.
   - **Pago en físico**: Los usuarios también pueden realizar pagos en puntos de pago autorizados, que serán registrados en el sistema por un administrador, con el rol de administrador de pagos.
3. **Revisión de depósitos**: El administrador tiene acceso a una sección donde puede revisar los comprobantes de depósito y aprobar, rechazar o rechazar el pago incompleto manualmente cada pago.
4. **Gestión de eventos y categorías**: El sistema permite la creación de eventos con diferentes categorías, cada una con su respectivo precio y condiciones.
5. **Generación de reportes**: El sistema incluye una funcionalidad para generar reportes sobre el estado de las inscripciones y los pagos.

---

## Requisitos del Sistema
- **Servidor web**: Apache
- **Base de datos**: MySQL
- **Framework**: CodeIgniter 4
- **PHP**: Versión 8.2 o superior

---

## Estructura del Proyecto

El sistema está organizado en diferentes carpetas según las responsabilidades de los controladores:

### 1. **Carpeta Admin**
   Controladores que manejan la administración general del sistema:
   - `CategoriesController`: Gestiona las categorías de eventos.
   - `DashboardController`: Muestra estadísticas y el estado del sistema para el administrador.
   - `DepositosController`: Permite al administrador revisar y validar depósitos, como enviar el email de depósitos.
   - `EventsController`: Controla la creación y gestión de eventos.
   - `FiltrosController`: Permite aplicar filtros para la búsqueda por cédulas y ver las inscripciones sus respectivos estados.
   - `InscripcionesController`: Gestiona las inscripciones pasando por el filtro de cédulas y estados, es donde se actualizan todas las inscripciones y se eliminan.
   - `PagosController`: Controla la revisión y gestión de pagos realizados por el método de depósitos.
   - `RegistrationsController`: Maneja las inscripciones por evento se puede editar los datos del usuario inscrito cambiando los datos de todas las inscripciones en las que se encuentra registrado,eliminar solo la inscripción, también muestra tod@s las inscripciones activas como eliminadas.
   - `UsersController`: Controla la gestión de usuarios dentro del sistema como la recaudación del usuario con session como la recaudación en linea y de todo lo recaudado.

### 2. **Carpeta Client**
   Controladores que gestionan las interacciones del cliente (usuario):
   - `ClientController`: Controla las vistas general del cliente y acciones generales del cliente.
   - `InscripcionController`: Permite a los clientes inscribirse en los eventos, registrarse en la API y validar si existe el cliente.
   - `DepositosController`: Gestiona la carga de comprobantes de depósito por parte de los usuarios.
