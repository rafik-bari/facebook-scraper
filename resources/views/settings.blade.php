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
                        {{Form::model($appToken,['method'=>'put','route'=>['settings.update',$appToken->id]])}}
                        <div class="form-group">
                            {{Form::text('app_id',null,['class'=>'form-control'])}}
                        </div>
                        <div class="form-group">
                            {{Form::text('app_secret',null,['class'=>'form-control'])}}
                        </div>

                        <div class="form-actions">
                            <input class="btn btn-success" type="submit" value="Save Settings">
                        </div>
                        {{ Form::close()}}
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


            </div>
        </div>
    </div>

@endsection
