@extends('layouts.app')

@section('content')
    <div class="container-full">
        <div class="row">
            <div class="col-md-6">
                <div class="actionsDiv panel-heading">Search for one or more keywords:</div>

                <div class=" ">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="post" action="" novalidate>


                        <div style="{{$in_progress?'':'display: none'}}" id="loadingDiv" class="form-group">
                            <img width="50" height="50" src="/img/loading.gif" title="loading" alt="loading">
                            <br>

                            <small style="text-decoration: none">Scraping In Progress...</small>
                        </div>
                        <div style="{{$last_scrape_completed && !$in_progress?'':'display: none'}}" id="scrapeCompleteDiv"
                             class="form-group">
                            <img width="50" height="50" src="/img/success.png" title="success" alt="success">
                            <br>
                            <small style="text-decoration: none">Finished Scraping
                                All Results
                            </small>
                            <br><br>
                            <input style="{{isset($has_pages) && $has_pages?'':'display:none;'}}" type="button"
                                   id="download" onclick="event.preventDefault()"
                                   value="Download CSV">&nbsp;
                            <input style="{{isset($has_purgable_data) && $has_purgable_data?'':'display:none;'}}color: red"
                                   type="button" id="purge" onclick="event.preventDefault()"
                                   value="Start New Search">
                        </div>

                        <div class="actionsDiv form-group">
                          <textarea id="keywords"
                                    style="{{$last_scrape_completed || $in_progress?'display: none;':''}} width: 100%;min-height:140px"
                                    name="keywords"
                                    placeholder="">@foreach($scrapedKeywords as $keyword){{$keyword->value}}
                              &#13;&#10;@endforeach</textarea>
                        </div>

                        <small style="text-decoration: none">Rows collected: <span id="processed">0</span></small>
                    </form>

                    <div style="{{$last_scrape_completed || $in_progress?'display: none;':''}}" class="actionsDiv input-group">
                        <input id="scrape" onclick="event.preventDefault()" type="submit"
                               value="Search">&nbsp;
                        <input type="hidden" value="/page" id="nextPageUrl">
                        <input type="hidden" value="/page" id="currentPageUrl">
                        <a style="display: none" id="hdn"></a>
                    </div>
                    <br><br>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-12">
                <div id="resultsTableCont" class="table-responsive">
                    <table id="stream_table" class='table'>
                        <thead>

                        <tr>
                            @if(isset($fields) && is_array($fields))
                                @foreach($fields as $field)
                                    <th style="cursor: pointer" data-sort="{{$field}}:desc">{{$field}} </th>
                                @endforeach
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

@endsection
