<?php

namespace Tests\Feature;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class QueryBuilderTest extends TestCase
{
    // Query Builder (mmepermudah melakukan query ke database)
    // DB::table(namaTable)->method()

    protected function setUp(): void
    {
        parent::setUp();
        DB::delete("delete from products");
        DB::delete("delete from categories");
        // DB::delete("delete from counters");
    }

    // Query Insert
    // insert(["namaKolom" => "value"]) --> memasukkan data ke database, jika ada error maka akan throw exception
    // insertGetId() --> memasukkan data ke database dan mengembalikan primary key yang diset
    // insertOrIgnore() --> memasukkan data ke database, jika terjadi error maka akan di ignore
    public function testInsert()
    {
        DB::table("categories")->insert([
            "id" => "GADGET",
            "name" => "Gadget"
        ]);
        DB::table("categories")->insert([
            "id" => "FOOD",
            "name" => "Food"
        ]);

        $result = DB::select("select count(id) as total from categories");
        self::assertEquals(2, $result[0]->total);
    }


    // Query Select
    // select(columns) --> melakukan select berdasarkan column, jika tidak disebutkan columnya maka akan select semua column
    // get(columns) --> menyimpan data yang sudah diselect, jika columns tidak diisi akan diambil semua columns
    // first(columns) --> menyimpan data pertama yang sudah diselect, jika columns tidak diisi akan diambil semua columns
    // pluck(columns) --> menyimpan data dari satu column saja setelah data diselect
    // hasil dari query builder select adalah collection, bukan array
    public function testSelect()
    {
        $this->testInsert();

        $collection = DB::table("categories")->select(["id", "name"])->get();
        self::assertNotNull($collection);

        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });

    }

    public function insertCategories()
    {
        DB::table("categories")->insert([
            "id" => "SMARTPHONE",
            "name" => "Smartphone",
            "created_at" => "2020-10-10 10:10:10"
        ]);
        DB::table("categories")->insert([
            "id" => "FOOD",
            "name" => "Food",
            "created_at" => "2020-10-10 10:10:10"
        ]);
        DB::table("categories")->insert([
            "id" => "LAPTOP",
            "name" => "Laptop",
            "created_at" => "2020-10-10 10:10:10"
        ]);
        DB::table("categories")->insert([
            "id" => "FASHION",
            "name" => "Fashion",
            "created_at" => "2020-10-10 10:10:10"
        ]);
    }


    // Query Select Where
    // where(column, operator, value) --> AND (condition), contoh condition --> namaKolom=value
    // where([condition1, condition2]) --> AND (condition1 and condition2 ...)
    // where(callback(Builder)) --> AND (condition)
    // orWhere(column, operator, value) --> OR (condition)
    // orWhere(callback(Builder)) --> OR (condition)
    // whereNot(callback(Builder)) --> NOT (condition)
    public function testWhere()
    {
        $this->insertCategories();

        $collection = DB::table("categories")->where(function (Builder $builder) {
            $builder->where('id', '=', 'SMARTPHONE');
            $builder->orWhere('id', '=', 'LAPTOP');
            // SELECT * FROM categories WHERE (id = smartphone OR id = laptop)
        })->get();

        self::assertCount(2, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });

    }
    // whereBetween(column, [valu1, valu2]) --> WHERE column BETWEEN value1 AND valu2
    // whereNotBetween(column, [valu1, valu2]) --> WHERE column NOT BETWEEN value1 AND valu2
    public function testWhereBetween()
    {
        $this->insertCategories();

        $collection = DB::table("categories")
            ->whereBetween("created_at", ["2020-09-10 10:10:10", "2020-11-10 10:10:10"])
            ->get();

        self::assertCount(4, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }
    // whereIn(column, [array]) --> WHERE column IN array
    // whereNotIn(column, [array]) --> WHERE column NOT IN array
    public function testWhereIn()
    {
        $this->insertCategories();

        $collection = DB::table("categories")->whereIn("id", ["SMARTPHONE", "LAPTOP"])->get();

        self::assertCount(2, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });

    }
    // whereNull(column) --> WHERE column IS NULL
    // whereNotNull(column) --> WHERE column IS NOT NULL
    public function testWhereNull()
    {
        $this->insertCategories();

        $collection = DB::table("categories")
            ->whereNull("description")->get();

        self::assertCount(4, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }
    // whereDate(column, value) --> WHERE DATE(column) = value
    // whereMonth(column, value) --> WHERE MONTH(column) = value
    // whereDay(column, value) --> WHERE DAY(column) = value
    // whereYear(column, value) --> WHERE YEAR(column) = value
    // whereTime(column, value) --> WHERE TIME(column) = value
    public function testWhereDate()
    {
        $this->insertCategories();

        $collection = DB::table("categories")
            ->whereDate("created_at", "2020-10-10")->get();

        self::assertCount(4, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }


    // Query Update
    // update(["kolom" => "value"])
    public function testUpdate()
    {
        $this->insertCategories();

        DB::table("categories")->where("id", "=", "SMARTPHONE")->update([
            "name" => "Handphone"
        ]);

        $collection = DB::table("categories")->where("name", "=", "Handphone")->get();
        self::assertCount(1, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }


    // Query Update or Insert (Upsert) --> melakukan update, namun jika datanya tidak ada akan insert data baru
    // updateOrInsert([attribute], [value])
    public function testUpsert()
    {

        DB::table("categories")->updateOrInsert([
            "id" => "VOUCHER"
        ], [
            "name" => "Voucher",
            "description" => "Ticket and Voucher",
            "created_at" => "2020-10-10 10:10:10"
        ]);

        $collection = Db::table("categories")->where("id", "=", "VOUCHER")->get();
        self::assertCount(1, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }


    // Query Increment dan Decrement
    // increment(column, jumlahIncrement) --> melakukan increment
    // decrement(column, jumlahDecrement) --> melakukan decrement
    public function testIncrement()
    {
        DB::table("counters")->where("id", "=", "sample")->increment("counter", 1);

        $collection = DB::table("counters")->where('id', '=', 'sample')->get();
        self::assertCount(1, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }


    // Query Delete
    // delete() --> melakukan delete
    // truncate() --> melakukan delete table dan membuat ulang
    public function testDelete()
    {
        $this->insertCategories();

        DB::table("categories")->where('id', '=', 'SMARTPHONE')->delete();

        $collection = DB::table("categories")->where("id", "=", "SMARTPHONE")->get();
        self::assertCount(0, $collection);
    }


    public function insertProducts()
    {
        $this->insertCategories();

        DB::table("products")->insert([
            "id" => "1",
            "name" => "iPhone 14 Pro Max",
            "category_id" => "SMARTPHONE",
            "price" => 20000000
        ]);
        DB::table("products")->insert([
            "id" => "2",
            "name" => "Samsung Galaxy S21 Ultra",
            "category_id" => "SMARTPHONE",
            "price" => 18000000
        ]);
    }


    // Query Join (menggabungkan isi dari dua tabel atau lebih ketika memiliki kecocokan)
    // join(table, column, operator, ref_column) --> untuk JOIN atau INNER JOIN
    // leftJoin(table, column, operator, ref_column) --> untuk LEFT JOIN
    // rightJoin(table, column, operator, ref_column) --> untuk RIGHT JOIN
    // crossJoin(table, column, operator, ref_column) --> untuk CROS JOIN
    public function testJoin()
    {
        $this->insertProducts();

        // contoh menggabungkan isi dari table products dengan categories ketika products.category_id sama dengan categories.id
        $collection = DB::table("products")
            ->select("products.id", "products.name", "products.price", "categories.name as category_name") // categories.name dibuat alias agar tidak bentrok dengan product.name
            ->join("categories", "products.category_id", '=', 'categories.id')
            ->get();
        // select `products`.`id`, `products`.`name`, `products`.`price`, `categories`.`name` as `category_name` from `products` inner join `categories` on `products`.`category_id` = `categories`.`id`

        self::assertCount(2, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }
}
