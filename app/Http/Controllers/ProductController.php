<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function welcome()
    {
        return response()->json('App running');
    }
    public function index(Request $request)
    {
        $query = $request->query->all();

        $product = DB::table('products')
            ->join('stock', 'stock.product_id', '=', 'products.id')
            ->select('products.*', 'stock.stock')->paginate(5);

        return response()->json([
            'status' => 'ok',
            'code' => '200',
            'data' => [$product]
        ]);
    }
    public function search(Request $request)
    {
        $query = $request->query();
        $products = Product::where('product_name', 'like', "%" . $query['product_name'] . "%")
            ->orWhere('type_of_product', 'like', "%" . $query['type_of_product'] . "%")
            ->paginate(2);
        return response()->json($products);
    }

    public function getListProduct()
    {
        $product = DB::table('products')->select('id', 'product_name')->get();
        return response()->json(['message' => 'success', 'data' => $product]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required',
            'type_of_product' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => '400',
                'message' => $validator->errors()
            ])->setStatusCode(400);
        } else {
            try {
                $product = new Product();
                $product->createNewProduct($request);
                return response()->json([
                    'status' => 'ok',
                    'code' => '200',
                    'message' => 'new product added successfully'
                ]);
            } catch (\Exception $ex) {
                $errorMessage = $ex->getMessage();
                return response()->json([
                    'status' => 'error',
                    'code' => '500',
                    'message' => Str::limit($errorMessage, 89)
                ])->setStatusCode(500);
            }
        }
    }


    /**
     * Update the specified resource in storage.
     */
    function getId($id)
    {
        $checkId = Product::find($id);
        return $checkId;
    }
    public function update(Request $request, string $id)
    {
        $checkId = $this->getId($id);

        if (!$checkId) {
            return response()->json([
                'status' => 'error',
                'code' => '404',
                'message' => 'id not found',
            ])->setStatusCode(404);
        }


        $validator = Validator::make($request->all(), [
            'product_name' => 'required',
            'type_of_product' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => '400',
                'message' => $validator->errors()
            ])->setStatusCode(400);
        } else {
            $product = Product::where('id', $id)->update($request->all());
            return response()->json([
                'status' => 'ok',
                'code' => '200',
                'message' => 'product updated successfully'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $checkId = $this->getId($id);

        if (!$checkId) {
            return response()->json([
                'status' => 'error',
                'code' => '404',
                'message' => 'id not found',
            ])->setStatusCode(404);
        } else {
            try {
                $product = new Product();
                $product->deleteProduct($id);
                return response()->json([
                    'status' => 'ok',
                    'code' => '200',
                    'message' => 'product deleted successfully'
                ]);
            } catch (\Exception $ex) {
                $errorMessage = $ex->getMessage();
                return response()->json([
                    'status' => 'error',
                    'code' => '500',
                    'message' => $errorMessage
                    // 'message' => Str::limit($errorMessage, 89)
                ])->setStatusCode(500);
            }
        }
    }
}
