<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_products', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('invoice_id');
            $table->bigInteger('product_id');
            $table->string('qty')->nullable();
            $table->float('price',8, 2)->default(0);
            $table->float('total',8, 2)->default(0);
            $table->float('discount',8, 2)->default(0);
            $table->float('sub_total',8, 2)->default(0);
            $table->float('vat',8, 2)->default(0)->comment('Percentage');
            $table->float('grand_total',8, 2)->default(0);

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
        Schema::dropIfExists('invoice_products');
    }
}
