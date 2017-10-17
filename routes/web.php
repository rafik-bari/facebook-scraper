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
    /*  $appToken = \App\AppToken::first();
      $app_id = $appToken->app_id;
      $app_secret = $appToken->app_secret;
      dd($app_id.'/'.$app_secret);
      // require_once __DIR__ . '/vendor/autoload.php'; // change path as needed
      $app_id = '1083006501758342';
      $app_secret = '73c47ebeba31ae98dc00dd7d152f2a86';
      $fb = new \Facebook\Facebook([
          'app_id' => $app_id,
          'app_secret' => $app_secret,
          'default_graph_version' => 'v2.10',
      ]);


      $access_token = $app_id . '|' . $app_secret;
      $fields = \App\PageField::where('enabled',true)->get(['name'])->implode('name', ',');
      dd($fields);
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
                  foreach ($page as $k => $v) {

                      if('emails' == $k) {
                          if ($v && count($v)) {
                              dd($v);
                              $fieldValue = '<ul>';
                              foreach ($v as $mobile_os => $app_data) {

                                  if (isset($app_data) && is_array($app_data)) {

                                      $k = array_keys( $app_data);
                                      $fieldValue .= "<li><b>$mobile_os:</b></li>";
                                      foreach ($app_data[0] as $k => $v) {
                                          $fieldValue .= "<li>$k: $v</li>";
                                      }
                                  }
                              }
                              $fieldValue .= '</ul>';
                          }
                          dd($fieldValue);
                      }

                  }



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
  */
});

Route::get('/test2', function () {
    $public_fields_string = 'id,about,app_links' .
        ',birthday,can_checkin,can_post,category,'
        . 'category_list,checkins,company_overview,contact_address,cover,culinary_team,' .
        'current_location,description,description_html,display_subtext,displayed_message_response_time,'
        . 'emails,engagement,fan_count,featured_video,founded,general_info,' .
        'global_brand_page_name,global_brand_root_id,has_added_app,has_whatsapp_number,' .
        'hometown,hours,impressum,influences,is_always_open,is_chain,is_community_page,' .
        'is_eligible_for_branded_content,is_owned,is_permanently_closed,is_published,is_unclaimed,' .
        'is_webhooks_subscribed,' .
        'link,location,' .
        'merchant_review_status,messenger_ads_quick_replies_type,mission,name,' .
        'name_with_location_descriptor,network,new_like_count,offer_eligible,' .
        'parking,pharma_safety_info,phone,'
        . 'press_contact,produced_by,products,' .
        'rating_count,restaurant_services,' .
        'schedule,single_line_address,start_info,' .
        'store_number,studio,talking_about_count,'
        . 'username,verification_status,voip_info,website,' .
        'were_here_count,whatsapp_number,written_by';

    $fs = explode(',', $public_fields_string);
    echo '[';
    foreach ($fs as $field) {
        echo '\'' . $field . '\'' . ',';
    }
    echo ']';
});