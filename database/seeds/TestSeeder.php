<?php

use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pending_pages')->insert(
            [
                'page_id' => '1377316249228679'
            ]
        );
    }
}
