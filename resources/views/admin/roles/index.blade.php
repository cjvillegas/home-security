@extends('layouts.admin')
@section('content')
    <role-index :user="{{ $user }}"> </role-index>
@endsection
