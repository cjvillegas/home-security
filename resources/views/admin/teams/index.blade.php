@extends('layouts.admin')
@section('content')
    <team-index :user="{{ $user }}"> </team-index>
@endsection
