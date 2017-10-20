<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settingsRow = new \App\Settings();
        $settingsRow->minimum_fan_count = 100;
        $settingsRow->must_have_email = false;
        $settingsRow->save();
    }
}
