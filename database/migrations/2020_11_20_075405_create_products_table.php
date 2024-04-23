<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
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
            $table->bigInteger('user_id');
            $table->bigInteger('invoice_id');
            $table->bigInteger('vendor_id');
            $table->string('name');
            $table->string('slug');
            $table->string('qty')->nullable();
            $table->string('product_no');
            $table->string('currency_id')->default(3)->comment('Denmark Currency');
            $table->float('price',8, 2)->default(0);
            $table->float('total',8, 2)->default(0);
            $table->float('discount',8, 2)->default(0);
            $table->float('sub_total',8, 2)->default(0);
            $table->float('vat',8, 2)->default(0)->comment('Percentage');
            $table->float('grand_total',8, 2)->default(0);
            $table->boolean('status')->default(0);
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();
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
}
