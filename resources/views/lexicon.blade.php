<x-layout>
  <div class="lexicon">
    <h1>Lexique DWWM</h1>
    <form action="/add-word" method="POST">
      @csrf
      <label for="content">Nouveau mot</label>
      <input id="content" name="content" required />
      <button>Ajouter</button>
    </form>
    <div class="flow">
      <form>
        <label for='filter'>Filtrer par thème</label>
        <select id='filter' name='tag'>
          <option value="all">Tous les thèmes</option>
          @foreach($tags as $tag)
            <option
              value="{{ $tag->name }}"
              @if ($selectedTag === $tag->name) selected @endif
            >
              {{ $tag->name }}
            </option>
          @endforeach
        </select>
        <button>Filtrer</button>
      </form>
      <p>Le lexique contient {{ count($words) }} mots</p>
    </div>
    <table>
      @foreach($words as $word)
        <tr>
          <td>
            <form data-id="{{ $word->id }}" method="POST" action="update-word">
              @csrf
              <input name="id" type="hidden" value="{{ $word->id }}" />
              <input name="content" value="{{ $word->content }}" />
            </form>
          </td>
          <td class="tags">
            <div class="tag-list">
              @foreach($tags as $tag)
                <div
                  class="tag @if(in_array($tag->id, $word->tags)) active @endif"
                  data-csrf="{{ csrf_token() }}"
                  data-word-id="{{ $word->id }}"
                  data-tag-id="{{ $tag->id }}"
                >
                  {{ $tag->name }}
                </div>
              @endforeach
            </div>
          </td>
          <td class="delete-word">
            <form method="POST" action="delete-word">
              @csrf
              <input name="id" type="hidden" value="{{ $word->id }}" />
              <button>supprimer</button>
            </form>
          </td>
        </tr>
      @endforeach
    </table>
  </div>

  <script defer src="{{ asset('/js/lexicon.js') }}"></script>
</x-layout>
