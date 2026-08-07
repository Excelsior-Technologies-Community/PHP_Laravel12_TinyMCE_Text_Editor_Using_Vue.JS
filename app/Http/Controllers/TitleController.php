<?php

namespace App\Http\Controllers;

use App\Models\Title;
use App\Models\TitleRevision;
use App\Models\TitleComment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TitleController extends Controller
{
    public function index(Request $request)
    {
        $query = Title::query();

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'asc');
        $allowedSorts = ['id', 'title', 'created_at', 'status'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');
        }

        $titles = $query->paginate(10)->appends($request->query());

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
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:draft,published,archived'
        ]);

        $data['slug'] = Str::slug($data['title']);

        $title = Title::create($data);

        TitleRevision::create([
            'title_id' => $title->id,
            'title' => $title->title,
            'description' => $title->description,
            'changed_by' => 'system'
        ]);

        return redirect('/')->with('success', 'Title created successfully!');
    }

    public function editPage($id)
    {
        $title = Title::findOrFail($id);
        $revisions = TitleRevision::where('title_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
        $comments = TitleComment::where('title_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('app', [
            'page' => 'edit',
            'item' => $title,
            'revisions' => $revisions,
            'comments' => $comments
        ]);
    }

    public function update(Request $request, $id)
    {
        $title = Title::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:draft,published,archived'
        ]);

        $oldData = $title->replicate()->toArray();

        $data['slug'] = Str::slug($data['title']);
        $title->update($data);

        TitleRevision::create([
            'title_id' => $title->id,
            'title' => $title->title,
            'description' => $title->description,
            'changed_by' => 'system'
        ]);

        return redirect('/')->with('success', 'Title updated successfully!');
    }

    public function destroy($id)
    {
        Title::findOrFail($id)->delete();
        return redirect('/')->with('success', 'Title deleted successfully!');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:title,id'
        ]);

        Title::whereIn('id', $request->ids)->delete();

        return redirect('/')->with('success', 'Selected titles deleted successfully!');
    }

    public function preview($id)
    {
        $title = Title::findOrFail($id);
        return view('app', [
            'page' => 'preview',
            'item' => $title
        ]);
    }

    public function autosave(Request $request, $id)
    {
        $title = Title::findOrFail($id);
        $title->update($request->only('title', 'description'));

        TitleRevision::create([
            'title_id' => $title->id,
            'title' => $title->title,
            'description' => $title->description,
            'changed_by' => 'autosave'
        ]);

        return response()->json(['success' => true, 'message' => 'Auto-saved']);
    }

    public function addComment(Request $request, $id)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
            'user_name' => 'nullable|string|max:255'
        ]);

        TitleComment::create([
            'title_id' => $id,
            'body' => $request->body,
            'user_name' => $request->user_name ?? 'Anonymous'
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Comment added!']);
        }

        return back()->with('success', 'Comment added!');
    }

    public function restore($id)
    {
        Title::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Title restored successfully!');
    }

    public function forceDelete($id)
    {
        Title::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Title permanently deleted!');
    }
}
