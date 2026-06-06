Room Chat - 1
Room #1 – Pengembangan Service Reservasi & Booking (Laravel REST API, Swagger, GraphQL, Docker)
Rekap Log Prompting
No	Prompt / Permintaan Pengguna	Respons / Hasil yang Diperoleh
1	Meminta penjelasan langkah-langkah pengerjaan Service 1 – Reservasi & Booking berdasarkan dokumen tugas dan template project Laravel yang telah dibuat.	Dijelaskan alur pengerjaan mulai dari analisis endpoint, pembuatan migration, model, seeder, controller, middleware API Key, routes, hingga pengujian menggunakan Postman.
2	Menunjukkan controller ReservasiController yang telah dibuat dan meminta pengecekan apakah masih ada yang perlu disesuaikan.	Dilakukan review kode controller, termasuk penambahan import model Reservasi, validasi status, konsistensi enum status, response code, serta penyesuaian format response API.
3	Mengalami error Postman Invalid URI http:///api/v1/reservasis.	Diberikan penjelasan bahwa URL tidak memiliki host yang valid serta cara menjalankan Laravel menggunakan php artisan serve dan mengakses endpoint melalui http://127.0.0.1:8000.
4	Endpoint resource, check-in, dan update status mengembalikan pesan "Reservasi not found".	Dilakukan analisis terhadap kemungkinan penyebab seperti data belum di-seed, tabel kosong, atau ID yang diakses tidak tersedia pada database.
5	Menginformasikan bahwa pengujian Postman sudah berhasil dan meminta langkah selanjutnya.	Dijelaskan tahapan lanjutan berupa implementasi Swagger/OpenAPI, GraphQL, dan Docker sesuai rubrik tugas.
6	Menampilkan kode controller yang sudah ditambahkan anotasi Swagger dan meminta validasi.	Dilakukan pengecekan anotasi OpenAPI serta diberikan saran penambahan schema parameter dan request body agar dokumentasi Swagger lebih lengkap.
7	Mengalami kendala saat mengakses GraphQL Playground yang menampilkan halaman "Not Found".	Dijelaskan bahwa beberapa versi Lighthouse tidak lagi menyediakan Playground secara otomatis dan dilakukan pengecekan endpoint GraphQL melalui route yang tersedia.
8	Menampilkan error GraphQL terkait parameter query yang tidak ditemukan.	Dijelaskan bahwa endpoint GraphQL harus menerima parameter query atau request body GraphQL, kemudian diberikan contoh query yang benar.
9	Berhasil menjalankan query GraphQL dan memperoleh data reservasi.	Dikonfirmasi bahwa instalasi Lighthouse, schema GraphQL, dan query GraphQL telah berjalan dengan baik sehingga kebutuhan GraphQL pada tugas telah terpenuhi.
10	Menampilkan hasil dokumentasi Swagger yang berhasil terbuka namun mengembalikan error 401 Unauthorized.	Dijelaskan bahwa Swagger tidak mengirim header API Key sehingga middleware menolak akses. Dijelaskan pula bahwa middleware API Key telah berfungsi sesuai kebutuhan integrasi.
11	Meminta pembuatan konfigurasi Docker lengkap beserta .dockerignore dan volume database agar data tetap tersimpan.	Dibuatkan Dockerfile, docker-compose.yml, .dockerignore, konfigurasi environment database, langkah build dan menjalankan container, serta penggunaan volume permanen untuk MySQL.
Ringkasan Hasil Akhir
Selama sesi ini berhasil diselesaikan beberapa komponen utama pada Service Reservasi & Booking:
•	Implementasi REST API menggunakan Laravel. 
•	Pembuatan endpoint Collection, Resource, Action, dan Update. 
•	Implementasi middleware API Key untuk keamanan endpoint. 
•	Pengujian endpoint menggunakan Postman. 
•	Dokumentasi API menggunakan Swagger/OpenAPI. 
•	Implementasi GraphQL menggunakan Lighthouse. 
•	Pengujian query GraphQL terhadap data reservasi. 
•	Penyusunan konfigurasi Docker dan Docker Compose. 
•	Penyusunan konfigurasi volume database dan .dockerignore. 

Room #2 – Dockerisasi, Swagger, GraphQL, dan Troubleshooting Infrastruktur
Rekap Log Prompting
1. Penyatuan dan Penyelesaian Source Code Project
•	Meminta seluruh source code digabung menjadi satu agar lebih mudah digunakan. 
•	Meminta struktur project Laravel Reservasi Booking Service dirapikan. 
•	Meminta pembuatan konfigurasi Docker lengkap untuk project. 
2. Implementasi Dockerisasi Laravel
Dockerfile
•	Membuat Dockerfile untuk Laravel. 
•	Menggunakan PHP 8.3 Apache. 
•	Instalasi dependency: 
o	Composer 
o	PDO MySQL 
o	Zip Extension 
o	Git 
o	Curl 
Docker Compose
Membuat docker-compose.yml dengan service:
•	app (Laravel) 
•	mysql (MySQL 8) 
Volume Persistence
•	Meminta volume agar data database tetap tersimpan setelah container restart. 
•	Menggunakan volume: 
volumes:
  mysql_data:
Docker Ignore
Meminta pembuatan .dockerignore untuk mempercepat proses build image.
Contoh:
vendor
node_modules
.git
storage/logs
3. Troubleshooting Database pada Docker
Error MySQL Host Not Found
Mengalami error:
SQLSTATE[HY000] [2002]
php_network_getaddresses:
getaddrinfo for mysql failed
Analisis:
•	Laravel menggunakan: 
DB_HOST=mysql
•	Saat menjalankan: 
php artisan serve
Laravel berjalan di Windows Host, bukan di Docker Network.
Solusi:
Docker:
DB_HOST=mysql
Host Windows:
DB_HOST=127.0.0.1
DB_PORT=3307
4. GraphQL Playground Error
Mengalami:
{
  "message": "Internal server error"
}
Penyebab:
•	Cache driver database mencoba mengakses MySQL Docker. 
•	Host mysql tidak dapat ditemukan dari lingkungan host. 
Solusi:
•	Menyesuaikan konfigurasi database berdasarkan mode eksekusi: 
o	Docker 
o	php artisan serve 
5. Cache Laravel Bermasalah
Saat menjalankan:
php artisan optimize:clear
Muncul:
cache FAIL
SQLSTATE[HY000] [2002]
Analisis:
•	Cache menggunakan database. 
•	Laravel mencoba mengakses host mysql yang hanya tersedia pada Docker Network. 
Solusi:
•	Jalankan command di dalam container. 
•	Atau ubah konfigurasi DB_HOST sesuai lingkungan. 

6. Pengembangan Swagger/OpenAPI
Dokumentasi endpoint:
GET  /api/v1/reservasis
GET  /api/v1/reservasis/{id}
POST /api/v1/reservasis/{id}/checkin
PUT  /api/v1/reservasis/{id}/status
Menggunakan:
•	L5 Swagger 
•	OpenAPI Attributes PHP 8 
7. Implementasi API Key Authentication
Menambahkan security scheme:
#[OA\SecurityScheme(
    securityScheme: "ApiKeyAuth",
    type: "apiKey",
    in: "header",
    name: "X-IAE-KEY"
)]
Header:
X-IAE-KEY: 102022430001
8. Modifikasi Tampilan Swagger
Mencoba membuat tema:
•	Dark Blue Theme 
•	Custom Header 
•	Custom Footer 
•	Modern Card Style 
Masalah:
•	Sebagian teks hilang karena kontras warna. 
Keputusan:
•	Kembali menggunakan tampilan default L5 Swagger. 
9. Analisis Perbedaan Tampilan Swagger
Perbedaan ditemukan antara:
•	php artisan serve 
•	Docker Apache 
Investigasi:
•	swagger-ui.css 
•	swagger-ui-bundle.js 
•	generated OpenAPI JSON 
Hasil:
•	Asset berhasil dimuat. 
•	OpenAPI valid. 
•	Kemungkinan penyebab: 
o	Cache browser 
o	Asset publish 
o	Perbedaan environment 
10. Troubleshooting Swagger Failed to Fetch
Error:
Failed to fetch
Investigasi:
•	Postman berhasil. 
•	GraphQL berhasil. 
•	Endpoint API berhasil. 
Kesimpulan:
•	Backend tidak bermasalah. 
•	Masalah berada pada Swagger UI/browser. 
11. Pemeriksaan OpenAPI JSON
Memverifikasi:
{
  "title": "Reservasi Booking Service API",
  "version": "1.0.0"
}
Server:
{
  "url": "http://localhost:8000"
}
Security:
{
  "type": "apiKey",
  "name": "X-IAE-KEY"
}
Hasil:
•	Struktur OpenAPI valid. 
•	Endpoint berhasil tergenerate. 
12. Pemeriksaan Struktur Docker
Review:
•	Dockerfile 
•	docker-compose.yml 
•	Volume persistence 
•	Internal Network 
Hasil:
•	Konfigurasi dinilai layak digunakan. 
13. Alur Menjalankan Project
Build:
docker compose build
Menjalankan:
docker compose up -d
Migrasi:
docker exec -it reservasi-booking-service php artisan migrate
Generate Swagger:
docker exec -it reservasi-booking-service php artisan l5-swagger:generate
Ringkasan Hasil Room #2
Fokus utama room ini:
1.	Dockerisasi Laravel Reservasi Booking Service. 
2.	Konfigurasi Dockerfile, Docker Compose, Volume, dan Docker Ignore. 
3.	Penyelesaian error koneksi MySQL antara Docker dan php artisan serve. 
4.	Troubleshooting GraphQL Lighthouse. 
5.	Troubleshooting cache Laravel. 
6.	Pembuatan dan pengujian dokumentasi Swagger/OpenAPI. 
7.	Implementasi API Key Authentication. 
8.	Kustomisasi dan rollback tampilan Swagger. 
9.	Investigasi error Swagger Failed to Fetch. 
10.	Validasi OpenAPI JSON dan konfigurasi Docker secara menyeluruh. 

