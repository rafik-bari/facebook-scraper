@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
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
                                <small style="text-decoration: none">Collected: <span id="processed">0</span></small>


                            </div>


                            <br>


                        </form>
                        <textarea style="display: none;" id="pendingIds">

                        </textarea>
                            <div class="input-group">
                                <input disabled="" id="scrape" onclick="event.preventDefault()" type="submit"
                                       value="Scrape">
                                <script>
                                    // Only works after `FB.init` is called
                                    function myFacebookLogin() {
                                        FB.login(function () {
                                        }, {scope: 'public_profile'});
                                    }
                                </script>
                                &nbsp;&nbsp;
                                <button onclick="myFacebookLogin();event.preventDefault()">Login with Facebook</button>

                            </div>

                    </div>
                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">Results</div>

                    <div class="panel-body">
                        <div class="form-group">

                        <textarea style="width:100%;min-height: 500px" id="results">

                        </textarea>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
