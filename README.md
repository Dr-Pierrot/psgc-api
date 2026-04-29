# PSGC API

A Laravel REST API for Philippine Standard Geographic Code (PSGC) Q1 2026 data. It exposes searchable and paginated endpoints for regions, provinces, cities, municipalities, sub-municipalities, barangays, and PSGC classification values.

The API is documented with OpenAPI annotations through L5 Swagger and includes token-based authentication using Laravel Sanctum.

## Tech Stack

- PHP `^8.3`
- Laravel `^13.0`
- Laravel Sanctum `^4.0`
- L5 Swagger `^11.0`
- MySQL or another Laravel-supported database
- Vite, Tailwind CSS, and Laravel Vite Plugin for frontend asset tooling

## Project Structure

```text
app/Http/Controllers/API/AuthenticationController.php
app/Http/Controllers/API/PSGC_new/Q12026/
app/Models/PSGC_new/Q12026.php
app/Traits/PSGCHelper.php
database/migrations/2026_04_21_151103_create_q1-2026_table.php
database/q1-2026.sql
routes/api.php
storage/api-docs/api-docs.json
```

## PSGC Data

This project uses the `q1-2026` database table. The included SQL file, `database/q1-2026.sql`, contains `43,768` `INSERT` statements for Q1 2026 PSGC records.

Main record fields:

- `psgc_code`
- `name`
- `correspondence_code`
- `geographic_level`
- `old_name`
- `city_classification`
- `income_classification`
- `urban_rural`
- `population`
- `region_code`
- `province_code`
- `mun_city_code`
- `barangay_code`
- `mun_city_identifier`
- `barangay_identifier`

Geographic level values used by the model:

| Value | Meaning |
| --- | --- |
| `Reg` | Region |
| `Prov` | Province |
| `City` | City |
| `Mun` | Municipality |
| `SubMun` | Sub-municipality |
| `Bgy` | Barangay |

## Installation

Clone the repository and install dependencies:

```bash
composer install
npm install
```

Create a `.env` file with the usual Laravel settings and configure your database connection. This repository does not currently include `.env.example`, so create the file manually when setting up a fresh clone. Example MySQL settings:

```env
APP_NAME="PSGC API"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=psgc_api
DB_USERNAME=root
DB_PASSWORD=
```

Then generate the application key:

```bash
php artisan key:generate
```

Run migrations:

```bash
php artisan migrate
```

Import the PSGC data:

```bash
mysql -u root -p psgc_api < database/q1-2026.sql
```

Optional: seed the default test user defined in `DatabaseSeeder`:

```bash
php artisan db:seed
```

Default seeded user:

```text
Email: jaycee@gmail.com
Password: Pass1234!
```

Start the API server:

```bash
php artisan serve
```

Default local base URL:

```text
http://127.0.0.1:8000/api
```

## API Documentation

Swagger UI is configured at:

```text
http://127.0.0.1:8000/api/documentation
```

The generated JSON documentation is stored at:

```text
storage/api-docs/api-docs.json
```

To regenerate Swagger documentation:

```bash
php artisan l5-swagger:generate
```

## Authentication

Public auth endpoints:

| Method | Endpoint | Description |
| --- | --- | --- |
| `POST` | `/api/register` | Register a user |
| `POST` | `/api/login` | Login and receive a Sanctum token |

Protected endpoints require this header:

```http
Authorization: Bearer <token>
```

Protected auth endpoints:

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/user` | Get authenticated user information |
| `POST` | `/api/logout` | Revoke the authenticated user's tokens |

Register request:

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "Pass1234!"
}
```

Login request:

```json
{
  "email": "john@example.com",
  "password": "Pass1234!"
}
```

Successful login response includes:

```json
{
  "response_code": 200,
  "status": "success",
  "message": "Login successful",
  "user_info": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "token": "plain-text-sanctum-token"
}
```

## Response Format

Paginated endpoints return:

```json
{
  "response_code": 200,
  "status": "success",
  "message": "Data retrieved successfully",
  "data": [],
  "pagination": {
    "current_page": 1,
    "total_pages": 1,
    "per_page": 15,
    "total": 0
  }
}
```

Single-record and classification endpoints return:

```json
{
  "response_code": 200,
  "status": "success",
  "message": "Record retrieved successfully",
  "data": {}
}
```

Error responses return:

```json
{
  "response_code": 404,
  "status": "error",
  "message": "Record not found",
  "data": null
}
```

## Query Parameters

Most list endpoints support:

| Parameter | Default | Description |
| --- | --- | --- |
| `page` | `1` | Pagination page number |
| `per_page` | `15` | Records per page |

Search endpoint:

| Parameter | Required | Description |
| --- | --- | --- |
| `q` | Yes | Searches `name` and `psgc_code` |

Example:

```text
GET /api/psgc/search?q=Caloocan&per_page=10
```

## Endpoints

### PSGC

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/psgc` | List all PSGC records |
| `GET` | `/api/psgc/search?q={query}` | Search by name or PSGC code |
| `GET` | `/api/psgc/{psgc_code}` | Get one PSGC record |

### Classifications

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/geographic-levels` | List distinct geographic levels |
| `GET` | `/api/city-classifications` | List distinct city classifications |
| `GET` | `/api/income-classifications` | List distinct income classifications |
| `GET` | `/api/urban-rural-classifications` | List distinct urban/rural classifications |

### Regions

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/regions` | List regions |
| `GET` | `/api/regions/{psgc_code}` | Get one region |
| `GET` | `/api/regions/{psgc_code}/provinces` | List provinces in a region |
| `GET` | `/api/regions/{psgc_code}/cities` | List cities in a region |
| `GET` | `/api/regions/{psgc_code}/municipalities` | List municipalities in a region |
| `GET` | `/api/regions/{psgc_code}/sub-municipalities` | List sub-municipalities in a region |
| `GET` | `/api/regions/{psgc_code}/barangays` | List barangays in a region |

### Provinces

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/provinces` | List provinces |
| `GET` | `/api/provinces/{psgc_code}` | Get one province |
| `GET` | `/api/provinces/{psgc_code}/cities` | List cities and municipalities in a province |
| `GET` | `/api/provinces/{psgc_code}/barangays` | List barangays in a province |

### Cities

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/cities` | List cities |
| `GET` | `/api/cities/{psgc_code}` | Get one city |
| `GET` | `/api/cities/{psgc_code}/barangays` | List barangays in a city or municipality |
| `GET` | `/api/cities/classification/highly-urbanized` | List highly urbanized cities |
| `GET` | `/api/cities/classification/component` | List component cities |
| `GET` | `/api/cities/classification/independent-component` | List independent component cities |

### Municipalities

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/municipalities` | List municipalities |
| `GET` | `/api/municipalities/{psgc_code}` | Get one municipality |

### Sub-Municipalities

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/sub-municipalities` | List sub-municipalities |
| `GET` | `/api/sub-municipalities/{psgc_code}` | Get one sub-municipality |

### Barangays

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/barangays` | List barangays |
| `GET` | `/api/barangays/{psgc_code}` | Get one barangay |

## Example Requests

```bash
curl "http://127.0.0.1:8000/api/regions"
curl "http://127.0.0.1:8000/api/psgc/search?q=Caloocan"
curl "http://127.0.0.1:8000/api/barangays/1380100001"
```

Authenticated request:

```bash
curl "http://127.0.0.1:8000/api/user" \
  -H "Authorization: Bearer <token>"
```

## Development

Run tests:

```bash
php artisan test
```

Run Laravel Pint:

```bash
./vendor/bin/pint
```

Build frontend assets:

```bash
npm run build
```

Run the Vite development server:

```bash
npm run dev
```

The Composer `dev` script starts Laravel, queue listener, logs, and Vite together:

```bash
composer run dev
```

## Notes

- API routes are defined in `routes/api.php`.
- PSGC responses are formatted by `App\Traits\PSGCHelper`.
- The `q1-2026` model hides `created_at` and `updated_at` in serialized PSGC responses.
- Only `/api/user` and `/api/logout` are protected by Sanctum. PSGC data endpoints are public.
- `.env` is ignored by Git and should not be committed.

## License

This project is open-sourced under the MIT license.
