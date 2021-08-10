@extends('layouts.admin')
@section('content')
    <stock-levels :user={{ $user }}></stock-levels>
@endsection
