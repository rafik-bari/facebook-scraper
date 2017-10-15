<?php

namespace App\Http\Controllers;

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
        $fields = [];
        $enabled_fields_ = PageField::where('enabled', true)->pluck('name')->toArray();
        $visible_fields = ['id', 'name'];
        foreach ($enabled_fields_ as $fieldname) {
            if (in_array($fieldname, $visible_fields)) {
                $fields[] = $fieldname;
            }
        }
        return view('home',compact('fields'));
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

    public function csvData()
    {
        $all = [];
        $pages = Page::all();
        $allowedKeys = $this->getArrayOfKeys();
        foreach ($pages as $row) {
            $_hundred_pages = unserialize($row->pages_data_chunk);
            foreach ($_hundred_pages as $page) {
                $readySinglePageData = $page;
                foreach ($allowedKeys as $k) {
                    if(!isset($readySinglePageData[$k])) {
                        $readySinglePageData[$k] = null;
                    }
                    $readySinglePageData[$k] = serialize($readySinglePageData[$k]);
                }
                $all[] = $readySinglePageData;
            }

        }
        dd($all);
        $headers = array(
            'Content-Type' => 'text/csv',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Disposition' => 'attachment; filename=Pages.csv',
            'Expires' => '0',
            'Pragma' => 'public',
        );

        $response = new StreamedResponse(function () {
            $allowedKeys = $this->getArrayOfKeys();
            // Open output stream
            $handle = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($handle, $allowedKeys);
            $res = [];

            $rows = Page::all();
            foreach ($rows as $row) {

                // Add a new row with data
                foreach (unserialize($row->pages_data_chunk) as $pageD) {
                    if (!in_array($pageD['id'], $res)) {
                        $res[] = $pageD['id'];
                        foreach ($allowedKeys as $k) {
                            if (isset($pageD[$k]) && is_array($pageD[$k])) {
                                $pageD[$k] = implode('/', $this->flatten($pageD[$k]));
                                exit;
                            }


                        }
                        //   fputcsv($handle, array_values($this->filterNonExistingKeys($pageD, $allowedKeys)));


                    };

                }

            }
            // Close the output stream
            fclose($handle);

        }, 200, $headers);

        return $response->send();


    }
}
