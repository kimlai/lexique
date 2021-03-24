<?php

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

Route::get('/', function () {
  $tags = DB::select('SELECT * FROM tags');
  return view('index', ["tags" => $tags]);
});

Route::get('/lexique', function (Request $request) {
  $query = DB::table('words');
  if ($request->has("tag") && $request->query("tag") !== "all") {
    $tagId = DB::select('SELECT id FROM tags WHERE name = ?', [$request->query('tag')])[0]->id;
    $query->whereExists(function ($subquery) use ($tagId) {
      $subquery->select(DB::raw(1))
        ->from('tag_word')
        ->whereColumn('tag_word.word_id', 'words.id')
        ->where('tag_word.tag_id', '=', $tagId);
    });
  }
  $words = $query->orderBy('id', 'desc')->get();
  $tags = DB::select('SELECT * FROM tags');
  $wordTags = collect(DB::select('SELECT * FROM tag_word'))
    ->groupBy('word_id')
    ->map(function ($item) { return $item->pluck('tag_id');})
    ->toArray();
  foreach ($words as $word) {
    $word->tags = isset($wordTags[$word->id]) ? $wordTags[$word->id] : [];
  }
  return view('lexicon', ['words' => $words, 'tags' => $tags, 'selectedTag' => $request->query('tag')]);
});

Route::post('/add-word', function (Request $request) {
  $validatedData = $request->validate(["content" => "required"]);
  $content = $validatedData["content"];
  DB::insert("INSERT INTO words (content) VALUES (:content)", ["content" => $content]);
  return redirect('/lexique');
});

Route::post('update-word', function (Request $request) {
  $data = $request->validate(
    [
      "content" => "required",
      "id" => "required|integer|exists:words"
    ]
  );
  DB::update("UPDATE words SET content=? WHERE id=?", [$data["content"], (int) $data["id"]]);
  return response()->noContent();
});

Route::post('delete-word', function (Request $request) {
  $data = $request->validate(["id" => "required|integer|exists:words"]);
  DB::transaction(function () use ($data) {
    DB::delete("DELETE FROM tag_word WHERE word_id = ?", [$data["id"]]);
    DB::delete("DELETE FROM words WHERE id=?", [$data["id"]]);
  });
  return redirect('/lexique');
});

Route::post('add-tag', function (Request $request) {
  $data = $request->validate(
    [
      "word_id" => "required|integer|exists:words,id",
      "tag_id" => "required|integer|exists:tags,id",
    ]
  );
  DB::insert(
    "INSERT INTO tag_word (word_id, tag_id) VALUES (?, ?)",
    [$data["word_id"], $data["tag_id"]]
  );
  return response()->noContent();
});

Route::post('/new-quizz', function (Request $request) {
  $data = $request->validate(['tags' => 'required', 'tags.*' => 'integer']);
  $words = DB::table('words')
    ->join('tag_word', 'words.id', '=', 'tag_word.word_id')
    ->whereIn('tag_word.tag_id', $data["tags"])
    ->inRandomOrder()
    ->limit(30)
    ->get();
  $id = uniqid();
  $wordIds = implode(',', $words->pluck('word_id')->toArray());
  DB::insert(
    "INSERT INTO quizz (id, words, current) VALUES (?, ?, ?)",
    [$id, $wordIds, 0]
  );
  return redirect('/quizz/'.$id);
});

Route::get('/quizz/{id}', function ($id) {
  $quizz = DB::select("SELECT * FROM quizz WHERE id = ?", [$id])[0];
  $wordIds = explode(',', $quizz->words);
  if ($quizz->current < count($wordIds)) {
    $currentWordId = $wordIds[$quizz->current];
    $word = DB::select("SELECT * FROM words WHERE id = ?", [$currentWordId])[0];
    return view('quizz', ["quizz" => $quizz, "word" => $word]);
  } else {
    return view('quizz_ended');
  }
});

Route::post('/quizz/{id}/next', function ($id) {
  DB::update("UPDATE quizz SET current = current + 1 WHERE id = ?", [$id]);
  return redirect('/quizz/'.$id);
});
