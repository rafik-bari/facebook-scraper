<?php
if(!session_id()) {
    session_start();
}
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
Route::resource('tokens', 'AppTokensController');
Route::get('/purge', function () {
    $t1 = \App\Page::truncate();
    $t2 = \App\Keyword::truncate();
    $t3 = \App\ApiError::truncate();
    return \Response::json(['status' => ($t1 && $t2 && $t3)]);

})->middleware('auth');

Route::get('/csv', 'HomeController@csvData')->name('csv');


Route::get('/testFields', function () {


    $appToken = \App\AppToken::first();
    $app_id = '631064233723978';
    $app_secret = '4d0b5e1d89ac7c58818b6a949125cef5';
    $fb = new \Facebook\Facebook([
        'app_id' => $app_id,
        'app_secret' => $app_secret,
        'default_graph_version' => 'v2.10',

    ]);



    if(isset($_GET['code'])) {
        $helper = $fb->getRedirectLoginHelper();
        try {
            $accessToken = $helper->getAccessToken();
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (isset($accessToken)) {
            // Logged in!
            $_SESSION['facebook_access_token'] = (string) $accessToken;
            $fb->setDefaultAccessToken($accessToken);
            // Now you can redirect to another page and use the
            // access token from $_SESSION['facebook_access_token']
        } elseif ($helper->getError()) {
            // The user denied the request
            exit;
        }

        try {

            $response = $fb->get('/100016591030065?metadata=1');
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {

            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {

            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        $arr = $response->getDecodedBody()['metadata']['fields'];
        $all_fields = array_map(function($v) {
            return $v['name'].' :    --------->'.$v['description'];
        },$arr);

    } else {
        $helper = $fb->getRedirectLoginHelper();
        $permissions = ['public_profile']; // optional
        $loginUrl = $helper->getLoginUrl('http://fb.matadorwebgroup.com/testFields', $permissions);
        echo $loginUrl;
    }



});
Route::get('/testRequest', function () {


    $appToken = \App\AppToken::first();
    $app_id = '631064233723978';
    $app_secret = '4d0b5e1d89ac7c58818b6a949125cef5';
    $fb = new \Facebook\Facebook([
        'app_id' => $app_id,
        'app_secret' => $app_secret,
        'default_graph_version' => 'v2.10',

    ]);



    if(isset($_GET['code'])) {
        $helper = $fb->getRedirectLoginHelper();
        try {
            $accessToken = $helper->getAccessToken();
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (isset($accessToken)) {
            // Logged in!
            $_SESSION['facebook_access_token'] = (string) $accessToken;
            $fb->setDefaultAccessToken($accessToken);
            // Now you can redirect to another page and use the
            // access token from $_SESSION['facebook_access_token']
        } elseif ($helper->getError()) {
            // The user denied the request
            exit;
        }

        try {

            $response = $fb->get('/562681370482367?metadata=1');
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {

            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {

            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        $arr = $response->getDecodedBody()['metadata']['fields'];
        $all_fields = array_map(function($v) {
            return $v['name'];
        },$arr);

        $fields  = implode(',',$all_fields);
        $impossible = [];
        foreach ($all_fields as $field) {

            try {
                $response = $fb->get('/562681370482367?fields='.$field);

            } catch (\Facebook\Exceptions\FacebookResponseException $e) {
                $impossible[] = $field;
            }

        }
        $possible = [];

        foreach ($all_fields as $field) {

            if(!in_array($field,$impossible)) {
                $possible[] = $field;
            }



        }

        try {
            $response = $fb->get('/562681370482367/?fields='.implode(',',$possible));
            dd($response->getDecodedBody());

        } catch (\Facebook\Exceptions\FacebookResponseException $e) {

        }



    } else {
        $helper = $fb->getRedirectLoginHelper();
        $permissions = ['public_profile']; // optional
        $loginUrl = $helper->getLoginUrl('http://fb.matadorwebgroup.com/testRequest', $permissions);
        echo $loginUrl;
    }



});