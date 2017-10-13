<?php

use Illuminate\Database\Seeder;

class PageFieldsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // require_once __DIR__ . '/vendor/autoload.php'; // change path as needed
        $app_id = '1083006501758342';
        $app_secret = '73c47ebeba31ae98dc00dd7d152f2a86';
        $fb = new \Facebook\Facebook([
            'app_id' => $app_id,
            'app_secret' => $app_secret,
            'default_graph_version' => 'v2.10',

        ]);


        $access_token = $app_id . '|' . $app_secret;
        try {

            $response = $fb->get('/1377316249228679?metadata=1', $access_token);
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {

            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {

            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        //dd($response->getDecodedBody()['metadata']['fields']);
        foreach ($response->getDecodedBody()['metadata']['fields'] as $field) {

            $f = new \App\PageField();
            $f->fill($field);
            if (isset($field['name']) && 'id' == $field['name']) { // set id field to enabled by default
                $f->enabled = 1;
            }
            $f->save();
        }
    }
}
