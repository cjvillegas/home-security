@extends('layouts.login')
@section('content')
    @php
        $pageData = new \stdClass();

        $pageData = json_encode($pageData);
    @endphp

    <employee-login></employee-login>
@endsection

