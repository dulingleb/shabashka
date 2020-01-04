<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ mix('css/app.css') }}">
  <title>Shabashki Demo</title>
</head>
<body>
  <div id="app">
    <app csrf-token='{{ csrf_token() }}'></app>
  </div>

  <script src="{{ mix('js/app.js') }}"></script>
</body>
</html> 