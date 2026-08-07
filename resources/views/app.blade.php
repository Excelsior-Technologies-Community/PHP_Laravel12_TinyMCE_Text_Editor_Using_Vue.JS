<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title CRUD</title>

    <!-- 🔐 CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- ✅ Bootstrap 5 CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >

    <!-- ✅ TinyMCE CDN (NO API KEY) -->
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>

    <!-- ✅ Vite -->
    @vite('resources/js/app.js')
</head>
<body class="bg-light">

@if(session('success'))
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
        <div class="toast show align-items-center text-bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">{{ session('success') }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
@endif

<!-- 🔹 Main Container -->
<div class="container py-4">

    <div id="app"
         data-page="{{ $page }}"
         data-titles='@json($titles ?? [])'
         data-item='@json($item ?? null)'
         data-revisions='@json($revisions ?? [])'
         data-comments='@json($comments ?? [])'>
    </div>

</div>

<!-- ✅ Bootstrap JS (optional but recommended) -->
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous">
</script>

</body>
</html>
