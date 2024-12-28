<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentifacation;



Route::resource('/panier', "App\Http\Controllers\PanierController");
Route::resource('/commande', "App\Http\Controllers\CommandeController");
Route::resource('/ligne', "App\Http\Controllers\LigneController");
Route::resource('/paiment', "App\Http\Controllers\PaimentController");
Route::resource('/blog', "App\Http\Controllers\BlogController");
Route::resource('/livraison', "App\Http\Controllers\LivraisonController");
Route::resource('/reclamation', "App\Http\Controllers\ReclamationController");
Route::resource('/question', "App\Http\Controllers\QuestionController");
Route::resource('/produit', "App\Http\Controllers\ProduitController");

Route::post("/login", "App\Http\Controllers\Authentifacation@login");
Route::post("/registre", "App\Http\Controllers\Authentifacation@register");
Route::post("/logout", "App\Http\Controllers\Authentifacation@logout");
Route::get("/afficher-count", "App\Http\Controllers\AffichProduitController@jibliyacounterdyalproduit");



Route::middleware("auth:sanctum")->group(function () {
    Route::controller(Authentifacation::class)->group(function () {
        Route::get('/user', "user");
        Route::post("/logout", "logout");
    });


});

Route::resource('/produit', "App\Http\Controllers\ProduitController");


