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
