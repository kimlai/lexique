<x-layout>
  <x-slot name="title">
    Quizz
  </x-slot>
    <div class="center">
      <form action="/quizz/{{ $quizz->id }}/next" method="POST">
        @csrf
        <div class="quizz">
          <h1>C'est quoi ?</h1>
          <p>{{ $word->content }}</p>
          <button>Suivant</button>
          <div>
            <a href="/lexique">Ajouter des entrées au lexique</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</x-layout>
