# Laravel : Reservasi Booking Service

[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-FF2D20.svg?style=flat-square\&logo=laravel)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%208.3-777BB4.svg?style=flat-square\&logo=php)](https://php.net)
[![Swagger](https://img.shields.io/badge/Swagger-Supported-85EA2D.svg?style=flat-square\&logo=swagger)](https://swagger.io/)
[![GraphQL](https://img.shields.io/badge/GraphQL-Lighthouse-E10098.svg?style=flat-square\&logo=graphql)](https://graphql.org/)

Project ini merupakan implementasi mini service **Reservasi & Booking** pada sistem **Smart Hospitality** untuk mata kuliah Integrasi Aplikasi Enterprise (IAE).

Service ini bertanggung jawab dalam pengelolaan data reservasi tamu, verifikasi status reservasi, serta proses check-in tamu melalui REST API dan GraphQL.

---

## Informasi Mahasiswa

* Nama: FATHIR AULIA HELMI
* NIM: 102022430001
* Kelompok: 11
* Service: Reservasi & Booking

---

## Fitur Utama

* REST API
* GraphQL API
* Swagger UI Documentation
* API Key Authentication
* Docker Support
* Seeder Data Dummy

---

## Teknologi & Library

Project ini dibangun menggunakan:

* Laravel 11
* MySQL 8
* Docker & Docker Compose
* Lighthouse GraphQL
* L5 Swagger
* Swagger PHP

---

## Endpoint REST API

### Collection

```http
GET /api/v1/reservasis
```

Mengambil seluruh data reservasi.

### Resource

```http
GET /api/v1/reservasis/{id}
```

Mengambil detail reservasi berdasarkan ID.

### Action

```http
POST /api/v1/reservasis/{id}/checkin
```

Melakukan proses check-in dan mengubah status reservasi menjadi `checked_in`.

### Update

```http
PUT /api/v1/reservasis/{id}/status
```

Mengubah status reservasi.

Contoh Request:

```json
{
    "status": "confirmed"
}
```

---

## Authentication

Semua endpoint menggunakan API Key melalui header:

```http
X-IAE-KEY: 102022430001
```

---

## GraphQL

### Endpoint

```http
http://localhost:8000/graphql
```

### Sample Query

```graphql
{
  reservasis {
    id
    booking_code
    guest_name
    status
  }
}
```

---

## Dokumentasi API

### Swagger UI

```http
http://localhost:8000/api/documentation
```

### GraphiQL Playground

```http
http://localhost:8000/graphiql
```

---

## Menjalankan Project dengan Docker

### Build dan Jalankan Container

```bash
docker compose up -d --build
```

### Generate Application Key

```bash
docker compose exec app php artisan key:generate
```

### Jalankan Migration

```bash
docker compose exec app php artisan migrate
```

### Jalankan Seeder

```bash
docker compose exec app php artisan db:seed --class=ReservasiSeeder
```

### Generate Swagger

```bash
docker compose exec app php artisan l5-swagger:generate
```

---

## Konfigurasi Database

### MySQL Container

```text
Host     : localhost
Port     : 3307
Database : reservasi_db
Username : reservasi_user
Password : reservasi_pass
```

---

## Struktur Response API

### Success Response

```json
{
  "status": "success",
  "message": "Data retrieved successfully",
  "data": [],
  "meta": {
    "service_name": "Reservasi-Service",
    "api_version": "v1"
  }
}
```

### Error Response

```json
{
  "status": "error",
  "message": "Resource not found",
  "errors": null
}
```

---

## Proses Bisnis yang Didukung

1. Staff mencari data reservasi tamu
2. Staff melihat detail reservasi
3. Staff memverifikasi status reservasi
4. Staff melakukan check-in
5. Sistem memperbarui status reservasi menjadi checked_in

---

## Menjalankan Testing Endpoint

Contoh request menggunakan API Key:

```bash
curl -X GET \
http://localhost:8000/api/v1/reservasis \
-H "X-IAE-KEY: 102022430001"
```

---

## Author

102022430001 - FATHIR AULIA HELMI
