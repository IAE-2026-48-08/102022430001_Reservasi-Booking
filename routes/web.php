<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Reservation Booking Service'
    ]);
});

Route::get('/api-docs', function () {
    $path = storage_path('api-docs/api-docs.json');
    if (file_exists($path)) {
        return response()->file($path, [
            'Content-Type' => 'application/json'
        ]);
    }
    return response()->json(['openapi' => '3.0.0', 'paths' => []]);
});

Route::get('/openapi.json', function () {
    $path = storage_path('api-docs/api-docs.json');
    if (file_exists($path)) {
        return response()->file($path, [
            'Content-Type' => 'application/json'
        ]);
    }
    return response()->json(['openapi' => '3.0.0', 'paths' => []]);
});

Route::get('/swagger-ui.html', function () {
    $path = storage_path('api-docs/api-docs.json');
    if (file_exists($path)) {
        return response()->file($path, [
            'Content-Type' => 'application/json'
        ]);
    }
    return response()->json(['openapi' => '3.0.0', 'paths' => []]);
});

Route::get('/docs', function () {
    $path = storage_path('api-docs/api-docs.json');
    if (file_exists($path)) {
        return response()->file($path, [
            'Content-Type' => 'application/json'
        ]);
    }
    return response()->json(['openapi' => '3.0.0', 'paths' => []]);
})->name('l5-swagger.default.docs');

Route::redirect('/playground', '/graphql-playground');
Route::redirect('/graphiql', '/graphql-playground');