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
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_number');
            $table->decimal('order_amount', 8, 2);
            $table->string('offer_product_name')->nullable();
            $table->string('offer_product_qty')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->string('mobile',13);
            $table->string('email');
            $table->string('txn_id')->nullable();
            $table->enum('payment_method', ['Cod', 'Online'])->nullable()->comment('cod=> cash on delivery, online => payment gateway');
            $table->string('payment_status')->default('Pending');
            $table->enum('order_delivery_status', ['Pending', 'Deliver'])->default('Pending');
            $table->enum('driver_payment_type', ['Cash', 'Online'])->nullable();
            $table->foreign('driver_id')->references('id')->on('users');
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
        Schema::dropIfExists('orders');
    }
};
