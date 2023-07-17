<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleSize;
use Validator;
use Illuminate\Http\Request;

use App\Http\Resources\V1\ArticleResource;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::all();
        return $this->sendResponse(ArticleResource::collection($articles), 'Articles retrieved successfully.');

        // return ArticleResource::collection(Article::latest()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'ref' => 'required',
            'barcode' => 'required',
            'brand_id' => 'required',
            'size_details' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $article = Article::create($input);
        $details = [];
        foreach ($input["size_details"] as $articleSize) {
            array_push($details, new ArticleSize($articleSize));
        }
        $article->stock()->saveMany($details);

        return $this->sendResponse(new ArticleResource($article), 'Article created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        return new ArticleResource($article);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $article->name = $input['name'];
        $article->brand_id = $input['brand_id'];
        $article->save();

        return $this->sendResponse(new ArticleResource($article), 'Article updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        if ($article->delete()) {
            return response()->json(['message' => 'Success'], 204);
        }
        return response()->json(['message' => 'Not found'], 404);
    }
}
