<?php

use App\Http\Controllers\LexiconController;
use App\Http\Controllers\QuizzController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/lexique', [LexiconController::class, 'lexicon']);
Route::post('/add-word', [LexiconController::class, 'addWord']);
Route::post('/update-word', [LexiconController::class, 'updateWord']);
Route::post('delete-word', [LexiconController::class, 'deleteWord']);
Route::post('add-tag', [LexiconController::class, 'addTagToWord']);

Route::get('/', [QuizzController::class, 'themesSelection']);
Route::post('/new-quizz', [QuizzController::class, 'createQuizz']);
Route::get('/quizz/{id}', [QuizzController::class, 'quizz']);
Route::post('/quizz/{id}/next', [QuizzController::class, 'nextWord']);
