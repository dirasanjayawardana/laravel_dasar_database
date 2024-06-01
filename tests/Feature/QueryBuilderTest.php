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
        // insert into `categories` (`id`, `name`) values (?, ?)

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
        // select * from `categories` where (`id` = ? or `id` = ?)
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
        // select * from `categories` where `created_at` between ? and ?

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
        // select * from `categories` where `id` in (?, ?)

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
        // select * from `categories` where `description` is null

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
        // select * from `categories` where date(`created_at`) = ?

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
        // update `categories` set `name` = ? where `id` = ?

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
        // select exists(select * from `categories` where (`id` = ?)) as `exists`
        // insert into `categories` (`id`, `name`, `description`, `created_at`) values (?, ?, ?, ?)

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
        // update `counters` set `counter` = `counter` + 1 where `id` = ?

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
        // delete from `categories` where `id` = ?

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


    // Query Ordering (mengurutkan data)
    // orderBy(column, order) --> aroder bisa berupa asc(ascending) atau desc(descending), defaultnya ascending jika tidak disebutkan
    public function testOrdering()
    {
        $this->insertProducts();

        $collection = DB::table("products")->whereNotNull("id")
            ->orderBy("price", "desc")->orderBy("name", "asc")->get();
        //select * from `products` where `id` is not null order by `price` desc, `name` asc

        self::assertCount(2, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }


    // Query Paging
    // take(number) --> untuk melakukan LIMIT
    // skip(number) --> untuk melakukan OFFSET
    public function testPaging()
    {
        $this->insertCategories();

        $collection = DB::table("categories")
            ->skip(0)
            ->take(2)
            ->get();
        // select * from `categories` limit 2 offset 0

        self::assertCount(2, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }


    public function insertManyCategories()
    {
        for ($i = 0; $i < 100; $i++) {
            DB::table("categories")->insert([
                "id" => "CATEGORY-$i",
                "name" => "Category $i",
                "created_at" => "2020-10-10 10:10:10"
            ]);
        }
    }


    // Chunk (memotong data hasil query secara bertahap agar query yang di load tidak membuat out of memory, harus menambhakan ordering pada query nya)
    // chunk(number, callback) --> dimana number ada jumlah data yang ingin diambil
    public function testChunk()
    {
        $this->insertManyCategories();

        DB::table("categories")->orderBy("id")
            ->chunk(10, function ($categories) {
                self::assertNotNull($categories);
                Log::info("Start Chunk");
                $categories->each(function ($category) {
                    Log::info(json_encode($category));
                });
                Log::info("End Chunk");
            });
        // select * from `categories` order by `id` asc limit 10 offset 0
        // akan mengambil data berulang dimana dalam sekali pengambilan mengambil 10 data
    }


    // Lazy Result (mirip seperti chunk, namun tidak akan dieksekusi jika tidak diperlukan, namun akan mengembalikan lazyCollection)
    // lazy(number)
    public function testLazy()
    {
        $this->insertManyCategories();

        $collection = DB::table("categories")->orderBy("id")->lazy(10)->take(3); // chunk lazy(10) akan dieksekusi karena dibutuhkan pada take(3)
        // select * from `categories` order by `id` asc limit 10 offset 0
        self::assertNotNull($collection);

        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }


    // Cursor (chunk dan lazy melakukan sebenarnya paging dibelakang layar, sedangkan cursor hanya akan melakukan query satu kali)
    // Cursor mengambil datanya satu persatu menggunakan PDO::fetch()
    // secara penggunaan memory, cursor akan lebih hemat dibandingkan dengan chunk atau lazy, namun cursor bisa lebih lambat karena satu persatu
    // cursor() --> tidak perlu menentukan jumlah setiap pengambilan data, karena hanya akan mengambil satu data
    public function testCursor()
    {
        $this->insertManyCategories();

        $collection = DB::table("categories")->orderBy("id")->cursor();
        // select * from `categories` order by `id` asc
        // lalu data akan diambil satu persatu
        self::assertNotNull($collection);

        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }


    // Agregate
    // count(column) --> untuk mengambil jumlah data
    // min(column) --> untuk mengambil minimal data
    // max(column) --> untuk mengambil maksimal data
    // avg(column) --> untuk mengambil rata-rata data
    // sum(column) --> untuk menjumlahkan data
    public function testAggregate()
    {
        $this->insertProducts();

        $result = DB::table("products")->count("id");
        // select count(`id`) as aggregate from `products`
        self::assertEquals(2, $result);

        $result = DB::table("products")->min("price");
        // select min(`price`) as aggregate from `products`
        self::assertEquals(18000000, $result);

        $result = DB::table("products")->max("price");
        // select max(`price`) as aggregate from `products`
        self::assertEquals(20000000, $result);

        $result = DB::table("products")->avg("price");
        // select avg(`price`) as aggregate from `products`
        self::assertEquals(19000000, $result);

        $result = DB::table("products")->sum("price");
        // select sum(`price`) as aggregate from `products`
        self::assertEquals(38000000, $result);
    }


    // Query Builder Raw (kombinasi dari query builder dengan raw query)
    // DB::raw(query)
    public function testQueryBuilderRaw()
    {
        $this->insertProducts();

        $collection = DB::table("products")
            ->select(
                DB::raw("count(id) as total_product"),
                DB::raw("min(price) as min_price"),
                DB::raw("max(price) as max_price"),
            )->get();
        // select count(id) as total_product, min(price) as min_price, max(price) as max_price from `products`

        self::assertEquals(2, $collection[0]->total_product);
        self::assertEquals(18000000, $collection[0]->min_price);
        self::assertEquals(20000000, $collection[0]->max_price);
    }


    public function insertProductFood()
    {
        DB::table("products")->insert([
            "id" => "3",
            "name" => "Bakso",
            "category_id" => "FOOD",
            "price" => 20000
        ]);
        DB::table("products")->insert([
            "id" => "4",
            "name" => "Mie Ayam",
            "category_id" => "FOOD",
            "price" => 20000
        ]);
    }


    // Grouping
    // groupBy(column) --> melakukan group sesuai dengan variasi value yang ada di column yg ditentukan
    public function testGroupBy()
    {
        $this->insertProducts();
        $this->insertProductFood();

        $collection = DB::table("products")
            ->select("category_id", DB::raw("count(*) as total_product"))
            ->groupBy("category_id")
            ->orderBy("category_id", "desc")
            ->get();
        // select `category_id`, count(*) as total_product from `products` group by `category_id` order by `category_id` desc

        self::assertCount(2, $collection);
        self::assertEquals("SMARTPHONE", $collection[0]->category_id);
        self::assertEquals("FOOD", $collection[1]->category_id);
        self::assertEquals(2, $collection[0]->total_product);
        self::assertEquals(2, $collection[1]->total_product);
    }
    // Having (mirip seperti filter)
    // having(column, operator, value)
    public function testGroupByHaving()
    {
        $this->insertProducts();
        $this->insertProductFood();

        $collection = DB::table("products")
            ->select("category_id", DB::raw("count(*) as total_product"))
            ->groupBy("category_id")
            ->having(DB::raw("count(*)"), ">", 2)
            ->orderBy("category_id", "desc")
            ->get();
        // select `category_id`, count(*) as total_product from `products` group by `category_id` having count(*) > ? order by `category_id` desc

        self::assertCount(0, $collection);
    }

}
