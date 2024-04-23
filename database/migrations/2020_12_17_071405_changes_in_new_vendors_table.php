<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangesInNewVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_vendors', function (Blueprint $table) {
            $table->integer('user_id')->nullable()->change();
            $table->string('user_email')->nullable()->after('user_id');
            $table->integer('vendor_id')->nullable()->after('user_email');
            $table->string('vendor_email')->nullable()->after('vendor_id');
            $table->string('comments')->nullable()->after('is_new');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_vendors', function (Blueprint $table) {
            //
        });
    }
}
