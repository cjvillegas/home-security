@extends('layouts.admin')
@section('content')
    <stock-items :user={{ $user }}></stock-items>
@endsection
