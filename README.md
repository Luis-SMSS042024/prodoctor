# ProDoctor — Sistema Web de Gestión para Gabinete Médico

ProDoctor es una solución web integral diseñada para la administración y control de un gabinete médico de atención primaria. Permite centralizar los expedientes clínicos, programar citas de forma interactiva con una agenda de calendario, gestionar especialidades y doctores, y dar seguimiento a tratamientos y recetas médicas con roles específicos para administradores, médicos y pacientes.

---

## Integrantes del Grupo

*   Luis Alexander Rivera Álvarez — `SMSS042024`
*   Walter José Ramirez Pérez — `SMSS034824`
*   Ludwin Saul Vasquez Romero — `SMSS034624`
*   Josué Alexander Turcios Quintanilla — `SMSS948820`
*   Kriscia Tatiana Del Cid Argueta — `SMSS089424`

---

## Gestor de Base de Datos Utilizado

El proyecto utiliza **MySQL** como sistema de gestión de base de datos relacional para el almacenamiento de la información de usuarios, especialidades, doctores, pacientes, citas, disponibilidad de horarios, procedimientos y seguimientos clínicos.

El archivo de volcado SQL respectivo con la estructura y los datos semilla se encuentra en:
`database/prodoctor.sql`

---

## Requisitos de Instalación

Para ejecutar este proyecto en tu entorno local, asegúrate de tener instalado:
*   **PHP** (Versión 8.2 o superior)
*   **Composer** (Gestor de dependencias de PHP)
*   **Node.js & NPM** (Para compilar assets de Vue 3)
*   **Laragon** u otro servidor local (con soporte de **MySQL**)

### Pasos para la Configuración

1.  **Clonar el repositorio:**
    ```bash
    git clone <url-del-repositorio>
    cd prodoctor
    ```

2.  **Instalar dependencias de PHP:**
    ```bash
    composer install
    ```

3.  **Instalar dependencias de JavaScript y compilar assets:**
    ```bash
    npm install
    npm run build
    ```

4.  **Configurar archivo de entorno:**
    Copia el archivo `.env.example` como `.env` en la raíz del proyecto y configura tus credenciales de base de datos MySQL (`DB_DATABASE=prodoctor`, `DB_USERNAME`, `DB_PASSWORD`).
    *También asegúrate de generar la clave de la aplicación:*
    ```bash
    php artisan key:generate
    ```

5.  **Restablecer la Base de Datos y Sembrar Datos:**
    Puedes ejecutar el script automatizado provisto en la raíz del proyecto para Windows:
    *   Haz doble clic en **`recrear_base_de_datos.bat`**
    
    *O manualmente desde la terminal ejecutando:*
    ```bash
    php artisan migrate:fresh --seed
    ```

6.  **Levantar el servidor de desarrollo:**
    ```bash
    php artisan serve
    ```
    *(Si no estás usando el servidor de Laragon para servir el host directamente, accede mediante `http://127.0.0.1:8000`)*

---

## Cuentas de Acceso de Prueba (Seeding)

| Rol de Usuario | Correo de Prueba | Contraseña | Acceso y Dashboard Esperado |
| :--- | :--- | :--- | :--- |
| **Administrador** | `admin@prodoctor.com` | `admin123` | Consola Administrativa (Tema Morado y Fucsia). |
| **Doctor General** | `doctor1@prodoctor.com` | `doctor123` | Dashboard del Doctor con agenda diaria (Tema Azul). |
| **Doctor Especialista** | `doctor2@prodoctor.com` | `doctor123` | Dashboard de Cardiología y consultas médicas. |
| **Paciente Registrado** | `paciente1@prodoctor.com` | `paciente123` | Portal del Paciente (Tema Verde Esmeralda/Teal)se puede registrastrar tambien. |
