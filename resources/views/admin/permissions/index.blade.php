@extends('layouts.admin')
@section('content')
    <permission-index :user="{{ $user }}"> </permission-index>
@endsection
