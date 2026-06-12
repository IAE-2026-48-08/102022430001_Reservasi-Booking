PROMPT ENGINEERING LOG BERSAMA AI
Dokumentasi Eksplorasi Mandiri & Troubleshooting Infrastruktur Integrasi Enterprise

Identitas Mahasiswa / Tim:

Nomor Tim: TEAM-11

NIM Mahasiswa: 102022430001

Nama Project: Reservasi-Booking Service

## Room #1 – Integrasi Layer Jamak (SSO M2M, SOAP Audit Pusat, dan API Gateway Message Broker)
1. Kendala Interkoneksi Broker AMQP Terpusat (Silent Error)
Deskripsi Masalah: Saat pengujian melalui Postman dijalankan, sistem lokal berhasil mengembalikan respons sukses. Namun, data log aktivitas milik TEAM-11 tidak terdeteksi  pada visualisasi dasbor Papan Pengumuman RabbitMQ terpusat yang dikelola oleh server dosen.

Analisis Masalah bersama AI: Penelusuran arsitektur menunjukkan adanya diskrepansi protokol komunikasi. Rancangan awal kode menginisiasi koneksi soket langsung (direct socket connection) via protokol AMQP murni (port 5672) menggunakan pustaka PhpAmqpLib\Connection\AMQPStreamConnection. Sebaliknya, berdasarkan dokumen acuan API Tugas IAE, topologi jaringan broker server pusat telah diisolasi dan diamankan di balik API Gateway HTTP pada endpoint POST /api/v1/messages/publish. Mahasiswa tidak diizinkan membuka koneksi langsung ke port AMQP demi menjaga reliabilitas dan stabilitas server.

Solusi & Langkah Refaktorisasi: Melakukan rekonstruksi menyeluruh terhadap modul RabbitMqService.php. Dependensi koneksi soket AMQP murni dieliminasi secara total, kemudian dialihkan menggunakan komponen HTTP Client bawaan framework (Illuminate\Support\Facades\Http). Logika layanan diubah untuk mengirimkan request bermetode POST menuju API Gateway dosen dengan menyematkan parameter Bearer Token yang diperoleh dari hasil jabat tangan (handshake) mekanisme Machine-to-Machine (M2M) SSO IAE.

2. Penolakan Data oleh API Gateway (Validation Error: Message Required)
Deskripsi Masalah: Setelah migrasi jalur komunikasi ke HTTP POST berhasil diimplementasikan, entitas data tim masih belum muncul pada papan pengumuman. Melalui inspeksi berkas log internal kontainer menggunakan instruksi tail -n 50 storage/logs/laravel.log, ditemukan pesan penolakan dari sistem validasi server pusat:

JSON
{"status":"error","message":"message (object or string) is required."}
Analisis Masalah bersama AI: Eror tersebut terjadi karena ketidaksesuaian format struktur payload JSON (payload mismatch). API Gateway pusat menerapkan aturan validasi pembungkus data secara ketat (strict envelope validation). Server eksternal mewajibkan objek data utama dikapsulasi di dalam sebuah properti kunci bernama message. Sementara pada kode kontroler lokal, objek transaksi dikirim secara mentah (raw) tanpa adanya lapisan struktur terluar.

Solusi & Langkah Refaktorisasi: Mengubah pemetaan array data pada berkas RabbitMqService.php dengan menyusun skema enkapsulasi berlapis. Objek $payload dibungkus ke dalam array asosiatif dengan struktur sebagai berikut:

PHP
$wrappedPayload = [
    'routing_key' => $routingKey,
    'message'     => $payload
];
Modifikasi ini secara bersamaan memetakan argumen $routingKey reservasi.checked_in ke dalam atribut routing_key. Hal ini memungkinkan server gateway mengidentifikasi kategori event secara presisi, sehingga sistem dasbor pusat dapat merender indikator visual berupa label hijau terang untuk entitas data TEAM-11.

Ringkasan Hasil Akhir (Room #1)
Fokus utama dan pencapaian pada tahapan troubleshooting tingkat lanjut ini meliputi:

Transformasi Protokol Komunikasi: Berhasil melakukan migrasi arsitektur dari yang sebelumnya berbasis koneksi soket Native AMQP menjadi pengiriman data via Secure HTTP API Gateway Proxying.

Otentikasi Multi-Sistem: Sukses mengintegrasikan mekanisme keamanan Bearer Authentication berbasis token M2M untuk menjamin legalitas sirkulasi data antar-layanan (inter-service).

Standardisasi Format Payload: Menyesuaikan struktur data JSON (envelope payload matching) agar sesuai dengan standar skema validasi yang diterapkan oleh gerbang pesan pusat server dosen.