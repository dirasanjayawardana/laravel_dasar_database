## Database Command
- php artisan db (untuk mengakses terminal database, misal mysql)
- php artisan db:table (untuk melihat seluruh table di database)
- php artisan db:show (untuk melihat informasi database)
- php artisan db:monitor (untuk monitor jumlah koneksi database)
- php artisan db:seed (untuk menambah data di database)
- php artisan db:wipe (untuk menghapus seluruh table di databse)


## Database Migration
- Sebelumnya kita membuat tabel secara manual di MySQL. Laravel menyediakan fitur untuk membuat table di database.
- Database migration digunakan untuk membuat table di database, melakukan versioning schema database, melakukan tracking setiap perubahan schema database.
- Dengan menggunakan database migration tidak perlu mengubah schema database secara manual lagi.
- Ketika ada perubahan pada table, harus membuat file migration baru


### Membuat File Database Migration
`php artisan make:migration nama_file_migration`
- Secara otomatis akan dibuatkan file PHP yang digunakan untuk melakukan peruahan schema di databse di folder database/migrations
- Untuk membuat perubahan schema, kita bisa menggunakan Schema Builder, tidak perlu menggunakan SQL lagi
- Dengan menggunakan Schema Builder tidak perlu khawatir akan menggunakan database apa, akan otomatis disesuaikan
`php artisan make:migration create_table_counter`
`php artisan make:migration create_table_category`
`php artisan make:migration create_table_product`

### Menjalankan Migration
`php artisan migrate`

### Melihat Status Migration
`php artisan migrate:status`

### Rollback Database Migration
`php artisan migrate::rollack --step=jumlah`
- Membatalkan(rollback) migration, dimana rollback dilakukan mulai dari migration file terakhir yang sukses, ke file migration sebelumnya secara bertahap sesuai dengan jumlah yang diinginkan.
- Setelah Rollback jika ingin menjalankan file migrationnya lagi bisa dilakukan
- Akan menjalankan method down() pada file migration
`php artisan migrate:rollback --step=1`
 
 
## Seeding
- Melakukan perubahan (insert, update, delete) data di database
- Laravel memiliki cara untuk melakukan seeding

### Membuat Seeding
`php artisan make::seeder NamaSeeder`
- Secara otomatis akan dibuatkan file di folder database/seeders
- di file itu bisa menambahkan kode untuk memanipulasi data di database

### Menjalankan Seeder
`php artisan db:seed --class=ClassSeeder`

