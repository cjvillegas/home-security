<?php

Route::patch('monitorings/{monitoring}/status-change', 'Api\V1\MonitoringController@statusChange');
Route::resource('monitorings', 'Api\V1\MonitoringController');
