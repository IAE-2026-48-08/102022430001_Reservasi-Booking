**Room #1 – Pengembangan Service Reservasi & Booking (Laravel REST API, Swagger, GraphQL, Docker)**

1.	Meminta penjelasan langkah-langkah pengerjaan Service 1 – Reservasi & Booking berdasarkan dokumen tugas dan template project Laravel yang telah dibuat

2.	Menunjukkan controller ReservasiController yang telah dibuat dan meminta pengecekan apakah masih ada yang perlu disesuaikan

3.	Mengalami error Postman Invalid URI http:///api/v1/reservasis

4.	Endpoint resource, check-in, dan update status mengembalikan pesan "Reservasi not found"

5.	Menginformasikan bahwa pengujian Postman sudah berhasil dan meminta langkah selanjutnya

6.	Menampilkan kode controller yang sudah ditambahkan anotasi Swagger dan meminta validasi

7.	Mengalami kendala saat mengakses GraphQL Playground yang menampilkan halaman "Not Found"

8.	Menampilkan error GraphQL terkait parameter query yang tidak ditemukan

9.	Berhasil menjalankan query GraphQL dan memperoleh data reservasi

10.	Menampilkan hasil dokumentasi Swagger yang berhasil terbuka namun mengembalikan error 401 Unauthorized

11.	Meminta pembuatan konfigurasi Docker lengkap beserta .dockerignore dan volume database agar data tetap tersimpan

**Ringkasan Hasil Akhir**

Selama sesi ini berhasil diselesaikan beberapa komponen utama pada Service Reservasi & Booking:

    1.	Implementasi REST API menggunakan Laravel. 
    
    2.	Pembuatan endpoint Collection, Resource, Action, dan Update. 
    
    3.	Implementasi middleware API Key untuk keamanan endpoint. 
    
    4.	Pengujian endpoint menggunakan Postman. 
    
    5.	Dokumentasi API menggunakan Swagger/OpenAPI. 
    
    6.	Implementasi GraphQL menggunakan Lighthouse. 
    
    7.	Pengujian query GraphQL terhadap data reservasi. 
    
    8.	Penyusunan konfigurasi Docker dan Docker Compose. 
    
    9.	Penyusunan konfigurasi volume database dan .dockerignore. 

**Room #2 – Dockerisasi, Swagger, GraphQL, dan Troubleshooting Infrastruktur**

**1. Penyatuan dan Penyelesaian Source Code Project**

    •	Meminta seluruh source code digabung menjadi satu agar lebih mudah digunakan.
   
    •	Meminta struktur project Laravel Reservasi Booking Service dirapikan.
   
    •	Meminta pembuatan konfigurasi Docker lengkap untuk project. 

**2. Implementasi Dockerisasi Laravel**

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
    
    •   Menggunakan volume: 

            volumes:
            
              mysql_data:
          
Docker Ignore

Meminta pembuatan .dockerignore untuk mempercepat proses build image Contoh:

    •    vendor
    
    •    node_modules
    
    •    .git
    
    •    storage/logs

**3. Troubleshooting Database pada Docker**

Error MySQL Host Not Found

Mengalami error:SQLSTATE[HY000] [2002]

php_network_getaddresses:getaddrinfo for mysql failed

Analisis:

    •	Laravel menggunakan: DB_HOST=mysql

    •	Saat menjalankan: php artisan serve Laravel berjalan di Windows Host, bukan di Docker Network.

Solusi:

Docker:DB_HOST=mysql

Host Windows:DB_HOST=127.0.0.1 DB_PORT=3307

**4. GraphQL Playground Error**

Mengalami:{"message": "Internal server error"}

Penyebab:

    •	Cache driver database mencoba mengakses MySQL Docker. 
    
    •	Host mysql tidak dapat ditemukan dari lingkungan host. 

Solusi:

    •	Menyesuaikan konfigurasi database berdasarkan mode eksekusi: 
    
        o	Docker 
        
        o	php artisan serve 

**5. Cache Laravel Bermasalah**

Saat menjalankan:php artisan optimize:clear

Muncul:cache FAIL SQLSTATE[HY000] [2002]

Analisis:

    •	Cache menggunakan database. 
    
    •	Laravel mencoba mengakses host mysql yang hanya tersedia pada Docker Network. 

Solusi:

    •	Jalankan command di dalam container. 
    
    •	Atau ubah konfigurasi DB_HOST sesuai lingkungan. 

**6. Pengembangan Swagger/OpenAPI**

Dokumentasi endpoint:

    GET  /api/v1/reservasis
    
    GET  /api/v1/reservasis/{id}
    
    POST /api/v1/reservasis/{id}/checkin
    
    PUT  /api/v1/reservasis/{id}/status

Menggunakan:

    •	L5 Swagger 
    
    •	OpenAPI Attributes PHP 8 

**7. Implementasi API Key Authentication**

Menambahkan security scheme:

    #[OA\SecurityScheme(

    securityScheme: "ApiKeyAuth",
    
    type: "apiKey",
    
    in: "header",
    
    name: "X-IAE-KEY"
    
    )]

Header:X-IAE-KEY: 102022430001

**8. Modifikasi Tampilan Swagger**

Mencoba membuat tema:

    •	Dark Blue Theme 
    
    •	Custom Header 
    
    •	Custom Footer 
    
    •	Modern Card Style 

Masalah:

    •	Sebagian teks hilang karena kontras warna. 

Keputusan:

    •	Kembali menggunakan tampilan default L5 Swagger. 

**9. Troubleshooting Swagger Failed to Fetch**

Error:Failed to fetch

Investigasi:

    •	Postman berhasil. 
    
    •	GraphQL berhasil. 
    
    •	Endpoint API berhasil. 

Kesimpulan:

    •	Backend tidak bermasalah. 
    
    •	Masalah berada pada Swagger UI/browser. 

**10. Pemeriksaan OpenAPI JSON**

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

**11. Pemeriksaan Struktur Docker**

Review:

    •	Dockerfile 
    
    •	docker-compose.yml 
    
    •	Volume persistence 
    
    •	Internal Network 

Hasil:Konfigurasi dinilai layak digunakan. 

**12. Alur Menjalankan Project**

Build:docker compose build

Menjalankan:docker compose up -d

Migrasi:docker exec -it reservasi-booking-service php artisan migrate

Generate Swagger:docker exec -it reservasi-booking-service php artisan l5-swagger:generate

**Ringkasan Hasil Room #2**

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
