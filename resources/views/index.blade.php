<x-layout>
  <x-slot name="title">
    Quizz
  </x-slot>
    <div class="center">
      <form action="/new-quizz" method="POST">
        @csrf
        <div class="start-quizz">
          <h1>Démarrer un nouveau quizz</h1>
          <p>Choisissez vos thèmes :</p>
          <div class="tag-list">
            @foreach($tags as $tag)
              <div>
                <input type="checkbox" id="tag-{{ $tag->id }}" name="tags[]" value="{{ $tag->id }}">
                <label for="tag-{{ $tag->id }}">{{ $tag->name }}</label>
              </div>
            @endforeach
          </div>
          <button>Démarrer</button>
        </div>
      </form>
    </div>
  </div>
</x-layout>
