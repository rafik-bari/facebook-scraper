<?php

namespace App\Http\Controllers;

use App\ApiError;
use App\Page;
use App\PageField;
use function Couchbase\defaultDecoder;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class PageController extends Controller
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
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $enabled_fields = [];
            $enabled_fields_ = PageField::where('enabled', true)->pluck('name')->toArray();
            $visible_fields = ['id', 'name'];
            foreach ($enabled_fields_ as $fieldname) {
                if (in_array($fieldname, $visible_fields)) {
                    $enabled_fields[] = $fieldname;
                }
            }
            $ids = [];
            $data = [];

            if($request->get('offset')) {
                $currentPage = ($request->get('offset')/1000) + 1;
            } else {
                $currentPage = 1;
            }


            // Make sure that you call the static method currentPageResolver()
            // before querying users
            Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });
            $rows = Page::paginate(1000);

            foreach ($rows as $page) {

                if (!in_array($page->id, $ids)) {
                    $ids[] = $page->id;
                    $singlePageData = [];
                    foreach ($enabled_fields as $field) {

                        switch ($field) {
                            case 'link':
                                $fieldValue = '<a href="' . $page->$field . '">visit url</a>';
                                continue;
                            default:
                                $fieldValue = $page->$field;
                                continue;
                        }
                        $singlePageData[$field] = $fieldValue;

                    }
                    $data[] = $singlePageData;
                }
            }

            $response = [
                'status' => true,
                'count' => count($data),
                'total' => Page::count(),
                'body' => $data,
                'next_page_url' => $rows->nextPageUrl()
            ];

            return \Response::json($response);
        }
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
