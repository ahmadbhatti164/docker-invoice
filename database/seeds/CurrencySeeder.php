<?php

use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::insert("insert into `currencies` (`id`, `country`,  `name`, `lang_code`) values('1','United States','USD','en_US')");
        DB::insert("insert into `currencies` (`id`, `country`,  `name`, `lang_code`) values('2','Italy','EURO','it_IT')");
        DB::insert("insert into `currencies` (`id`, `country`,  `name`, `lang_code`) values('3','Denmark','DKK','dk_DK')");
    }
}
