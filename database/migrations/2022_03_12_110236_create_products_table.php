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
        Schema::create('products', function (Blueprint $table) {            
            $table->bigIncrements('id');
            $table->string('name');
            $table->enum('freshfromthefarm',['0,1'])->default('0')->comment('is freshfromthefarm == 1 else == 0');
            $table->decimal('pav_price', 8, 2)->default(0.00);
            $table->decimal('half_kg_price', 8, 2)->default(0.00);
            $table->decimal('kg_price', 8, 2)->default(0.00);
            $table->unsignedBigInteger('category_id');
            $table->integer('status')->nullable();
            $table->longText('description');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->softDeletes();
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
        Schema::dropIfExists('products');
    }
};
