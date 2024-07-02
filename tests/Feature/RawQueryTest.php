<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RawQueryTest extends TestCase
{
    // DB::connection('nama_db_config') --> memilih configurasi database yg diinginkan, ada di config/database.php
    // DB::insert(sql, array): bool --> melakukan insert data
    // DB::update(sql, array): int --> melakukan update data
    // DB::delete(sql, array): int --> melakukan delete data
    // DB::select(sql, array): array --> melakukan select data
    // DB::statement(sql, array): bool --> melakukan jenis sql lain
    // DB::unprepared(sql, array): bool --> melakukan sql tanpa prepared, tidak disarankan (rawan sql injection)
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from categories'); // memastikan table kosong setiap menjalankan unit test ini
    }

    public function testCrud()
    {
        DB::insert('insert into categories(id, name, description, created_at) values (?, ?, ? , ?)', [
            "GADGET", "Gadget", "Gadget Category", "2020-10-10 10:10:10"
        ]);

        $results = DB::select('select * from categories where id = ?',  ['GADGET']);

        self::assertCount(1, $results);
        self::assertEquals('GADGET', $results[0]->id);
        self::assertEquals('Gadget', $results[0]->name);
        self::assertEquals('Gadget Category', $results[0]->description);
        self::assertEquals("2020-10-10 10:10:10", $results[0]->created_at);
    }


    // Named Binding (mengganti tanda ? menjadi nama parameter yang sesuai)
    public function testCrudNamedParameter()
    {
        DB::insert('insert into categories(id, name, description, created_at) values (:id, :name, :description , :created_at)', [
            "id" => "GADGET",
            "name" => "Gadget",
            "description" => "Gadget Category",
            "created_at" => "2020-10-10 10:10:10",
        ]);

        $results = DB::select('select * from categories where id = ?',  ['GADGET']);

        self::assertCount(1, $results);
        self::assertEquals('GADGET', $results[0]->id);
        self::assertEquals('Gadget', $results[0]->name);
        self::assertEquals('Gadget Category', $results[0]->description);
        self::assertEquals("2020-10-10 10:10:10", $results[0]->created_at);
    }
}


