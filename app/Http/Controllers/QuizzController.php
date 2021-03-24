<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizzController
{
  public function themesSelection()
  {
    $tags = DB::select('SELECT * FROM tags');
    return view('index', ["tags" => $tags]);
  }

  public function createQuizz(Request $request)
  {
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
  }

  public function quizz($id)
  {
    $quizz = DB::select("SELECT * FROM quizz WHERE id = ?", [$id])[0];
    $wordIds = explode(',', $quizz->words);
    if ($quizz->current < count($wordIds)) {
      $currentWordId = $wordIds[$quizz->current];
      $word = DB::select("SELECT * FROM words WHERE id = ?", [$currentWordId])[0];
      return view('quizz', ["quizz" => $quizz, "word" => $word]);
    } else {
      return view('quizz_ended');
    }
  }

  public function nextWord($quizzId)
  {
    DB::update("UPDATE quizz SET current = current + 1 WHERE id = ?", [$quizzId]);
    return redirect('/quizz/'.$quizzId);
  }
}
