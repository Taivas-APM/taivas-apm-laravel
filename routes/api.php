<?php

use Illuminate\Support\Facades\Route;

// By default these routes are prefixed with '/taivasapm', so a complete route looks like '/taivasapm/analytics/last-requests'

// Analytics routes
Route::get('/analytics/recent-requests', 'AnalyticsController@recentRequests')->name('taivasapm.analytics.recent-requests');
Route::get('/analytics/last-requests', 'AnalyticsController@lastRequests')->name('taivasapm.analytics.last-requests');
Route::get('/analytics/longest-requests', 'AnalyticsController@longestRequests')->name('taivasapm.analytics.longest-requests');

// Request detail view routes
Route::get('/requests/{requestId}', 'RequestController@show')->name('taivasapm.request');
Route::get('/requests/{requestId}/history', 'RequestController@history')->name('taivasapm.request.history');
