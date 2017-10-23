<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Settings;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    public function authenticated()
    {
        $t1 = \App\Page::truncate();
        $t2 = \App\Keyword::truncate();
        $t3 = \App\ApiError::truncate();
        $t4 = \App\ScrapedKeyword::truncate();
        $settingsRow = Settings::find(1);
        $settingsRow->last_scrape_completed = false;
        if ($settingsRow->save()) {
            return redirect()->intended('/home');
        }

    }


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
