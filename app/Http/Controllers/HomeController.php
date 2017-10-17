<?php

namespace App\Http\Controllers;

use App\Keyword;
use App\Page;
use App\PageField;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // ini_set('memory_limit', '-1');
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $fields = PageField::where('enabled', true)->pluck('name')->toArray();
        $has_pages = Page::count() > 0;
        $has_purgable_data = $has_pages > 0 || Keyword::count() > 0;

        return view('home', compact('fields', 'has_purgable_data', 'has_pages'));
    }

    public function getArrayOfKeys()
    {
        $s = PageField::where('enabled', 1)->pluck('name')->toArray();
        return $s;
        $filtered_keys = [];
        $row = Page::first();
        $pages = unserialize($row->pages_data_chunk);
        foreach ($pages as $page) {
            return array_keys($this->filterNonExistingKeys($page, $s));
        }

        //return PageField::pluck('name')->toArray();
    }

    public function filterNonExistingKeys($my_array, $allowed)
    {

        $filtered = array_filter(
            $my_array,
            function ($key) use ($allowed) {
                return in_array($key, $allowed);
            },
            ARRAY_FILTER_USE_KEY
        );
        return $filtered;
    }

    public function export($ids = array())
    {

    }

    public function flatten(array $array)
    {
        $return = array();
        array_walk_recursive($array, function ($a) use (&$return) {
            $return[] = $a;
        });
        return $return;
    }

    private $blob_fields = ['emails', 'start_info', 'engagement', 'voip_info', 'app_links', 'location'];

    public function shouldSerialize($field_name)
    {
        return in_array($field_name, $this->blob_fields);
    }

    public function csvData()
    {


        $header = \App\PageField::where('enabled', true)->get(['name'])->implode('name', ',');
        $enabled_fields = PageField::where('enabled', true)->pluck('name')->toArray();

        $csvData = [];
        $pages = Page::get($enabled_fields);


        Excel::create('Filename', function ($excel) {
            $excel->sheet('Sheetname', function ($sheet) {
                $enabled_fields = PageField::where('enabled', true)->pluck('name')->toArray();
                $pages = Page::get($enabled_fields);
                $csvData = [];
                foreach ($pages as $page) {
                    $csv_row = [];
                    foreach ($page->toArray() as $key => $value) {
                        if ($value && $this->shouldSerialize($key)) {
                            $csv_row[$key] = implode(',          ', unserialize($value));
                        } else {
                            $csv_row[$key] = $value;
                        }
                    }
                    $csvData[] = $csv_row;
                }
                $sheet->fromArray($csvData);
            });
        })->download('xls');


    }
}
