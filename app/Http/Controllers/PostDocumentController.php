<?php

namespace App\Http\Controllers;

use App\Models\PostDocument;
use App\Http\Requests\UpdatePostDocumentRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;

class PostDocumentController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PostDocument::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required'
        ]);
        $postDocument = $request->user()->postDocuments()->create($fields);

        return $postDocument;
    }
    /**
     * Display the specified resource.
     */
    public function show(PostDocument $postDocument)
    {
        return  $postDocument;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( Request $request, PostDocument $postDocument)
    {
        Gate::authorize('modify', $postDocument);
        
        $fields = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required'
        ]);
        $postDocument->update($fields);

        return  $postDocument;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PostDocument $postDocument)
    {
        Gate::authorize('modify', $postDocument);

        $postDocument->delete();
        return ['message' => "El archivo fue borrado"];
    }
}
