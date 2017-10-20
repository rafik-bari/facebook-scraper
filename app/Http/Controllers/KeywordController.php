<?php

namespace App\Http\Controllers;

use App\Jobs\ScrapePaginateKeyword;
use App\Keyword;
use App\ScrapedKeyword;
use Illuminate\Http\Request;

class KeywordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $keywords = $request->get('keywords');
        foreach (array_chunk($keywords, 49) as $keywords_chunk) {

            $kw = new Keyword();
            $kw->keywords_chunk = base64_encode(serialize($keywords_chunk));

            if ($kw->save()) {
                // create a job to process this keyword
                foreach ($keywords_chunk as $k) {
                    $kk = ScrapedKeyword::where('value', $k);
                    if (!$kk->exists()) {
                        $scrapedKeyword = new \App\ScrapedKeyword();
                        $scrapedKeyword->value = $k;
                        $scrapedKeyword->save();
                    }

                }
                ScrapePaginateKeyword::dispatch($kw);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
