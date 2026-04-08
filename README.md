# Sistema de Flota Vehicular

API REST construida con Laravel 13 para gestionar:

- Tipos de vehiculo
- Vehiculos
- Viajes
- Registros de combustible
- Registros de mantenimiento
- Incidencias
- Autenticacion por token con Laravel Sanctum

## Requisitos

- PHP 8.3 o superior
- Composer
- Node.js 20 o superior
- NPM
- Base de datos SQLite o MySQL/MariaDB

## Stack principal

- Laravel 13
- Laravel Sanctum
- Spatie Laravel Permission
- Pest para pruebas
- Vite para frontend/assets

## 1) Clonar e instalar dependencias

En la raiz del proyecto ejecuta:

composer install
npm install

## 2) Configurar variables de entorno

Copia el archivo de ejemplo y genera la key:

copy .env.example .env
php artisan key:generate

Si usas PowerShell y ya existe .env, puedes omitir la copia.

## 3) Configurar base de datos

### Opcion A: SQLite (recomendada para desarrollo rapido)

1. En .env deja:
DB_CONNECTION=sqlite

2. Crea el archivo de base:

PowerShell:
New-Item -ItemType File -Path database/database.sqlite -Force

### Opcion B: MySQL/MariaDB

Configura en .env:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_de_tu_bd
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password

## 4) Migraciones y seeders

Para levantar todo desde cero:

php artisan migrate:fresh --seed

Este comando:

- Recrea todas las tablas
- Inserta datos base
- Crea roles y permisos (Spatie)
- Crea usuarios iniciales

## 5) Ejecutar el proyecto

### Backend (API)

php artisan serve

API disponible por defecto en:
http://127.0.0.1:8000

### Frontend / assets (si aplica)

Modo desarrollo:
npm run dev

Build de produccion:
npm run build

## 6) Autenticacion (Sanctum)

Endpoint de login:
POST /api/v1/auth/login

Ejemplo de credenciales seed:

- admin@flota.local / password
- supervisor@flota.local / password
- conductor@flota.local / password

Respuesta del login incluye un token Bearer. Usa ese token en Authorization para endpoints protegidos.

## 7) Rutas API

Prefijo base:
/api/v1

Auth:

- POST /auth/login
- POST /auth/logout
- GET /auth/me

Recursos (apiResource):

- /vehicle-types
- /vehicles
- /trips
- /fuel-logs
- /maintenance-logs
- /incidents

## 8) Documentacion OpenAPI

Archivo:
[docs/swagger.yaml](docs/swagger.yaml)

Nota importante:
La especificacion fue ajustada al estado actual del proyecto. Si implementas o cambias controladores/requests, actualiza este archivo para mantener sincronia.

## 9) Ejecutar pruebas

php artisan test

o

vendor/bin/pest

## 10) Problemas comunes

1. Error de permisos en token Sanctum:
Verifica que corriste migraciones y que existe la tabla personal_access_tokens.

2. 401 en endpoints protegidos:
Confirma que envias Authorization: Bearer TU_TOKEN.

3. 403 en create/update:
Algunos FormRequest pueden negar autorizacion si no estan implementados para permitir la accion.

4. Error al instalar dependencias de Node:
Revisa package.json; debe tener JSON valido antes de correr npm install.

## Comando rapido de arranque

Si ya tienes PHP, Composer, Node y NPM listos:

composer install
copy .env.example .env
php artisan key:generate
New-Item -ItemType File -Path database/database.sqlite -Force
php artisan migrate:fresh --seed
php artisan serve

En otra terminal:

npm install
npm run dev
