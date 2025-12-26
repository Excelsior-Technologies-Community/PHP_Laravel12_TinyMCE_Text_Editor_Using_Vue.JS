# PHP_Laravel12_TinyMCE_Text_Editor_Using_Vue.JS

#Step 1 : install laravel 12 and Create Project 
```php
composer create-project laravel/laravel PHP_Laravel12_TinyMCE_Text_Editor_Using_Vue.JS
``` 
# Step 2: Migration Create 
```php
php artisan make:migration create_title_table
``` 
database/migrations/xxxx_xx_xx_create_title_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('title', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('title');
    }
};
``` 
# Run migration:
```php
php artisan migrate
``` 
# Step 3: Create Model For Title
```php
php artisan make:model Title
``` 
app/Models/Title.php
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
   protected $table = 'title';
    protected $fillable = ['title', 'description'];
}
``` 
# Step 4: Now Create Controller for Title Controller
```php
php artisan make:controller TitleController
```
```php
<?php

namespace App\Http\Controllers;

use App\Models\Title;
use Illuminate\Http\Request;

class TitleController extends Controller
{
   public function index()
{
    $titles = Title::orderBy('id', 'asc')->get();

    return view('app', [
        'page' => 'index',
        'titles' => $titles
    ]);
}

    public function create()
    {
        return view('app', [
            'page' => 'create'
        ]);
    }

    public function store(Request $request)
    {
        Title::create($request->only('title', 'description'));
        return redirect('/');
    }

    public function editPage($id)
    {
        $title = Title::findOrFail($id);
        return view('app', [
            'page' => 'edit',
            'item' => $title
        ]);
    }

    public function update(Request $request, $id)
    {
        $title = Title::findOrFail($id);
        $title->update($request->only('title', 'description'));
        return redirect('/');
    }

    public function destroy($id)
    {
        Title::findOrFail($id)->delete();
        return redirect('/');
    }
}
``` 
# Step 5: Create Route for web.php
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TitleController;



Route::get('/', [TitleController::class, 'index']);
Route::get('/create', [TitleController::class, 'create']);
Route::post('/store', [TitleController::class, 'store']);

Route::get('/edit/{id}', [TitleController::class, 'editPage']);
Route::post('/update/{id}', [TitleController::class, 'update']);

Route::get('/delete/{id}', [TitleController::class, 'destroy']);
``` 
# Step 6: Blade File (Vue Mount Only)
# resources/views/app.blade.php
```php
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

<!-- 🔹 Main Container -->
<div class="container py-4">

    <div id="app"
         data-page="{{ $page }}"
         data-titles='@json($titles ?? [])'
         data-item='@json($item ?? null)'>
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
``` 
# Step 7: Create vue.js file for index,create,edit in 
# resource/js/pages/Title folder
# resource/js/pages/Title/index.vue
```php
<template>
    <div class="card shadow-sm">

        <!-- 🔹 Header -->
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Title Management</h4>
            <a href="/create" class="btn btn-primary btn-sm">
                + Create New
            </a>
        </div>

        <!-- 🔹 Table -->
        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%">ID</th>
                        <th width="20%">Title</th>
                        <th>Description</th>
                        <th width="15%">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="item in titles" :key="item.id">
                        <td>{{ item.id }}</td>
                        <td class="fw-semibold">{{ item.title }}</td>

                        <!-- Render TinyMCE HTML -->
                        <td v-html="item.description"></td>

                        <td>
                            <a
                                :href="`/edit/${item.id}`"
                                class="btn btn-warning btn-sm me-1"
                            >
                                Edit
                            </a>

                            <a
                                :href="`/delete/${item.id}`"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete?')"
                            >
                                Delete
                            </a>
                        </td>
                    </tr>

                    <tr v-if="titles.length === 0">
                        <td colspan="4" class="text-center text-muted py-4">
                            No records found
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</template>

<script>
export default {
    data() {
        const el = document.getElementById('app')
        return {
            titles: JSON.parse(el.dataset.titles || '[]')
        }
    }
}
</script>
``` 
# resource/js/pages/create.vue
```php
<template>
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Create Title</h4>
        </div>

        <div class="card-body">
            <form method="POST" action="/store">
                <input type="hidden" name="_token" :value="csrf">

                <!-- Title -->
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input
                        type="text"
                        name="title"
                        class="form-control"
                        placeholder="Enter title"
                        required
                    >
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea id="editor"></textarea>
                    <input type="hidden" name="description" :value="description">
                </div>

                <!-- Actions -->
                <div class="d-flex gap-2">
                    <button class="btn btn-success">
                        Save
                    </button>
                    <a href="/" class="btn btn-secondary">
                        Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            description: '',
            csrf: document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content')
        }
    },
    mounted() {
        tinymce.init({
            selector: '#editor',
            height: 280,
            menubar: false,
            plugins: 'lists link code',
            toolbar: 'undo redo | bold italic | bullist numlist | code',
            setup: (editor) => {
                editor.on('Change KeyUp', () => {
                    this.description = editor.getContent()
                })
            }
        })
    }
}
</script>
``` 
# resource/js/pages/edit.vue
```php
<template>
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Edit Title</h4>
        </div>

        <div class="card-body">
            <form :action="`/update/${item.id}`" method="POST">
                <input type="hidden" name="_token" :value="csrf">

                <!-- Title -->
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input
                        type="text"
                        name="title"
                        v-model="title"
                        class="form-control"
                        required
                    >
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea id="editor"></textarea>
                    <input type="hidden" name="description" :value="description">
                </div>

                <!-- Actions -->
                <div class="d-flex gap-2">
                    <button class="btn btn-primary">
                        Update
                    </button>
                    <a href="/" class="btn btn-secondary">
                        Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        const el = document.getElementById('app')
        const item = JSON.parse(el.dataset.item)

        return {
            item,
            title: item.title,
            description: item.description,
            csrf: document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content')
        }
    },
    mounted() {
        tinymce.init({
            selector: '#editor',
            height: 280,
            menubar: false,
            plugins: 'lists link code',
            toolbar: 'undo redo | bold italic | bullist numlist | code',
            setup: (editor) => {
                editor.on('init', () => {
                    editor.setContent(this.description)
                })
                editor.on('Change KeyUp', () => {
                    this.description = editor.getContent()
                })
            }
        })
    }
}
</script>
``` 
# Step 8 : Vue Plugin Install 
```php
npm install @vitejs/plugin-vue --save-dev
``` 
# Step 9: vite.config.js FIX MOST IMPORTANT)

Open file:
vite.config.js
```php
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        vue(), // 👈 THIS IS REQUIRED
    ],
});

``` 
# Step 10 : Now Update For app.js file in resource/js folder
```php
import { createApp } from 'vue'

import Index from './Pages/Title/Index.vue'
import Create from './Pages/Title/Create.vue'
import Edit from './Pages/Title/Edit.vue'

const el = document.getElementById('app')

const page = el.dataset.page

let component = Index

if (page === 'create') component = Create
if (page === 'edit') component = Edit

createApp(component).mount('#app')
``` 
 # Step 11 : Now Update For welcome.blade.php file
 ```php
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
``` 
# Step 12 : Now run project in terminal
```php
npm run build 
npm run dev
php artisan serve
``` 
# Now Open Browser and paste this url http://127.0.0.1:8000/

<img width="1516" height="658" alt="image" src="https://github.com/user-attachments/assets/6e1d85d9-0f8a-4a97-8bdd-15010e2a3155" />
<img width="1515" height="397" alt="image" src="https://github.com/user-attachments/assets/3d5a931f-cc0d-4ecb-9371-ba4bc54c9133" />
<img width="1521" height="669" alt="image" src="https://github.com/user-attachments/assets/2947ecca-d97a-4c70-a479-c0240b8a00e5" />









