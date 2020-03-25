<?php

use Illuminate\Support\Facades\Route;
use TaivasAPM\Http\Middleware\Authenticate;

// By default these routes are prefixed with '/taivas', so a complete route looks like '/taivas/analytics/last-requests'

// Auth routes
Route::post('/auth/login', 'AuthController@login')->name('taivasapm.auth.login');

Route::group(['middleware' => Authenticate::class], function() {
    // Analytics routes
    Route::get('/analytics/recent-requests', 'AnalyticsController@recentRequests')->name('taivasapm.analytics.recent-requests');
    Route::get('/analytics/last-requests', 'AnalyticsController@lastRequests')->name('taivasapm.analytics.last-requests');
    Route::get('/analytics/longest-requests', 'AnalyticsController@longestRequests')->name('taivasapm.analytics.longest-requests');

    // Request detail view routes
    Route::get('/requests/{requestId}', 'RequestController@show')->name('taivasapm.request');
    Route::get('/requests/{requestId}/history', 'RequestController@history')->name('taivasapm.request.history');
});
