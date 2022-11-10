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
        Schema::create('carts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->string('product_name')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('product_quantity_phav')->nullable();
            $table->string('product_quantity_half_kg')->nullable();
            $table->string('product_quantity_kg')->nullable();
            $table->string('product_total_quantity');            
            $table->decimal('product_phav_amount', 8, 2)->default(0.00);
            $table->decimal('product_half_kg_amount', 8, 2)->default(0.00);
            $table->decimal('product_kg_amount', 8, 2)->default(0.00);
            $table->decimal('total_amount', 8, 2);
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('carts');
    }
};
