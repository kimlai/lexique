<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Lexique' }}</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/modern-css-reset/dist/reset.min.css" />
        <link href="{{ asset('/styles.css') }}" rel="stylesheet">
    </head>
    <body>
      <main>
        {{ $slot }}
      </main>
    </body>
</html>
