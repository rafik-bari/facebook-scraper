<?php

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PageFieldsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $appToken = \App\AppToken::first();
        $app_id = $appToken->app_id;
        $app_secret = $appToken->app_secret;
        $fb = new \Facebook\Facebook([
            'app_id' => $app_id,
            'app_secret' => $app_secret,
            'default_graph_version' => 'v2.10',

        ]);


        $access_token = $app_id . '|' . $app_secret;
        $fb->setDefaultAccessToken($access_token);
        try {

            $response = $fb->get('/1377316249228679?metadata=1');
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {

            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {

            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }


        /*UNCOMMENT THIS CODE BELOW IF YOU WANT TO PULL ALL ACTUAL FIELDS INCLUDING NON PUBLIC ONES*/
        /*WARNING? THE CODE WILL NOT WORK IF YOU DONT HAVE THE PERMISSION OF ONE OF THE FIELDS*/


        $public_fields = ['id', 'about', 'app_links', 'can_checkin', 'category',
            'checkins', 'company_overview', 'cover', 'description', 'description_html',
            'emails', 'engagement', 'fan_count', 'featured_video', 'founded', 'global_brand_page_name',
            'global_brand_root_id', 'has_whatsapp_number', 'impressum', 'is_owned', 'is_published', 'is_unclaimed',
            'link', 'location', 'mission', 'name', 'parking', 'pharma_safety_info', 'phone', 'products',
            'rating_count', 'schedule', 'single_line_address', 'start_info', 'store_number', 'talking_about_count',
            'username', 'verification_status', 'voip_info', 'website', 'were_here_count', 'whatsapp_number'];
        $i = 0;
        $enabled_by_default = ['category', 'emails', 'engagement', 'fan_count', 'is_published', 'link', 'name', 'username'];
        foreach ($response->getDecodedBody()['metadata']['fields'] as $field) {


            if (in_array($field['name'], $public_fields, 0)) {
                $f = new \App\PageField();
                $f->fill($field);
                if (in_array($field['name'], $enabled_by_default)) {
                    $f->enabled = true;
                }
                $f->save();
                Schema::table('pages', function (Blueprint $table) use ($field) {
                    $column_type = $this->getColumnTypeFromField($field);
                    switch ($column_type) {
                        case 'integer':
                            $table->integer($field['name'])->nullable();
                            break;
                        case 'binary':
                            $table->binary($field['name'])->nullable();
                            break;
                        case 'varchar':
                            $table->string($field['name'])->nullable();
                            break;
                        case 'longtext':
                            $table->longtext($field['name'])->nullable();
                            break;
                        case 'float':
                            $table->float($field['name'], 10, 8)->nullable();
                            break;
                        case 'boolean':
                            $table->boolean($field['name'])->nullable();
                            break;
                        default:
                            $table->text($field['name'])->nullable();
                            break;
                    }

                });
            }


        }

    }

    private function getColumnTypeFromField($field)
    {
        if (!isset($field['name'])) {
            exit('Error getting metadata from fb api');
        }
        $r = 'string';
        switch ($field['name']) {
            case 'emails':
            case 'app_links':
            case 'engagement':
            case 'location':
            case 'start_info':
            case 'voip_info':
                $r = 'binary';
                break;
        }

        if (isset($field['type'])) {
            switch ($field['type']) {
                case "bool":
                    $r = 'boolean';
                    break;
                case "float":
                    $r = "float";
                    break;
            }
        }


        return $r;
    }
}
