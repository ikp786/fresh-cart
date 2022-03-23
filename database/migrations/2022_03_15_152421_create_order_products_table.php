<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->string('product_name');
            $table->string('product_description');
            $table->string('product_quantity_phav')->default(0);
            $table->string('product_quantity_half_kg')->default(0);
            $table->string('product_quantity_kg')->default(0);
            $table->string('product_total_quantity');            
            $table->decimal('product_phav_amount', 8, 2)->default(0.00);
            $table->decimal('product_half_kg_amount', 8, 2)->default(0.00);
            $table->decimal('product_kg_amount', 8, 2)->default(0.00);
            $table->decimal('total_amount', 8, 2);
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('product_id')->references('id')->on('products');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_products');
    }
};
