<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
            	'id' => 1,
                'name' => 'Supper Admin',
				'email' => 'admin@test.com',
				'slug' => 'super-admin-1',
				'password' => bcrypt('admin123'),
				'phone_no' => '+92336443121',
				'is_admin' => 1,
				'is_verified' => 1,
				'status' => 1,
				'address' => 'Lahore',
				'country' => 'Pakistan',
				'state' => 'Punjab',
				'city' => 'Lahore',
            ],
        ]);
    }
}
