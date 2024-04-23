<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug');
            $table->string('email');
            $table->string('phone_no');
            $table->string('password');
            $table->string('verified_token')->nullable();
            $table->boolean('is_admin')->default(0);
            $table->boolean('is_verified')->default(0);
            $table->boolean('status')->default(0);
            $table->string('address')->nullable(); 
            $table->string('country')->nullable();
            $table->string('state')->nullable(); 
            $table->string('city')->nullable();
            $table->text('from_emails')->nullable(); 
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
        Schema::dropIfExists('users');
    }
}
