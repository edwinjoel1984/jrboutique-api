<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleSize;
use Validator;
use Illuminate\Http\Request;

use App\Http\Resources\V1\ArticleResource;
use App\Http\Resources\V1\ArticleSizeResource2;
use App\Models\Brand;
use App\Models\GroupSize;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::with(['brand', 'stock', 'stock.size'])->withSum('stock as stock_quantity', 'quantity')->orderBy('name')->get();
        return  ArticleResource::collection($articles);

        // return ArticleResource::collection(Article::latest()->paginate());
    }

    public function article_data()
    {
        return [
            'brands' => Brand::orderBy('name')->get(),
            'group_sizes' => GroupSize::all()
        ];
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
            $article = Article::with(['brand', 'stock', 'stock.size'])
                ->withSum('stock as stock_quantity', 'quantity')
                ->find($article->id);
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
        $article = Article::with(['brand', 'stock', 'stock.size'])
            ->withSum('stock as stock_quantity', 'quantity')
            ->find($article->id);
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
        $result = ArticleSize::with(['article', 'size'])
            ->where('quantity', '>', 0)
            ->whereHas('article', function ($query) use ($input) {
                $query->where('name', 'like', "%" . $input['name'] . "%");
            })
            ->get();

        return ArticleSizeResource2::collection($result);
    }
    public function find_by_code(Request $request)
    {
        $input = $request->all();
        $result = ArticleSize::with(['article', 'size'])
            ->where('quantity', '>', 0)
            ->where('uniquecode', $input['code'])
            ->get();

        return  ArticleSizeResource2::collection($result);
    }

    public function updateArticleSizeInInventory(Request $request)
    {
        try {
            DB::beginTransaction();
            $articleSize = ArticleSize::find($request->article_size_id);
            if ($request->action === "ADD") {
                $newQuantity = $articleSize['quantity'] + $request->quantity;
                $transaction = new Transaction([
                    "order_id" => null,
                    "quantity" => $request->quantity,
                    "type" => "ENTRADA DE INVENTARIO",
                    "memo" => "Adición Manual de Producto"
                ]);
            } else if ($request->action === "REMOVE") {
                $newQuantity = $articleSize['quantity'] - $request->quantity;
                $transaction = new Transaction([
                    "order_id" => null,
                    "quantity" => $request->quantity,
                    "type" => "OTRO",
                    "memo" => "Disminución manual de producto por error"
                ]);
            }

            if ($request->action === "UPDATE_PRICE") {
                $articleSize['sale_price'] = $request->price;
            } else {
                $articleSize['quantity'] = $newQuantity;
            }
            $articleSize->save();
            if (isset($transaction)) {
                $articleSize->transaction()->save($transaction);
            }

            DB::commit();
            $articles = Article::with(['brand', 'stock', 'stock.size'])->withSum('stock as stock_quantity', 'quantity')->orderBy('name')->get();
            return  ArticleResource::collection($articles);
        } catch (\Error $error) {
            DB::rollBack();
            return $this->sendError('Something went wrong.', $error, 422);
        }
    }
}
