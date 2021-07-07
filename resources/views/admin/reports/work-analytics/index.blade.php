@extends('layouts.admin')
@section('content')
    <?php
        $pageData = new stdClass();
        $pageData->teams = $teams;
        $pageData->processes = $processes;
        $pageData->shifts = $shifts;

        $pageData = json_encode($pageData);
    ?>

    <work-analytics-index :page-data="{{ $pageData }}"></work-analytics-index>
@endsection
