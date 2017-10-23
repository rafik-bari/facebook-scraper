<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css"/>
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">


    <!-- Styles -->
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.0/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <style>
        .hidden {
            display: none !important;
        }

        .container-full {
            margin: 0 auto;
            width: 90%;
        }

        .table {
            font-size: 0.99999999999999999em !important;
            font-weight: normal !important;
        }

        .table > caption + thead > tr:first-child > td, .table > caption + thead > tr:first-child > th, .table > colgroup + thead > tr:first-child > td, .table > colgroup + thead > tr:first-child > th, .table > thead:first-child > tr:first-child > td, .table > thead:first-child > tr:first-child > th {
            border-top: 0;
            padding: 0.44031111111111em;
        }

    </style>
</head>
<body>
<div id="app">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    &nbsp;
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @guest
                        <li><a href="{{ route('login') }}">Login</a></li>

                        @else

                            <li><a href="{{ route('settings.index') }}">Settings</a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                              style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                            @endguest
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')
</div>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>

<script
        src="https://code.jquery.com/jquery-3.2.1.min.js"
        integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"
        integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn"
        crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.0/js/responsive.bootstrap.min.js"></script>


<script src="{{asset('js/stream_table.min.js')}}"></script>
<script src="{{asset('js/mustache.min.js')}}"></script>


@include('partials.mustache_table_template')

<script>
    var data = [];
    var dummyPage = [];
    dummyPage['id'] = '123';
    dummyPage['name'] = 'test';
    data.push(dummyPage);
    var template = $.trim($("#template").html());
    Mustache.parse(template);

    var view = function (record, index) {
        return Mustache.render(template, {record: record, index: index});
    };
    var json_url = '/page';
    var p;
    var cbs = {
        before_add: function (data) {
            var new_data = [];
            if ('undefined' !== typeof (data.body)) {
                new_data = data.body;

            }
            var spn = $('span#processed');

            if (!isNaN(data.total))
                spn.text(parseInt(data.total));
            return new_data;
        },
        after_add: function () {

            if (this.data.length > 0) {
                $('input#purge').show();
                $('input#download').show();
                $('#resultsTableCont').show();
            }
        }
    };

    var options = {
        view: view,                  //View function to render table rows.
        data_url: json_url,  //Data fetching url
        stream_after: 10000,             //Start streaming after 2 secs
        fetch_data_limit: 300,       //Streaming data in batch of 500
        callbacks: cbs,
        pagination: {
            per_page_opts: [100, 250, 500, 1000, 2500, 5000],           //Per Page select box options. Default is [10, 25, 50].
            per_page: 100                    //Show number of record per page. Defalut 10.
        }
    };


    var st = StreamTable('#stream_table', options, data);

</script>
<script src="{{asset('js/custom.js')}}"></script>

</body>
</html>
