<?php
use Framework\Router\Route;

Route::get("/", function () {
    return view("home");
});

// Post CRUD routes
Route::get("/posts", "PostController@index");
Route::get("/posts/create", "PostController@create");
Route::post("/posts", "PostController@store");
Route::get("/posts/{id}", "PostController@show");
Route::get("/posts/{id}/edit", "PostController@edit");
Route::put("/posts/{id}", "PostController@update");
Route::delete("/posts/{id}", "PostController@destroy");

Route::socialAuth();