<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Redirect::to('/login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::resource('keyword','KeywordController');
Route::resource('page', 'PageController');
Route::resource('pendingPage', 'PendingPageController');
Route::resource('settings', 'SettingsController');
Route::resource('fields', 'PageFieldController');



Route::get('/test', function () {
    // require_once __DIR__ . '/vendor/autoload.php'; // change path as needed
    $app_id = '1083006501758342';
    $app_secret = '73c47ebeba31ae98dc00dd7d152f2a86';
    $fb = new \Facebook\Facebook([
        'app_id' => $app_id,
        'app_secret' => $app_secret,
        'default_graph_version' => 'v2.10',
    ]);


    $access_token = $app_id . '|' . $app_secret;
    $fields = \App\PageField::where('enabled', '1')->get(['name'])->implode('name', ',');
    $pages_ids = \App\PendingPage::all();
    $batch = [];
    $fb->setDefaultAccessToken($access_token);

    foreach ($pages_ids as $page) {
       // $batch[] = $fb->request('GET',"/$page->page_id?fields=$fields");
        $batch[] = $fb->request('GET',"/search?type=page&q=a&fields=$fields&limit=5000");
        $batch[] = $fb->request('GET',"/search?type=page&q=b&fields=$fields&limit=5000");
        $batch[] = $fb->request('GET',"/search?type=page&q=c&fields=$fields&limit=5000");
        $batch[] = $fb->request('GET',"/search?type=page&q=d&fields=$fields&limit=5000");
        $batch[] = $fb->request('GET',"/search?type=page&q=e&fields=$fields&limit=5000");
        $batch[] = $fb->request('GET',"/search?type=page&q=f&fields=$fields&limit=5000");
    }


    try {
        $responses = $fb->sendBatchRequest($batch);
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }

    foreach ($responses as $key => $response) {
        if ($response->isError()) {
            $e = $response->getThrownException();
            echo '<p>Error! Facebook SDK Said: ' . $e->getMessage() . "\n\n";
            echo '<p>Graph Said: ' . "\n\n";
            var_dump($e->getResponse());
        } else {
            $resp = $response->getDecodedBody();
            $pages_data = $resp['data'];
            $chunk_100_pages=[];
            foreach (array_chunk($pages_data,100) as $pages_rows) {
                $chunk_100_pages[] = [
                    'pages_data_chunk'=>serialize($pages_rows),
                    'chunk_size' => count($pages_rows)
                ];
                \DB::table('pages')->insert($chunk_100_pages);
            }
        }
    }





});