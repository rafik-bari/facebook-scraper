<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DemoAppTokensSeeder::class);
        $this->call(PageFieldsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(TestSeeder::class);
    }
}
