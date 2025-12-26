<!DOCTYPE html>
<html>
<head>
    <title>Title CRUD</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/js/app.js')
</head>
<body>

<div id="app"
     data-titles='@json($titles ?? [])'>
</div>

</body>
</html>
