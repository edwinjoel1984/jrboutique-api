<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ArticleCollectionResource;
use App\Models\Article;
use App\Models\ArticleSize;
use Validator;
use Illuminate\Http\Request;

use App\Http\Resources\V1\ArticleResource;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::all();
        return new ArticleCollectionResource($articles);

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
            'brand_id' => 'required',
            'size_details' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        try {
            DB::beginTransaction();
            $article = Article::create($input);
            foreach ($input["size_details"] as $articleSize) {
                $articleSize["uniquecode"] = $this->generateBarcodeNumber();
                $articleSize["article_id"] = $article["id"];
                $newArticleSize =  ArticleSize::create($articleSize);
                $newArticleSize->transaction()->save(new Transaction(["order_id" => null,  "quantity" => $newArticleSize["quantity"], "type" => "ENTRADA DE INVENTARIO", "memo" => "Stock Inicial"]));
            }
            DB::commit();
            return $this->sendResponse(new ArticleResource($article), 'Article created successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError('Something went wrong.', $th, 422);
        }
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

    function generateBarcodeNumber()
    {
        $number = mt_rand(1000000000, 9999999999); // better than rand()

        // call the same function if the barcode exists already
        if ($this->barcodeNumberExists($number)) {
            return $this->generateBarcodeNumber();
        }

        // otherwise, it's valid and can be used
        return $number;
    }

    function barcodeNumberExists($number)
    {
        // query the database and return a boolean
        // for instance, it might look like this in Laravel
        return ArticleSize::where('uniquecode', '=', $number)->exists();
    }

    public function find_by_name(Request $request)
    {
        $input = $request->all();
        $result = DB::table('article_sizes as as2')
            ->select('as2.id', DB::raw('CONCAT(a.name, \' (Talla \', s.name, \') \') as name'), 's.name as size', 'as2.sale_price', 'as2.quantity as stock', 'as2.uniquecode')
            ->join('articles as a', 'as2.article_id', '=', 'a.id')
            ->join('sizes as s', 's.id', '=', 'as2.size_id')
            ->where('as2.quantity', '>', 0)
            ->where('a.name', 'like', "%" . $input['name'] . "%")
            ->get();

        return $result;
    }
    public function find_by_code(Request $request)
    {
        $input = $request->all();
        $result = DB::table('article_sizes as as2')
            ->select('as2.id', DB::raw('CONCAT(a.name, \' (Talla \', s.name, \') \') as name'), 's.name as size', 'as2.sale_price', 'as2.quantity as stock', 'as2.uniquecode')
            ->join('articles as a', 'as2.article_id', '=', 'a.id')
            ->join('sizes as s', 's.id', '=', 'as2.size_id')
            ->where('as2.quantity', '>', 0)
            ->where('as2.uniquecode', '=', $input['code'])
            ->get();

        return $result;
    }

    public function addArticleSizeToInventory(Request $request)
    {
        try {
            DB::beginTransaction();
            $articleSize = ArticleSize::find($request->article_size_id);
            $newQuantity = $articleSize['quantity'] + $request->quantity;
            // dd($newQuantity);
            $articleSize['quantity'] = $newQuantity;
            $articleSize->save();
            $articleSize->transaction()->save(new Transaction(["order_id" => null,  "quantity" => $request->quantity, "type" => "ENTRADA DE INVENTARIO", "memo" => "Adicion Manual de Producto"]));

            DB::commit();
            $articles = Article::all();
            return new ArticleCollectionResource($articles);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError('Something went wrong.', $th, 422);
        }
    }
}
