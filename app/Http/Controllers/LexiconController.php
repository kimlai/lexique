<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LexiconController
{
  public function lexicon(Request $request)
  {
    $query = DB::table('words')->orderBy('id', 'desc');
    $tagFilter = $request->query('tag', 'all');

    if ($tagFilter !== "all") {
      $tagId = DB::select('SELECT id FROM tags WHERE name = ?', [$tagFilter])[0]->id;
      $query->whereExists(function ($subquery) use ($tagId) {
        $subquery->select(DB::raw(1))
          ->from('tag_word')
          ->whereColumn('tag_word.word_id', 'words.id')
          ->where('tag_word.tag_id', '=', $tagId);
      });
    }

    $words = $query->get();

    $tags = DB::select('SELECT * FROM tags');

    $wordTags = collect(DB::select('SELECT * FROM tag_word'))
      ->groupBy('word_id')
      ->map(function ($item) { return $item->pluck('tag_id');})
      ->toArray();

    foreach ($words as $word) {
      $word->tags = isset($wordTags[$word->id]) ? $wordTags[$word->id] : [];
    }

    return view(
      'lexicon',
      [
        'words' => $words,
        'tags' => $tags,
        'selectedTag' => $request->query('tag')
      ]
    );
  }

  public function addWord(Request $request)
  {
    $validatedData = $request->validate(["content" => "required"]);
    $content = $validatedData["content"];
    DB::insert("INSERT INTO words (content) VALUES (:content)", ["content" => $content]);
    return redirect('/lexique');
  }

  public function updateWord(Request $request)
  {
    $data = $request->validate(
      [
        "content" => "required",
        "id" => "required|integer|exists:words"
      ]
    );
    DB::update("UPDATE words SET content=? WHERE id=?", [$data["content"], (int) $data["id"]]);
    return response()->noContent();
  }

  public function deleteWord(Request $request)
  {
    $data = $request->validate(["id" => "required|integer|exists:words"]);
    DB::transaction(function () use ($data) {
      DB::delete("DELETE FROM tag_word WHERE word_id = ?", [$data["id"]]);
      DB::delete("DELETE FROM words WHERE id=?", [$data["id"]]);
    });
    return redirect('/lexique');
  }

  public function addTagToWord(Request $request)
  {
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
  }
}
