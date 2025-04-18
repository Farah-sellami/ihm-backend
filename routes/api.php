<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    UserController,
    CategorieController,
    ScategorieController,
    PosteController,
    OffreController
};

// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 🔐 Protected Routes (ajouter 'auth:api' uniquement là où c’est nécessaire)
Route::middleware(['auth:api'])->group(function () {
    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::post('users', [UserController::class, 'store']);
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
    Route::post('/users/{id}/block', [UserController::class, 'blockUser']);
    Route::post('/users/{id}/unblock', [UserController::class, 'unblockUser']);



    Route::post('/categories', [CategorieController::class, 'store']);
    Route::put('/categories/{categorie}', [CategorieController::class, 'update']);
    Route::delete('/categories/{categorie}', [CategorieController::class, 'destroy']);

    Route::post('/scategories', [ScategorieController::class, 'store']);
    Route::put('/scategories/{scategorie}', [ScategorieController::class, 'update']);
    Route::delete('/scategories/{scategorie}', [ScategorieController::class, 'destroy']);

    Route::post('/postes', [PosteController::class, 'store']);
    Route::put('/postes/{id}', [PosteController::class, 'update']);
    Route::delete('/postes/{id}', [PosteController::class, 'destroy']);
    Route::put('/postes/{id}/approve', [PosteController::class, 'approvePoste']);
    Route::put('/postes/{id}/disapprove', [PosteController::class, 'disapprovePoste']);
    Route::get('/postesfiltre', [PosteController::class, 'getPostesfiltred']);




    Route::post('/offres', [OffreController::class, 'store']);
    Route::put('/offres/{id}', [OffreController::class, 'update']);
    Route::delete('/offres/{id}', [OffreController::class, 'destroy']);
});

// 🔓 Public Routes
Route::get('/categories', [CategorieController::class, 'index']);
Route::get('/categories/{categorie}', [CategorieController::class, 'show']);
Route::get('/categorie/{id}/scategories', [CategorieController::class, 'getScategories']);

Route::get('/scategories', [ScategorieController::class, 'index']);
Route::get('/scategories/{scategorie}', [ScategorieController::class, 'show']);
Route::get('/categories/{categorieID}/subcategories', [ScategorieController::class, 'getByCategory']);

Route::get('/postes', [PosteController::class, 'index']);
Route::get('/postes/{id}', [PosteController::class, 'show']);

Route::get('/offres', [OffreController::class, 'index']);
Route::get('/offres/{id}', [OffreController::class, 'show']);
Route::get('/offres/poste/{posteId}', [OffreController::class, 'getOffresByPoste']);
