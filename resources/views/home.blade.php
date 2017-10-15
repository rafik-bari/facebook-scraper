@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Dashboard</div>

                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="post" action="" novalidate>
                            <div class="form-group">
                          <textarea id="keywords" style="width: 100%;min-height:130px " name="keywords"
                                    placeholder="Type some keywords ( One per line)">
                          </textarea>
                                <textarea id="log" style="width: 100%;min-height:130px " name="logs"
                                          placeholder="">
                          </textarea>
                                <small style="text-decoration: none">Collected: <span id="processed">0</span></small>


                            </div>


                            <br>


                        </form>

                        <div class="input-group">
                            <input disabled="" id="scrape" onclick="event.preventDefault()" type="submit"
                                   value="Scrape"> &nbsp;
                            <input type="button" id="purge" onclick="event.preventDefault()"
                                   value="Purge All Data">
                            <input type="hidden" value="/page" id="nextPageUrl">
                            <input type="hidden" value="/page" id="currentPageUrl">

                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 ">
                        <table id="stream_table" class='table table-responsive'>
                            <thead>
                            <tr>

                                @if(isset($fields) && is_array($fields))
                                    @foreach($fields as $field)
                                        <th>{{$field}}</th>
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
    </div>

@endsection
