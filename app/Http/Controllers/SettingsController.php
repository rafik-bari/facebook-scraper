<?php

namespace App\Http\Controllers;

use App\ApiError;
use App\AppToken;
use App\PageField;
use App\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fields = PageField::all();
        $appToken = AppToken::findOrFail(1);
        $settingsRow = Settings::findOrFail(1);
        if (!$appToken) {
            $appToken = new AppToken();
            $appToken->save();
        }

        $api_errors = ApiError::all();

        return view('settings', compact('fields', 'appToken','settingsRow','api_errors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $settingsRow = Settings::findOrFail(1);
        $settingsRow->fill($request->all());
        $settingsRow->must_have_email = (bool) ('on' === $request->get('must_have_email'));
        $settingsRow->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
