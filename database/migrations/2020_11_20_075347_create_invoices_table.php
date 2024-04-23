<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->bigInteger('vendor_id');
            $table->string('title');
            $table->string('slug');
            $table->string('invoice_number');
            $table->string('invoice_date');
            $table->string('pdf_file');
            $table->string('html_file');
            $table->float('total',8, 2)->default(0);
            $table->float('discount',8, 2)->default(0);
            $table->float('sub_total',8, 2)->default(0);
            $table->float('vat',8, 2)->default(0)->comment('Percentage');
            $table->float('grand_total',8, 2)->default(0);
            $table->float('balance',8, 2)->default(0);
            $table->string('cvr_number')->nullable();
            $table->string('currency_id')->default(3)->comment('Denmark Currency');
            $table->text('billing_address');
            $table->text('shipping_address');
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
        Schema::dropIfExists('invoices');
    }
}
