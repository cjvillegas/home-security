@extends('layouts.admin')
@section('content')
    <quality-control-index :user="{{ $user }}"></quality-control-index>
@endsection
