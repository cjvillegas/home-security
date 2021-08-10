@extends('layouts.admin')
@section('content')
    @php
        $pageData = new \stdClass();
        $pageData->teams = $teams;
        $pageData->shifts = $shifts;

        $pageData = json_encode($pageData);
    @endphp

    <app :page-data="{{ $pageData }}"></app>
@endsection
