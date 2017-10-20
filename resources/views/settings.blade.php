@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <p style="font-family: Arial,Microsoft Sans Serif,sans-serif">
                            Please sepecify the API key of your Facebook App.
                        </p>

                    </div>
                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        {{Form::model($appToken,['method'=>'put','route'=>['tokens.update',$appToken->id]])}}
                        <div class="form-group">
                            {{Form::text('app_id',null,['class'=>'form-control'])}}
                        </div>
                        <div class="form-group">
                            {{Form::text('app_secret',null,['class'=>'form-control'])}}
                        </div>

                        <div class="form-actions">
                            <input class="btn btn-success" type="submit" value="Update Tokens">
                        </div>
                        {{ Form::close()}}
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <p style="font-family: Arial,Microsoft Sans Serif,sans-serif">
                            Scraping Options.
                        </p>

                    </div>
                    <div class="panel-body">

                        {{Form::model($settingsRow,['route'=>['settings.update',$settingsRow],'class'=>'form2'] )}}
                        <table>
                            <tr>
                                <td>Scrape only pages with at least:&nbsp;</td>
                                <td>
                                    {{Form::number('minimum_fan_count',null,[
                                    'class'=>'form2Component'])}}
                                    &nbsp;fans
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    &nbsp;
                                </td>
                                <td>
                                    &nbsp;
                                </td>
                            </tr>
                            <tr>
                                <td>Skip pages without email? &nbsp;</td>

                                <td>
                                    <label for="must_have_email" class="custom-control custom-checkbox">
                                        {{Form::checkbox('must_have_email',null,null
                                        ,['style'=>'display:none','class'=>'hidden
                                        form2Component
                                        custom-control-input','id'=>'must_have_email'])}}
                                        <span class="custom-control-indicator"></span>
                                    </label>
                                </td>

                            </tr>
                            <tr>
                                <td>
                                    &nbsp;
                                </td>

                            </tr>
                        </table>
                        {{Form::close()}}


                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <p style="font-family: Arial,Microsoft Sans Serif,sans-serif">
                            Please select the fields you want to collect.
                        </p>

                    </div>

                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        {{ Form::open(['route' => ['settings.store']])}}
                        <table class="table table-bordered table-definition mb-5">
                            <thead class="table-warning ">
                            <tr>
                                <th></th>
                                <th>Field</th>
                                <th>Description</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($fields as $field)
                                <tr>
                                    <td>
                                        <label for="f{{$field->id}}" class="custom-control custom-checkbox">
                                            <input {{(bool)$field->enabled?'checked':''}}
                                                   value="{{$field->id}}"
                                                   style="display: none" id="f{{$field->id}}"
                                                   type="checkbox" class="update-field-value custom-control-input">
                                            <span class="custom-control-indicator"></span>
                                        </label>
                                    </td>
                                    <td> {{$field->name}}</td>
                                    <td>{{$field->description}}</td>
                                </tr>
                            @endforeach


                            </tbody>

                        </table>

                        {{ Form::close()}}


                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <p style="font-family: Arial,Microsoft Sans Serif,sans-serif">
                            Exceptions logged since the last time data was purged.
                        </p>

                    </div>

                    <div class="panel-body">


   <textarea readonly id="log" style="width: 100%;min-height:130px " name="logs"
             placeholder="">
       @foreach($api_errors as $error)
            {{$error->created_at.': '.$error->message}}
       @endforeach
                          </textarea>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
