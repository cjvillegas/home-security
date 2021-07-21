@extends('layouts.admin')
@section('content')
    @php
        $pageData = new \stdClass();
        $pageData->user = $user;

        $pageData = json_encode($pageData);
    @endphp

    <settings-process-categories :user="{{ $user }}" />
@endsection
