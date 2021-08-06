@extends('layouts.admin')
@section('content')
    @php
        $pageData = new \stdClass();
        $pageData->processSequence = $processSequence;
        $pageData->processes = $processes;

        $pageData = json_encode($pageData);
    @endphp

    <process-sequence-steps
        :page-data="{{ $pageData }}">
    </process-sequence-steps>
@endsection
