@extends('layouts.admin')
@section('content')
    @php
        $pageData = new \stdClass();
        $pageData->roles = $roles;
        $pageData->permissions = $permissions;

        $pageData = json_encode($pageData);
    @endphp

    <app :page-data="{{ $pageData }}"></app>
@endsection
