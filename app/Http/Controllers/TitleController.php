<?php

namespace App\Http\Controllers;

use App\Models\Title;
use App\Models\TitleRevision;
use App\Models\TitleComment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

                $q->where(
                    'title',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'description',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($status = $request->input('status')) {

            $query->where(
                'status',
                $status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Favorite Filter
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled('favorite') &&
            $request->favorite !== 'all'
        ) {

            $query->where(
                'is_favorite',
                $request->favorite === '1'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date From
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {

            $query->whereDate(
                'created_at',
                '>=',
                $request->date_from
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date To
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_to')) {

            $query->whereDate(
                'created_at',
                '<=',
                $request->date_to
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        |
        | DEFAULT = ID ASC
        |
        | 1, 2, 3, 4, 5...
        |
        */

        $sort = $request->input(
            'sort',
            'id'
        );

        $direction = $request->input(
            'direction',
            'asc'
        );

        $allowedSorts = [
            'id',
            'title',
            'created_at',
            'status',
            'is_favorite',
        ];

        if (!in_array($sort, $allowedSorts)) {

            $sort = 'id';
        }

        if (!in_array($direction, ['asc', 'desc'])) {

            $direction = 'asc';
        }

        $query->orderBy(
            $sort,
            $direction
        );

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $titles = $query
            ->paginate(5)
            ->appends(
                $request->query()
            );

        return view('app', [
            'page' => 'index',
            'titles' => $titles,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('app', [
            'page' => 'create',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $data = $request->validate([

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'status' => [
                'nullable',
                'in:draft,published,archived',
            ],

            'is_favorite' => [
                'nullable',
                'boolean',
            ],

        ]);

        /*
        |--------------------------------------------------------------------------
        | Default Status
        |--------------------------------------------------------------------------
        */

        $data['status'] =
            $data['status']
            ?? 'draft';

        /*
        |--------------------------------------------------------------------------
        | Favorite
        |--------------------------------------------------------------------------
        */

        $data['is_favorite'] =
            $request->boolean(
                'is_favorite'
            );

        /*
        |--------------------------------------------------------------------------
        | Slug
        |--------------------------------------------------------------------------
        */

        $data['slug'] =
            Str::slug(
                $data['title']
            );

        /*
        |--------------------------------------------------------------------------
        | Create Title
        |--------------------------------------------------------------------------
        */

        $title =
            Title::create($data);

        /*
        |--------------------------------------------------------------------------
        | Initial Revision
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
            'system',

        ]);

        return redirect('/')
            ->with(
                'success',
                'Title created successfully!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT PAGE
    |--------------------------------------------------------------------------
    */

    public function editPage($id)
    {
        $title =
            Title::findOrFail($id);

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
            $comments,

        ]);
    }


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

        $data =
            $request->validate([

                'title' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'description' => [
                    'nullable',
                    'string',
                ],

                'status' => [
                    'nullable',
                    'in:draft,published,archived',
                ],

            ]);

        $data['slug'] =
            Str::slug(
                $data['title']
            );

        $title->update($data);

        /*
        |--------------------------------------------------------------------------
        | Revision
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
            'system',

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
        $title =
            Title::findOrFail($id);

        $title->delete();

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
                'array',
            ],

            'ids.*' => [
                'exists:title,id',
            ],

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
            $title,

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | AUTOSAVE
    |--------------------------------------------------------------------------
    */

    public function autosave(
        Request $request,
        $id
    ) {

        $title =
            Title::findOrFail($id);

        $data =
            $request->validate([

                'title' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'description' => [
                    'nullable',
                    'string',
                ],

                'status' => [
                    'nullable',
                    'in:draft,published,archived',
                ],

            ]);

        /*
        |--------------------------------------------------------------------------
        | Update Slug Only If Title Changed
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

        $title->update($data);

        /*
        |--------------------------------------------------------------------------
        | Revision
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
            'autosave',

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
                $title->status,

            ],

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
                'max:1000',
            ],

            'user_name' => [
                'nullable',
                'string',
                'max:255',
            ],

        ]);

        Title::findOrFail($id);

        $comment =
            TitleComment::create([

                'title_id' =>
                $id,

                'body' =>
                $request->body,

                'user_name' =>
                $request->user_name
                    ?? 'Anonymous',

            ]);

        if ($request->expectsJson()) {

            return response()->json([

                'success' =>
                true,

                'message' =>
                'Comment added successfully!',

                'comment' =>
                $comment,

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
    | TOGGLE FAVORITE
    |--------------------------------------------------------------------------
    */

    public function toggleFavorite($id)
    {
        $title =
            Title::findOrFail($id);

        $title->is_favorite =
            !$title->is_favorite;

        $title->save();

        return response()->json([

            'success' =>
            true,

            'is_favorite' =>
            $title->is_favorite,

            'message' =>
            $title->is_favorite
                ? 'Title added to favorites successfully!'
                : 'Title removed from favorites successfully!',

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DUPLICATE
    |--------------------------------------------------------------------------
    */

    public function duplicate($id)
    {
        $title =
            Title::findOrFail($id);

        $newTitle =
            $title->replicate();

        $newTitle->title =
            $title->title . ' - Copy';

        $newTitle->slug =
            Str::slug(
                $newTitle->title
            );

        /*
        |----------------------------------------------------------------------
        | Duplicate starts as non-favorite and draft
        |---------------------------------------------------------------------- 
        */

        $newTitle->is_favorite =
            false;

        $newTitle->status =
            'draft';

        $newTitle->save();

        /*
        |--------------------------------------------------------------------------
        | Revision
        |--------------------------------------------------------------------------
        */

        TitleRevision::create([

            'title_id' =>
            $newTitle->id,

            'title' =>
            $newTitle->title,

            'description' =>
            $newTitle->description,

            'changed_by' =>
            'duplicate',

        ]);

        return redirect('/')
            ->with(
                'success',
                'Title duplicated successfully!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | QUICK STATUS UPDATE
    |--------------------------------------------------------------------------
    */

    public function changeStatus(
        Request $request,
        $id
    ) {

        $request->validate([

            'status' => [
                'required',
                'in:draft,published,archived',
            ],

        ]);

        $title =
            Title::findOrFail($id);

        $title->status =
            $request->status;

        $title->save();

        return response()->json([

            'success' =>
            true,

            'status' =>
            $title->status,

            'message' =>
            'Title status changed to ' .
                ucfirst($title->status) .
                ' successfully!',

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | EXPORT CSV
    |--------------------------------------------------------------------------
    */

    public function exportCsv(
        Request $request
    ): StreamedResponse {

        $query =
            Title::query();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search = $request->input('search')) {

            $query->where(function ($q) use ($search) {

                $q->where(
                    'title',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'description',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if ($status = $request->input('status')) {

            $query->where(
                'status',
                $status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Favorite
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled('favorite') &&
            $request->favorite !== 'all'
        ) {

            $query->where(
                'is_favorite',
                $request->favorite === '1'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date From
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {

            $query->whereDate(
                'created_at',
                '>=',
                $request->date_from
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date To
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_to')) {

            $query->whereDate(
                'created_at',
                '<=',
                $request->date_to
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CSV ID ASC
        |--------------------------------------------------------------------------
        */

        $titles =
            $query
            ->orderBy(
                'id',
                'asc'
            )
            ->get();

        $filename =
            'titles-' .
            now()->format(
                'Y-m-d-H-i-s'
            ) .
            '.csv';

        return response()->streamDownload(

            function () use ($titles) {

                $handle =
                    fopen(
                        'php://output',
                        'w'
                    );

                /*
                |--------------------------------------------------------------------------
                | Header
                |--------------------------------------------------------------------------
                */

                fputcsv(
                    $handle,
                    [
                        'ID',
                        'Title',
                        'Status',
                        'Favorite',
                        'Slug',
                        'Created At',
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | Rows
                |--------------------------------------------------------------------------
                */

                foreach ($titles as $title) {

                    fputcsv(
                        $handle,
                        [

                            $title->id,

                            $title->title,

                            $title->status,

                            $title->is_favorite
                                ? 'Yes'
                                : 'No',

                            $title->slug,

                            $title->created_at,

                        ]
                    );
                }

                fclose($handle);
            },

            $filename,

            [
                'Content-Type' =>
                'text/csv',
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RESTORE
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
