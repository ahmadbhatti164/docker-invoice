<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeleteColumnToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('invoice_products', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('vendors', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('user_vendors', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('companies', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('company_users', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('invoice_products', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('user_vendors', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('companies', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('company_users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
