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
Route::resource('keyword', 'KeywordController');
Route::resource('page', 'PageController');
Route::resource('pendingPage', 'PendingPageController');
Route::resource('settings', 'SettingsController');
Route::resource('fields', 'PageFieldController');

Route::get('/purge', function () {
    $t1 = \App\Page::truncate();
    $t2 = \App\Keyword::truncate();
    $t3 = \App\ApiError::truncate();
    return \Response::json(['status' => ($t1 && $t2 && $t3)]);

})->middleware('auth');

Route::get('/csv', 'HomeController@csvData')->name('csv');




Route::get('/testjob', function () {
    // require_once __DIR__ . '/vendor/autoload.php'; // change path as needed
    $app_id = '1083006501758342';
    $app_secret = '73c47ebeba31ae98dc00dd7d152f2a86';
    $fb = new \Facebook\Facebook([
        'app_id' => $app_id,
        'app_secret' => $app_secret,
        'default_graph_version' => 'v2.10',
    ]);


    $access_token = $app_id . '|' . $app_secret;
    $fields = \App\PageField::get(['name'])->implode('name', ',');
    $pages_ids = \App\PendingPage::all();
    $batch = [];
    $fb->setDefaultAccessToken($access_token);
    $keywords_chunk = ['test'];
    foreach ($keywords_chunk as $keyword) {
        $keyword_ = urlencode($keyword);
        // $batch[] = $fb->request('GET',"/$page->page_id?fields=$fields");
        $batch[] = $fb->request('GET', "/search?type=page&q=$keyword_&fields=$fields&limit=100");
    }


    try {
        $responses = $fb->sendBatchRequest($batch);
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        $apiErrpr = new \App\ApiError();
        $apiErrpr->message = $e->getMessage();
        $apiErrpr->save();
        exit;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        $apiErrpr = new \App\ApiError();
        $apiErrpr->message = $e->getMessage();
        $apiErrpr->save();
        exit;
    }

    foreach ($responses as $key => $response) {
        if ($response->isError()) {
            $apiErrpr = new \App\ApiError();
            $e = $response->getThrownException();

            $apiErrpr->message = $e->getMessage();


            $apiErrpr->save();

        } else {
            $resp = $response->getDecodedBody();
            $pages_data = $resp['data'];
            $chunk_100_pages = [];
            foreach ($pages_data as $page) {
                $pa = new \App\Page();

                foreach ($page as $k => $v) {
                    if (is_array($v)) {
                        $page[$k] = serialize($v);
                    }
                }
                $pa->fill($page);
                $pa->save();
            }
            if(isset($resp['paging']) && isset($resp['paging']['next'])) {
                $next_url = $resp['paging']['next'];
                \App\Jobs\ScrapeNextPage::dispatch($next_url);
            }
        }
    }

});

Route::get('/test2', function () {
    $rows = \App\Page::simplePaginate(100);
    $enabled_fields = \App\PageField::where('enabled', true)->pluck('name')->toArray();
    $ids = [];
    $data = [];
    foreach ($rows as $page) {
        dd($page);
        if (!in_array($page->id, $ids)) {
            $ids[] = $page->id;
            $singlePageData = [];

            foreach ($enabled_fields as $field) {
                if (!isset($page->$field)) {

                    $fieldValue = serialize($page->$field);
                } else {
                    switch ($field) {

                        default:
                            $fieldValue = $page->$field;
                            break;
                    }


                }
                $singlePageData[$field] = $fieldValue;
            }
            $data[] = $singlePageData;
        }
       // dd($data);

    }

});