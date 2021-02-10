@extends('layouts.app')

@section('title') Пользователи @endsection

@section('content')
    <div class="container">
        <div class="">
            <users-list :users="{{ json_encode($users) }}"></users-list>
        </div>
    </div>
@endsection
