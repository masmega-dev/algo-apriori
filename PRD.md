# REVISI PRODUCT REQUIREMENTS DOCUMENT

## Sistem Pemesanan Kue Ulang Tahun Publik dan Analisis Apriori

**Versi revisi:** 1.1
**Perubahan utama:** Pemesanan dilakukan langsung oleh pelanggan melalui halaman publik. Hanya admin yang wajib login.

---

# 1. Ringkasan Perubahan

Aplikasi memiliki dua area:

## 1.1 Public Area

Dapat diakses tanpa login oleh pelanggan.

Fitur:

- Melihat informasi singkat toko.
- Membuat pesanan kue.
- Memilih spesifikasi kue.
- Memilih item tambahan.
- Mengunggah foto referensi.
- Melihat ringkasan harga.
- Mengirim pesanan.
- Melihat invoice setelah pesanan berhasil dibuat.
- Membuka kembali invoice melalui tautan unik.

## 1.2 Admin Area

Hanya dapat diakses setelah login.

Fitur:

- Dashboard.
- Daftar order.
- Detail order.
- Menandai pesanan selesai.
- Menghapus pesanan.
- Mengirim ulang WhatsApp.
- Mengelola master data.
- Melihat laporan operasional.
- Menjalankan analisis Apriori.

---

# 2. Alur Sistem Utama

```text
Pelanggan membuka halaman order publik
        ↓
Pelanggan mengisi data diri dan detail pesanan
        ↓
Sistem memvalidasi dan menghitung total pesanan
        ↓
Sistem menyimpan pesanan
        ↓
Sistem membuat nomor order dan token invoice publik
        ↓
Pelanggan diarahkan ke halaman invoice
        ↓
Notifikasi WhatsApp dikirim ke pelanggan
        ↓
Notifikasi WhatsApp dikirim ke admin toko
        ↓
Pesanan masuk ke daftar order admin
        ↓
Admin memproses pesanan
        ↓
Admin mengubah status menjadi selesai
        ↓
Transaksi selesai digunakan dalam analisis Apriori
```

Kegagalan WhatsApp tidak boleh membatalkan penyimpanan order.

---

# 3. Aktor Sistem

## 3.1 Pelanggan

Pelanggan tidak memiliki akun dan tidak perlu login.

Pelanggan dapat:

- Membuka halaman pemesanan.
- Mengisi data pemesan.
- Memilih detail kue.
- Memilih item tambahan.
- Mengunggah foto referensi.
- Melihat perhitungan biaya.
- Mengirim pesanan.
- Melihat invoice.
- Membuka kembali invoice melalui tautan unik.

Pelanggan tidak dapat:

- Melihat pesanan pelanggan lain.
- Mengedit pesanan setelah dikirim.
- Menghapus pesanan.
- Mengubah status pesanan.
- Mengakses dashboard atau laporan.

## 3.2 Admin

Admin wajib login.

Admin dapat:

- Melihat seluruh order.
- Melihat detail order.
- Mencari dan memfilter order.
- Mengubah status order.
- Menghapus order dengan soft delete.
- Mengirim ulang WhatsApp.
- Melihat laporan.
- Menjalankan Apriori.
- Mengelola master data.
- Mengatur informasi toko dan template WhatsApp.

---

# 4. Struktur Halaman Publik

## 4.1 Landing dan Order

Untuk MVP, landing page dan form order dapat dibuat dalam satu halaman.

Struktur:

1. Header sederhana.
2. Informasi toko.
3. Form data pemesan.
4. Form detail kue.
5. Pilihan item tambahan.
6. Metode penerimaan.
7. Ringkasan harga.
8. Tombol Buat Pesanan.
9. Informasi kontak toko.

Pelanggan tidak perlu melihat sidebar atau dashboard.

## 4.2 Invoice Publik

Setelah order berhasil dibuat, pelanggan diarahkan ke:

```text
/order-success/{public_token}
```

atau:

```text
/invoice/{public_token}
```

URL tidak boleh menggunakan ID database berurutan.

Contoh yang tidak aman:

```text
/invoice/1
/invoice/2
/invoice/3
```

Contoh yang digunakan:

```text
/invoice/01JY9QTX4EDJ7M6E8JRA2F4DGH
```

Token harus acak dan sulit ditebak.

## 4.3 Halaman Pesanan Berhasil

Halaman menampilkan:

- Pesan bahwa order berhasil dibuat.
- Nomor order.
- Nama pemesan.
- Jadwal pengambilan.
- Status pesanan.
- Ringkasan item.
- Total pembayaran.
- Tombol cetak invoice.
- Tombol hubungi toko.
- Informasi bahwa konfirmasi WhatsApp sedang dikirim.

---

# 5. Struktur Navigasi Admin

Menu utama admin:

1. Dashboard.
2. Daftar Order.
3. Laporan.
4. Pengaturan.

Menu Buat Order tidak lagi menjadi menu utama karena order dibuat oleh pelanggan.

Namun admin tetap dapat memiliki tombol:

> Tambah Order Manual

Fitur ini bersifat opsional untuk order yang masuk melalui telepon atau WhatsApp.

Jika fitur order manual belum masuk MVP, admin hanya mengelola order dari halaman publik.

---

# 6. Autentikasi dan Hak Akses

## 6.1 Public Route

Route publik:

```text
GET  /
GET  /order
POST /order
GET  /invoice/{publicToken}
```

Tidak membutuhkan login.

## 6.2 Admin Route

Route admin menggunakan middleware:

```text
auth
verified
```

Contoh:

```text
/admin/dashboard
/admin/orders
/admin/orders/{order}
/admin/reports
/admin/settings
```

## 6.3 Authorization

Gunakan Laravel Policy untuk:

- Melihat detail order admin.
- Mengubah status order.
- Menghapus order.
- Mengakses laporan.
- Mengelola pengaturan.

Public invoice tidak menggunakan Policy berbasis user, tetapi menggunakan token publik yang aman.

---

# 7. Form Order Publik

## 7.1 Data Pemesan

Field:

- Nama pemesan.
- Nomor WhatsApp.
- Tanggal order otomatis.
- Nomor order otomatis setelah tersimpan.

Validasi:

- Nama wajib.
- Nomor WhatsApp wajib.
- Nomor WhatsApp harus valid.
- Nomor dinormalisasi ke format internasional.

Contoh:

```text
081234567890
```

Disimpan sebagai:

```text
6281234567890
```

## 7.2 Jadwal Pengambilan

Field:

- Tanggal pengambilan.
- Jam pengambilan.
- Metode penerimaan.
- Alamat.
- Catatan lokasi.

Pilihan metode:

- Ambil di toko.
- Delivery.
- COD.

Aturan:

- Tanggal pengambilan tidak boleh sebelum hari ini.
- Jam pengambilan wajib.
- Alamat wajib untuk Delivery dan COD.
- Alamat disembunyikan jika pelanggan memilih Ambil di Toko.

## 7.3 Detail Kue

Field:

- Nama atau tulisan pada kue.
- Umur.
- Ukuran.
- Bentuk.
- Rasa dasar.
- Rasa isian.
- Warna dasar.
- Warna hiasan.
- Tema atau karakter.
- Foto referensi.
- Catatan dekorasi.
- Harga kue.

Harga kue dapat ditentukan dengan salah satu pendekatan:

### Pendekatan A — Harga otomatis

Harga berasal dari ukuran atau varian kue.

Contoh:

```text
20 cm = Rp150.000
22 cm = Rp180.000
25 cm = Rp220.000
```

### Pendekatan B — Harga dikonfirmasi admin

Form pelanggan tidak menampilkan harga final dan admin mengonfirmasi harga setelah melihat pesanan.

Untuk sistem yang ingin langsung membuat invoice, pendekatan A lebih sesuai.

## 7.4 Item Tambahan

Pelanggan dapat memilih:

- Pisau.
- Piring.
- Lilin.
- Balon.
- Topper.
- Item lain yang aktif.

Setiap item menampilkan:

- Nama.
- Harga.
- Satuan.
- Quantity.
- Subtotal.

Contoh:

```text
☑ Pisau       Rp2.000 × 1
☑ Piring      Rp500 × 10
☑ Lilin       Rp1.000 × 2
```

## 7.5 Ringkasan Pesanan

Ringkasan harga dibuat sticky pada desktop dan tampil sebelum tombol submit pada mobile.

Informasi:

- Harga kue.
- Total item tambahan.
- Biaya pengiriman.
- Total pembayaran.

Frontend hanya menampilkan estimasi.

Backend wajib menghitung ulang seluruh harga berdasarkan database.

## 7.6 Submit Order

Tombol:

> Buat Pesanan

Saat diklik:

1. Tombol masuk loading state.
2. Tombol dinonaktifkan untuk mencegah double submit.
3. Backend memvalidasi request.
4. Backend menghitung total.
5. Order disimpan dalam database transaction.
6. Nomor order dibuat.
7. Token invoice publik dibuat.
8. Notifikasi WhatsApp dimasukkan ke queue.
9. Pelanggan diarahkan ke halaman invoice.

---

# 8. Status Order

Gunakan enum:

```text
Pending
Completed
Cancelled
```

## 8.1 Pending

Order baru dari pelanggan secara otomatis berstatus Pending.

Arti:

- Pesanan sudah masuk.
- Belum selesai dikerjakan.

## 8.2 Completed

Admin menandai pesanan selesai.

Data yang disimpan:

```text
completed_at
completed_by
```

## 8.3 Cancelled

Status internal untuk pesanan yang dibatalkan.

Transaksi Cancelled tidak dihitung dalam laporan omzet dan Apriori.

Pada MVP, tombol utama tetap:

- Detail.
- Done.
- Hapus.

Status Cancelled dapat disiapkan dalam backend untuk pengembangan berikutnya.

---

# 9. Invoice Publik

Invoice menampilkan:

- Logo toko.
- Nama toko.
- Nomor order.
- Tanggal order.
- Nama pemesan.
- Nomor WhatsApp yang dimasking.
- Jadwal pengambilan.
- Metode penerimaan.
- Alamat jika delivery.
- Spesifikasi kue.
- Foto referensi.
- Item tambahan.
- Harga kue.
- Biaya tambahan.
- Biaya pengiriman.
- Grand total.
- Status pesanan.
- Catatan.

Nomor WhatsApp pada halaman publik sebaiknya dimasking:

```text
0812****7890
```

Tombol:

- Cetak Invoice.
- Hubungi Toko.
- Kembali ke Beranda.

Pelanggan tidak dapat mengedit invoice dari halaman tersebut.

---

# 10. WhatsApp Gateway

## 10.1 Penerima

Setiap order baru menghasilkan dua notifikasi:

1. Notifikasi kepada pelanggan.
2. Notifikasi kepada admin toko.

## 10.2 Pesan Pelanggan

Template:

```text
Halo {customer_name},

Pesanan kue Anda berhasil dibuat.

Nomor Order: {order_number}
Tanggal Pengambilan: {pickup_date}
Jam Pengambilan: {pickup_time}
Metode: {fulfillment_method}
Total: {grand_total}

Invoice:
{invoice_url}

Pesanan akan diproses oleh admin toko.

Terima kasih.
```

## 10.3 Pesan Admin

Template:

```text
Pesanan baru telah masuk.

Nomor Order: {order_number}
Pemesan: {customer_name}
Nomor WhatsApp: {customer_phone}
Tanggal Pengambilan: {pickup_date}
Jam Pengambilan: {pickup_time}
Metode: {fulfillment_method}
Total: {grand_total}

Detail Admin:
{admin_order_url}
```

## 10.4 Arsitektur Queue

Alur:

```text
Order berhasil dibuat
        ↓
Dispatch notifikasi pelanggan
        ↓
Dispatch notifikasi admin
        ↓
Queue worker mengirim ke WhatsApp Gateway
        ↓
Response disimpan dalam whatsapp_logs
```

Lebih baik satu log mewakili satu penerima.

Contoh:

```text
Order ORD-001
├── Customer notification
└── Admin notification
```

## 10.5 Status Pengiriman

Status:

```text
Pending
Sent
Failed
```

Status pelanggan dan admin tidak boleh digabung karena salah satunya dapat berhasil sementara yang lain gagal.

## 10.6 Retry

Retry maksimal tiga kali.

Backoff:

```text
60 detik
300 detik
900 detik
```

Order tetap berhasil meskipun semua pengiriman WhatsApp gagal.

---

# 11. Daftar Order Admin

Daftar order menampilkan order yang dibuat melalui halaman publik.

Kolom:

- Nomor order.
- Nama pemesan.
- Nomor WhatsApp.
- Tanggal order.
- Jadwal pengambilan.
- Total.
- Metode penerimaan.
- Status order.
- Status WA pelanggan.
- Status WA admin.
- Aksi.

## 11.1 Filter

- Rentang tanggal order.
- Rentang tanggal pengambilan.
- Status.
- Metode penerimaan.
- Status WhatsApp.

Filter tanggal utama perlu dibedakan:

### Tanggal Order

Kapan pelanggan membuat pesanan.

### Tanggal Pengambilan

Kapan kue harus disiapkan.

Untuk operasional toko, tanggal pengambilan biasanya lebih penting.

## 11.2 Search

Search berdasarkan:

- Nomor order.
- Nama pelanggan.
- Nomor WhatsApp.

## 11.3 Aksi

### Detail

Melihat detail lengkap order.

### Done

Mengubah status menjadi Completed.

### Hapus

Menggunakan soft delete.

### Kirim Ulang WhatsApp

Admin dapat memilih:

- Kirim ke pelanggan.
- Kirim ke admin.
- Kirim ke keduanya.

---

# 12. Ringkasan Order

Ringkasan mengikuti filter yang aktif.

Kartu:

- Total Order.
- Sudah Dikerjakan.
- Belum Selesai.

Contoh:

```text
Total Order         25
Sudah Dikerjakan    18
Belum Selesai        7
```

Jika filter menggunakan tanggal pengambilan, seluruh kartu menghitung berdasarkan tanggal pengambilan.

---

# 13. Dashboard Admin

Dashboard menampilkan:

- Order masuk hari ini.
- Pesanan yang harus diambil hari ini.
- Pesanan selesai hari ini.
- Pesanan belum selesai.
- Total omzet.
- Item tambahan terpopuler.
- Grafik order.
- Order terbaru.
- Order terdekat yang harus diselesaikan.

Dashboard hanya dapat diakses admin.

---

# 14. Perubahan Struktur Database

## 14.1 orders

Tambahkan field:

```text
public_token
source
```

Struktur relevan:

```text
id
public_token
order_number
customer_id
source
order_date
pickup_date
pickup_time
fulfillment_method
delivery_address
delivery_fee
cake_size_id
cake_shape_id
base_flavor_id
filling_flavor_id
cake_text
age_text
base_color
decoration_color
character_theme
reference_image_path
cake_price
additional_items_total
grand_total
status
notes
completed_at
completed_by
created_by
created_at
updated_at
deleted_at
```

Nilai `source`:

```text
public
admin
```

Untuk order publik:

```text
created_by = null
source = public
```

Untuk order manual admin pada fase berikutnya:

```text
created_by = user_id
source = admin
```

## 14.2 public_token

Ketentuan:

- Unik.
- Tidak berurutan.
- Tidak berasal dari ID database.
- Menggunakan UUID, ULID, atau random string yang aman.
- Memiliki unique index.

## 14.3 whatsapp_logs

Revisi:

```text
id
order_id
recipient_type
recipient_name
phone
provider
message
status
provider_message_id
provider_response
error_message
attempt_count
sent_at
created_at
updated_at
```

Nilai `recipient_type`:

```text
customer
admin
```

## 14.4 settings

Tambahkan:

```text
admin_whatsapp_number
customer_order_message_template
admin_new_order_message_template
public_order_enabled
minimum_pickup_days
store_open_time
store_close_time
```

---

# 15. Keamanan Halaman Publik

Karena form dapat diakses tanpa login, perlindungan berikut wajib diterapkan.

## 15.1 Rate Limiting

Batasi submit berdasarkan IP dan nomor WhatsApp.

Contoh:

```text
Maksimal 5 order per IP dalam 10 menit.
```

Batas aktual dapat disesuaikan.

## 15.2 Anti Double Submit

Gunakan:

- Loading state.
- Disabled button.
- Idempotency token atau submission token.
- Database protection jika request yang sama terkirim ulang.

## 15.3 Honeypot

Tambahkan field tersembunyi untuk mendeteksi bot sederhana.

## 15.4 CAPTCHA

CAPTCHA tidak harus langsung digunakan pada MVP.

Aktifkan apabila mulai terjadi spam.

Gunakan penyedia yang tidak terlalu mengganggu UX seperti Cloudflare Turnstile.

## 15.5 Upload Security

- Validasi MIME.
- Validasi ekstensi.
- Maksimum 5 MB.
- Nama file diacak.
- File tidak boleh dapat dieksekusi.
- Simpan pada storage khusus upload.
- Jangan menggunakan nama asli sebagai nama file final.

## 15.6 Harga

Frontend tidak boleh menjadi sumber harga final.

Backend mengambil harga dari:

- Master ukuran kue.
- Master item tambahan.
- Pengaturan delivery.

Payload harga dari browser tidak dipercaya.

## 15.7 Invoice Access

Invoice publik hanya dapat diakses menggunakan `public_token`.

Jangan menampilkan data sensitif secara berlebihan.

Nomor WhatsApp dimasking pada tampilan invoice.

---

# 16. Analisis Apriori

Tidak ada perubahan besar pada rumus Apriori.

Data dianalisis dari order yang:

- Berstatus Completed.
- Tidak terhapus.
- Tidak Cancelled.
- Memiliki minimal satu item tambahan.
- Berada dalam periode analisis.

Satu order pelanggan menjadi satu transaksi Apriori.

Contoh:

```text
ORD-001 = [Pisau, Lilin, Piring]
ORD-002 = [Pisau, Piring]
ORD-003 = [Lilin, Balon]
```

Quantity tidak dihitung sebagai kemunculan berulang.

Contoh:

```text
Piring quantity 20
```

Dalam Apriori tetap dianggap:

```text
Piring = ada
```

Bukan dua puluh transaksi.

---

# 17. Struktur Frontend

Pisahkan halaman public dan admin.

```text
resources/js/
├── components/
│   ├── public/
│   ├── admin/
│   ├── orders/
│   ├── reports/
│   ├── forms/
│   └── ui/
├── layouts/
│   ├── PublicLayout.tsx
│   ├── AdminLayout.tsx
│   └── AuthLayout.tsx
├── pages/
│   ├── public/
│   │   ├── Home.tsx
│   │   ├── CreateOrder.tsx
│   │   ├── OrderSuccess.tsx
│   │   └── PublicInvoice.tsx
│   ├── admin/
│   │   ├── Dashboard.tsx
│   │   ├── orders/
│   │   ├── reports/
│   │   └── settings/
│   └── auth/
├── hooks/
├── types/
└── utils/
```

---

# 18. Reusable Components Publik

Komponen:

```text
PublicHeader
StoreInfoSection
CustomerDataSection
CakeSpecificationSection
AdditionalItemSelector
FulfillmentSection
OrderPriceSummary
PublicOrderForm
OrderSuccessCard
PublicInvoice
WhatsAppStatusNotice
```

Form pelanggan tetap menggunakan komponen yang sama apabila nanti admin memiliki fitur tambah order manual.

Perbedaannya hanya pada wrapper dan authorization.

Contoh:

```text
<OrderForm mode="public" />
```

atau:

```text
<OrderForm mode="admin" />
```

Business logic form tidak boleh ditulis dua kali.

---

# 19. UI/UX Public Order

Tema tetap menggunakan biru cerah, putih, dan slate.

Palet utama:

```text
Primary Blue    #2563EB
Soft Blue       #EFF6FF
Sky Accent      #38BDF8
White           #FFFFFF
Slate Surface   #F8FAFC
Dark Text       #0F172A
Success         #10B981
Warning         #F59E0B
Danger          #EF4444
```

## Prinsip desain

- Form dibuat bertahap secara visual, tetapi tetap satu halaman.
- Tidak menggunakan wizard yang memaksa terlalu banyak klik.
- Setiap kelompok field berada dalam card.
- Ringkasan harga selalu terlihat.
- Tombol utama hanya satu: Buat Pesanan.
- Mobile-first.
- Tidak meminta pelanggan membuat akun.
- Tidak meminta data yang tidak diperlukan.
- Kesalahan field tampil langsung di bawah input.
- Foto dapat dipreview sebelum submit.
- Item tambahan tampil dalam card yang mudah dipilih.

## Struktur desktop

```text
Kiri:
- Data pemesan
- Detail kue
- Item tambahan
- Pengiriman

Kanan:
- Ringkasan pesanan sticky
```

## Struktur mobile

```text
Data pemesan
Detail kue
Item tambahan
Pengiriman
Ringkasan harga
Tombol Buat Pesanan
```

---

# 20. Acceptance Criteria Revisi

## Public Order

- Pelanggan dapat membuka form tanpa login.
- Pelanggan dapat membuat pesanan.
- Harga dihitung ulang di backend.
- Pesanan tersimpan dengan status Pending.
- Nomor order unik dibuat otomatis.
- Token invoice publik dibuat otomatis.
- Pelanggan diarahkan ke invoice.
- Job WhatsApp pelanggan dijalankan.
- Job WhatsApp admin dijalankan.
- Double submit dapat dicegah.

## Invoice Publik

- Invoice hanya dapat dibuka melalui token valid.
- URL tidak menggunakan ID database.
- Invoice menampilkan seluruh detail pesanan.
- Nomor WhatsApp dimasking.
- Invoice dapat dicetak.
- Pelanggan tidak dapat mengedit pesanan.

## Admin

- Halaman admin hanya dapat diakses setelah login.
- Order publik muncul di daftar order.
- Admin dapat melihat detail.
- Admin dapat menyelesaikan order.
- Admin dapat menghapus order.
- Admin dapat mengirim ulang notifikasi.

## WhatsApp

- Pelanggan menerima notifikasi order.
- Admin menerima notifikasi order baru.
- Status setiap penerima disimpan secara terpisah.
- Kegagalan gateway tidak menggagalkan order.
- Retry berjalan sesuai konfigurasi.

## Apriori

- Hanya order Completed yang dihitung secara default.
- Order Cancelled dan terhapus tidak dihitung.
- Basket dibentuk dari item tambahan.
- Support, confidence, dan lift dihitung dengan benar.
- Hasil mengikuti periode filter.

---

# 21. Scope MVP Final

## Public

1. Landing page sederhana.
2. Form order publik.
3. Data pemesan.
4. Detail kue.
5. Item tambahan.
6. Upload foto referensi.
7. Kalkulasi harga.
8. Submit order.
9. Invoice publik.
10. Cetak invoice.
11. Notifikasi WhatsApp pelanggan.
12. Notifikasi WhatsApp admin.

## Admin

1. Login.
2. Dashboard.
3. Daftar order.
4. Filter dan search.
5. Detail order.
6. Menandai order selesai.
7. Soft delete.
8. Kirim ulang WhatsApp.
9. Master data.
10. Laporan operasional.
11. Grafik.
12. Analisis Apriori.
13. Interpretasi hasil.
14. Export laporan.

---

# 22. Keputusan Arsitektur Final

- Pelanggan tidak memiliki akun.
- Auth hanya digunakan untuk admin.
- Order dibuat dari halaman publik.
- Invoice publik diakses menggunakan token acak.
- Nomor order tetap dibuat dalam format mudah dibaca.
- Notifikasi dikirim kepada admin dan pelanggan.
- WhatsApp berjalan melalui queue.
- Harga final selalu dihitung backend.
- Order baru berstatus Pending.
- Order Completed digunakan untuk Apriori.
- Empat menu utama admin adalah Dashboard, Daftar Order, Laporan, dan Pengaturan.
- Form order dibuat reusable agar dapat digunakan kembali untuk order manual admin pada fase berikutnya.
