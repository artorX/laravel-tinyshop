<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopProductsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->string('name')->index();
            $table->text('description')->nullable();
            $table->integer('status')->index();
            $table->bigInteger('main_category_id');
            $table->bigInteger('brand_id')->index();
            $table->decimal('old_price', 10, 2)->nullable();
            $table->decimal('price', 10, 2)->nullable()->index();
            $table->decimal('rating', 4, 2)->nullable()->index();

            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            $table->timestamps();

            // FK
            $table->foreign('main_category_id')->references('id')->on('shop_categories')->onDelete('RESTRICT');
            $table->foreign('brand_id')->references('id')->on('shop_brands')->onDelete('RESTRICT');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_products');
    }

}
