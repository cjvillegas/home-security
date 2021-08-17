@extends('layouts.admin')
@section('content')
    @php
        $pageData = new \stdClass();

        $pageData = json_encode($pageData);
    @endphp

    <app :page-data="{{ $pageData }}"></app>
@endsection
