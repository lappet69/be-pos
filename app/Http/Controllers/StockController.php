<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function indexStock(Stock $stock)
    {
        $get_product = DB::table('products')
            ->join('stock', 'stock.product_id', '=', 'products.id')
            ->select('*')->get();
        return $get_product;
    }

    public function addStock(Request $request, Stock $stock)
    {
        $checkStockProductId = $stock->where(['product_id' => $request->get('product_id')]);
        $current_stock = $checkStockProductId->first();
        // dd($current_stock->stock);
        if ($checkStockProductId->exists()) {
            try {
                $query = Stock::where(['product_id' => $request->get('product_id')])->update(['stock' => ($current_stock->stock + $request->get('stock'))]);
            } catch (\PDOException $e) {
                return response()->json($e->getMessage());
            }
        } else {
            try {
                $query = Stock::create(['product_id' => $request->get('product_id'), 'stock' => $request->get('stock')]);
            } catch (\PDOException $e) {
                return response()->json($e->getMessage());
            }
        }
        return response()->json(['status' => 'success', 'message' => 'stock updated']);
    }

    public function updateStock(Request $request, Stock $stock, $id)
    {

        $query = DB::table('stock')->where('id', $id)
            ->where('product_id', $request->get('product_id'))
            ->update(['stock' => $request->get('stock')]);
        return $query;
    }
}
