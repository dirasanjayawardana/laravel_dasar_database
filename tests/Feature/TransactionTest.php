<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TransactionTest extends TestCase
{

    // DB::Transaction(function, jumlahPercobaan/attempts) --> attempts defaultnya 1 artinya function akan dijalankan 1 kali, perintah database dilakukan didalam function, jika terjadi error maka secara otomatis transaksi akan di rollback/pembatalan, jika tidak ada error maka transaksi akan di commit/simpan perubahan.

    // Commit adalah perintah untuk menyelesaikan transaksi dan menyimpan semua perubahan yang telah dilakukan selama transaksi tersebut ke dalam database secara permanen. Setelah commit dijalankan, perubahan tidak bisa dibatalkan.

    // Rollback adalah perintah untuk membatalkan semua perubahan yang telah dilakukan selama transaksi dan mengembalikan database ke kondisi sebelum transaksi dimulai. Ini digunakan untuk memastikan bahwa database tetap dalam keadaan konsisten jika terjadi kesalahan selama transaksi.

    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from categories'); // memastikan table kosong setiap menjalankan unit test ini
    }

    public function testTransactionSuccess()
    {
        DB::transaction(function () {
            DB::insert('insert into categories(id, name, description, created_at) values (?, ?, ? , ?)', [
                "GADGET",
                "Gadget",
                "Gadget Category",
                "2020-10-10 10:10:10"
            ]);
            DB::insert('insert into categories(id, name, description, created_at) values (?, ?, ? , ?)', [
                "FOOD",
                "Food",
                "Food Category",
                "2020-10-10 10:10:10"
            ]);
        });

        $results = DB::select("select * from categories");
        self::assertCount(2, $results);

    }


    public function testTransactionFailed()
    {
        try {
            DB::transaction(function () {
                DB::insert('insert into categories(id, name, description, created_at) values (?, ?, ? , ?)', [
                    "GADGET",
                    "Gadget",
                    "Gadget Category",
                    "2020-10-10 10:10:10"
                ]);
                // akan terjadi error karena id sudah didaftarkan sebelumnya, sehingga semua transaksi akan dirollback
                DB::insert('insert into categories(id, name, description, created_at) values (?, ?, ? , ?)', [
                    "GADGET",
                    "Food",
                    "Food Category",
                    "2020-10-10 10:10:10"
                ]);
            });
        } catch (QueryException $error) {
            // expected
        }

        $results = DB::select("select * from categories");
        self::assertCount(0, $results);

    }


    // Manual Database Transaction --> mengecek transaction secara manual
    // DB::beginTransaction() --> memulai transaksi
    // DB::commit() --> melakukan commit/simpan perubahan transaksi
    // DB::rollback() --> melakukan rollback/pembatalan transaksi
    public function testMaualTransactionSuccess()
    {
        try {
            DB::beginTransaction();
            DB::insert('insert into categories(id, name, description, created_at) values (?, ?, ? , ?)', [
                "GADGET",
                "Gadget",
                "Gadget Category",
                "2020-10-10 10:10:10"
            ]);
            DB::insert('insert into categories(id, name, description, created_at) values (?, ?, ? , ?)', [
                "FOOD",
                "Food",
                "Food Category",
                "2020-10-10 10:10:10"
            ]);
            DB::commit();
        } catch (QueryException $error) {
            DB::rollBack();
        }

        $results = DB::select("select * from categories");
        self::assertCount(2, $results);

    }

    public function testMaualTransactionFailed()
    {
        try {
            DB::beginTransaction();
            DB::insert('insert into categories(id, name, description, created_at) values (?, ?, ? , ?)', [
                "GADGET",
                "Gadget",
                "Gadget Category",
                "2020-10-10 10:10:10"
            ]);
            DB::insert('insert into categories(id, name, description, created_at) values (?, ?, ? , ?)', [
                "GADGET",
                "Food",
                "Food Category",
                "2020-10-10 10:10:10"
            ]);
            DB::commit();
        } catch (QueryException $error) {
            DB::rollBack();
        }

        $results = DB::select("select * from categories");
        self::assertCount(0, $results);

    }
}
