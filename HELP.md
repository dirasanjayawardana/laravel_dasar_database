### Database Command

- php artisan db (untuk mengakses terminal database, misal mysql)
- php artisan db:table (untuk melihat seluruh table di database)
- php artisan db:show (untuk melihat informasi database)
- php artisan db:monitor (untuk monitor jumlah koneksi database)
- php artisan db:seed (untuk menambah data di database)
- php artisan db:wipe (untuk menghapus seluruh table di databse)


### Database Migration

Sebelumnya kita membuat tabel secara manual di MySQL. Laravel menyediakan fitur untuk membuat file database.
Database migration digunakan untuk melakukan versioning schema database, melakukan tracking setiap perubahan schema database.
Dengan menggunakan database migration tidak perlu mengubah schema database secara manual lagi.

#### Membuat File Database Migration

`php artisan make:migration`
- Secara otomatis akan dibuatkan file PHP yang digunakan untuk melakukan peruahan schema di databse di folder database/migrations
> untuk membuat perubahan schema, kita bisa menggunakan Schema Builder, tidak perlu menggunakan SQL lagi



