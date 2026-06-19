# ANALISIS TUGAS 3
## Reservasi Booking Service
### NIM : 102022430001

# 1. Deskripsi Service

Service yang saya kembangkan adalah Reservasi Booking Service.

Pada Tugas 3 kali ini, saya mengintegrasikan service reservasi dengan layanan terpusat yang sudah disediakan oleh dosen, yaitu:

1. Federated SSO (JWT Authentication)
2. SOAP Audit Service
3. RabbitMQ Message Broker

Integrasinya dilakukan pada proses Check-In Reservasi karena proses tersebut merupakan transaksi yang penting dan memiliki dampak pada sistem lain.


# 2. Analisis Transaksi Penting

## Transaksi yang Dipilih

Transaksi yang digunakan:

**Check-In Reservasi**

Endpoint:

http://localhost:8000/api/v1/reservasis/{id}/checkin

Contoh:

http://localhost:8000/api/v1/reservasis/1/checkin

http://localhost:8000/api/v1/reservasis/2/checkin

http://localhost:8000/api/v1/reservasis/3/checkin


## Alasan Transaksi Dinilai Penting

check-in merupakan tahap yang mengubah status reservasi menjadi aktif.

Sebelum check-in:

```text
Status = confirmed
```

Sesudah check-in:

```text
Status = checked_in
```

Perubahan status yang terjadi dapat mempengaruhi berbagai aktivitas operasional sehingga perlu dicatat dan disebarkan ke sistem lain.


## Mengapa Menggunakan SOAP

SOAP digunakan untuk melakukan pencatatan audit terhadap transaksi penting.

Saat check-in berhasil, sistem akan mengirimkan informasi audit ke layanan SOAP terpusat yang disediakan oleh dosen.

Data yang dikirim antara lain:

```json
{
  "reservasi_id": 1,
  "status": "checked_in",
  "action": "CustomerCheckin"
}
```

Tujuan penggunaan SOAP:

- Menyimpan jejak audit transaksi penting
- Menjamin adanya bukti transaksi
- Menghasilkan Receipt Number sebagai bukti pencatatan

Contoh Receipt Number:IAE-LOG-2026-BECAEA36


## Mengapa Menggunakan RabbitMQ

Setelah transaksi berhasil dicatat melalui SOAP, informasi tersebut perlu disebarkan ke sistem lain dan itu memerlukan RabbitMQ.RabbitMQ memungkinkan sistem lain menerima notifikasi tanpa harus melakukan request langsung ke Reservasi Booking Service.

Event yang dipublikasikan:

```json
{
  "event_name": "reservasi.checked_in",
  "service_name": "Reservasi-Service",
  "api_version": "v1",
  "occurred_at": "2026-06-19T16:00:00+00:00",
  "reservasi_id": 1,
  "booking_code": "BK001",
  "guest_name": "Budi",
  "room_type": "Deluxe",
  "legacy_receipt_number": "IAE-LOG-2026-BECAEA36",
  "nim": "102022430001",
  "approved_by": {
    "sso_subject": "warga14@ktp.iae.id",
    "roles": [
      "warga"
    ]
  }
}
```

Manfaat RabbitMQ:

- Mendukung arsitektur event-driven
- Mengurangi ketergantungan antar service
- Memungkinkan integrasi dengan service lain secara asynchronous


# 3. Analisis Federated SSO

Sebelum mengakses layanan SOAP dan RabbitMQ, sistem harus memperoleh token dari layanan SSO.

Proses yang dilakukan:

## M2M Token

Digunakan untuk autentikasi antar aplikasi.

Request:

```json
{
  "api_key": "KEY-MHS-37",
  "nim": "102022430001"
}
```

Response:

```json
{
  "token_type": "m2m",
  "token": "JWT_TOKEN"
}
```

---

## User Token

Digunakan untuk memperoleh identitas pengguna yang menyetujui transaksi.

Request:

```json
{
  "email": "warga14@ktp.iae.id",
  "password": "KtpDigital2026!"
}
```

Response:

```json
{
  "token_type": "user",
  "profile": {
    "name": "Nadia Putri Rahayu",
    "nim": "2026000014",
    "email": "warga14@ktp.iae.id"
  }
}
```


# 4. Kesimpulan

Integrasi meliputi:

1. Federated SSO untuk autentikasi dan identitas pengguna.
2. SOAP XML Client untuk pencatatan audit transaksi penting.
3. RabbitMQ Publisher untuk penyebaran event antar service.

Transaksi Check-In dipilih karena merupakan transaksi penting yang mengubah status reservasi dan memerlukan pencatatan audit serta distribusi informasi ke sistem lain.

Hasil yang didapatkan:

- M2M Token berhasil diperoleh.
- User Token berhasil diperoleh.
- Audit SOAP berhasil menghasilkan Receipt Number.
- Event berhasil dipublikasikan ke RabbitMQ.
