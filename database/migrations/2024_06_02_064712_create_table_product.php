<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Run the migrations.
    public function up(): void
    {
        // Schema::table(namaTable, callback) --> alter table, merubah table
        // Schema::create(namaTable, callback) --> membuat tabel baru, method pada callback:
        // tipeData('namaKolom', size)
        // nullable(condition)
        // primary()
        // default(value)
        // useCurrent()
        // foreign(namaKolom)->refferences(kolomReferensi)->on(tableReferensi)
        Schema::create('products', function (Blueprint $table) {
            $table->string("id", 100)->nullable(false)->primary();
            $table->string("name", 100)->nullable(false);
            $table->text("description")->nullable(true);
            $table->integer("price")->nullable(false);
            $table->string("category_id", 100)->nullable(false);
            $table->timestamp("created_at")->nullable(false)->useCurrent();

            $table->foreign("category_id")->references("id")->on("categories");
        });
    }


    // Reverse the migrations. Selalu kebalikan dari apa yang dilakukan di up
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};