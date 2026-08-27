<?php

namespace App\Http\Controllers;

use App\Models\Title;
use App\Models\TitleRevision;
use App\Models\TitleComment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TitleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Title::query();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search = $request->input('search')) {

            $query->where(function ($q) use ($search) {

                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");

            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $sort = $request->input('sort', 'id');

        $direction = $request->input('direction', 'asc');

        $allowedSorts = [
            'id',
            'title',
            'created_at',
            'status'
        ];

        if (in_array($sort, $allowedSorts)) {

            $query->orderBy(
                $sort,
                $direction === 'desc'
                    ? 'desc'
                    : 'asc'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $titles = $query
            ->paginate(10)
            ->appends($request->query());

        return view('app', [
            'page' => 'index',
            'titles' => $titles
        ]);
    }

<<<<<<< HEAD
public function create()
=======

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
>>>>>>> development
    {
        return view('app', [
            'page' => 'create'
        ]);
    }

<<<<<<< HEAD
public function store(Request $request)
=======

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
>>>>>>> development
    {
        $data = $request->validate([

            'title' => [
                'required',
                'string',
                'max:255'
            ],

            'description' => [
                'nullable',
                'string'
            ],

            'status' => [
                'nullable',
                'in:draft,published,archived'
            ]

        ]);

        /*
        |--------------------------------------------------------------------------
        | Default Status
        |--------------------------------------------------------------------------
        */

        $data['status'] =
            $data['status'] ?? 'draft';

        /*
        |--------------------------------------------------------------------------
        | Generate Slug
        |--------------------------------------------------------------------------
        */

        $data['slug'] =
            Str::slug($data['title']);

        /*
        |--------------------------------------------------------------------------
        | Create Title
        |--------------------------------------------------------------------------
        */

        $title =
            Title::create($data);

        /*
        |--------------------------------------------------------------------------
        | Create Initial Revision
        |--------------------------------------------------------------------------
        */

        TitleRevision::create([

            'title_id' =>
                $title->id,

            'title' =>
                $title->title,

            'description' =>
                $title->description,

            'changed_by' =>
                'system'

        ]);

        return redirect('/')
            ->with(
                'success',
                'Title created successfully!'
            );
    }

<<<<<<< HEAD
public function editPage($id)
=======

    /*
    |--------------------------------------------------------------------------
    | EDIT PAGE
    |--------------------------------------------------------------------------
    */

    public function editPage($id)
>>>>>>> development
    {
        $title =
            Title::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Revisions
        |--------------------------------------------------------------------------
        */

        $revisions =
            TitleRevision::where(
                'title_id',
                $id
            )
            ->orderBy(
                'created_at',
                'desc'
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Comments
        |--------------------------------------------------------------------------
        */

        $comments =
            TitleComment::where(
                'title_id',
                $id
            )
            ->orderBy(
                'created_at',
                'desc'
            )
            ->get();

        return view('app', [

            'page' =>
                'edit',

            'item' =>
                $title,

            'revisions' =>
                $revisions,

            'comments' =>
                $comments

        ]);
    }

<<<<<<< HEAD
public function update(Request $request, $id)
    {
        $title = Title::findOrFail($id);
=======
>>>>>>> development

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        $id
    ) {

        $title =
            Title::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $data =
            $request->validate([

                'title' => [
                    'required',
                    'string',
                    'max:255'
                ],

                'description' => [
                    'nullable',
                    'string'
                ],

                'status' => [
                    'nullable',
                    'in:draft,published,archived'
                ]

            ]);

        /*
        |--------------------------------------------------------------------------
        | Generate Slug
        |--------------------------------------------------------------------------
        */

        $data['slug'] =
            Str::slug(
                $data['title']
            );

        /*
        |--------------------------------------------------------------------------
        | Update Title
        |--------------------------------------------------------------------------
        */

        $title->update($data);

        /*
        |--------------------------------------------------------------------------
        | Create Revision
        |--------------------------------------------------------------------------
        */

        TitleRevision::create([

            'title_id' =>
                $title->id,

            'title' =>
                $title->title,

            'description' =>
                $title->description,

            'changed_by' =>
                'system'

        ]);

        return redirect('/')
            ->with(
                'success',
                'Title updated successfully!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        Title::findOrFail($id)
            ->delete();

        return redirect('/')
            ->with(
                'success',
                'Title deleted successfully!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | BULK DELETE
    |--------------------------------------------------------------------------
    */

    public function bulkDestroy(
        Request $request
    ) {

        $request->validate([

            'ids' => [
                'required',
                'array'
            ],

            'ids.*' => [
                'exists:title,id'
            ]

        ]);

        Title::whereIn(
            'id',
            $request->ids
        )->delete();

        return redirect('/')
            ->with(
                'success',
                'Selected titles deleted successfully!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | PREVIEW
    |--------------------------------------------------------------------------
    */

    public function preview($id)
    {
        $title =
            Title::findOrFail($id);

        return view('app', [

            'page' =>
                'preview',

            'item' =>
                $title

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | AUTOSAVE
    |--------------------------------------------------------------------------
    |
    | This is used by the Vue TinyMCE editor.
    |
    | Saves:
    | - title
    | - description
    | - status
    |
    */

    public function autosave(
        Request $request,
        $id
    ) {

        $title =
            Title::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Validate Autosave Data
        |--------------------------------------------------------------------------
        */

        $data =
            $request->validate([

                'title' => [
                    'required',
                    'string',
                    'max:255'
                ],

                'description' => [
                    'nullable',
                    'string'
                ],

                'status' => [
                    'nullable',
                    'in:draft,published,archived'
                ]

            ]);

        /*
        |--------------------------------------------------------------------------
        | Update Slug If Title Changes
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['title']) &&
            $data['title'] !== $title->title
        ) {

            $data['slug'] =
                Str::slug(
                    $data['title']
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Update Database
        |--------------------------------------------------------------------------
        */

        $title->update($data);

        /*
        |--------------------------------------------------------------------------
        | Create Autosave Revision
        |--------------------------------------------------------------------------
        */

        TitleRevision::create([

            'title_id' =>
                $title->id,

            'title' =>
                $title->title,

            'description' =>
                $title->description,

            'changed_by' =>
                'autosave'

        ]);

        return response()->json([

            'success' =>
                true,

            'message' =>
                'Auto-saved successfully.',

            'data' => [

                'id' =>
                    $title->id,

                'title' =>
                    $title->title,

                'description' =>
                    $title->description,

                'status' =>
                    $title->status

            ]

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | ADD COMMENT
    |--------------------------------------------------------------------------
    */

    public function addComment(
        Request $request,
        $id
    ) {

        $request->validate([

            'body' => [
                'required',
                'string',
                'max:1000'
            ],

            'user_name' => [
                'nullable',
                'string',
                'max:255'
            ]

        ]);

        /*
        |--------------------------------------------------------------------------
        | Check Title Exists
        |--------------------------------------------------------------------------
        */

        Title::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Create Comment
        |--------------------------------------------------------------------------
        */

        $comment =
            TitleComment::create([

                'title_id' =>
                    $id,

                'body' =>
                    $request->body,

                'user_name' =>
                    $request->user_name
                        ?? 'Anonymous'

            ]);

        /*
        |--------------------------------------------------------------------------
        | JSON Response
        |--------------------------------------------------------------------------
        */

        if ($request->expectsJson()) {

            return response()->json([

                'success' =>
                    true,

                'message' =>
                    'Comment added successfully!',

                'comment' =>
                    $comment

            ]);
        }

        return back()
            ->with(
                'success',
                'Comment added successfully!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | RESTORE SOFT DELETED TITLE
    |--------------------------------------------------------------------------
    */

    public function restore($id)
    {
        Title::onlyTrashed()
            ->findOrFail($id)
            ->restore();

        return back()
            ->with(
                'success',
                'Title restored successfully!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | FORCE DELETE
    |--------------------------------------------------------------------------
    */

    public function forceDelete($id)
    {
        Title::onlyTrashed()
            ->findOrFail($id)
            ->forceDelete();

        return back()
            ->with(
                'success',
                'Title permanently deleted!'
            );
    }
}