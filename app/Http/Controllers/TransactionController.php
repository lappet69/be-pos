<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function indexTransaction()
    {
        $product = DB::table('products')
            ->join('stock', 'stock.product_id', '=', 'products.id')
            ->join('transactions', 'transactions.product_id', '=', 'products.id')
            ->select('products.*', 'stock.stock', 'transactions.number_sold as jumlah_terjual')->paginate(5);

        return response()->json([
            'status' => 'ok',
            'code' => '200',
            'data' => [$product]
        ]);
    }
    public function createTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => '400',
                'message' => $validator->errors()
            ])->setStatusCode(400);
        }
        DB::beginTransaction();
        try {
            $stock_product = DB::table('stock')
                ->where('product_id', '=', $request->get('product_id'))
                ->select('stock.stock')->where('stock.stock', '>=', $request->get('amount'))->lockForUpdate();
            $current_stock = $stock_product->get()->first();
            // $aa->stock;
            if (!$stock_product->exists()) {
                return response()->json(['message' => 'something gone wrong'])->setStatusCode(400);
            }
            // update table stock
            $updatedStok = ($current_stock->stock - $request->get('amount'));
            $stock_product->update(['stock' =>  $updatedStok]);

            // create new transaction
            $transaction =  Transaction::create([
                'product_id' => $request->get('product_id'),
                'number_sold' => $request->get('amount'),
                'transaction_date' => $request->get('transaction_date'),
            ]);

            DB::commit();
            return response()->json([
                'status' => 'ok',
                'code' => '200',
                'message' => 'transaction successfully created',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'code' => '500',
                'message' => $e->getMessage(),
            ])->setStatusCode(500);
        }
    }
    public function updateTransaction(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'transaction_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => '400',
                'message' => $validator->errors()
            ])->setStatusCode(400);
        }
        DB::beginTransaction();
        try {

            // check stock product
            $stock_product = DB::table('stock')
                ->where('product_id', '=', $request->get('product_id'))
                ->select('stock.stock')->where('stock.stock', '>=', $request->get('amount'))->lockForUpdate();
            $current_stock = $stock_product->get()->first();

            if (!$stock_product->exists()) {
                return response()->json(['message' => 'something gone wrong'])->setStatusCode(400);
            }

            // get last number sold transactions with id
            $last_number_sold = DB::table('transactions')->where('id', $id)->select('number_sold')->first();


            // rollback stock
            $rollback_stock = ($current_stock->stock + $last_number_sold->number_sold);


            // update table stock
            $updatedStok = ($rollback_stock - $request->get('amount'));
            $stock_product->update(['stock' =>  $updatedStok]);



            // update transaction
            $transaction =  Transaction::where('id', $id)->update([
                // 'product_id' => $request->get('product_id'),
                'number_sold' => $request->get('amount'),
                'transaction_date' => $request->get('transaction_date'),
            ]);

            DB::commit();
            return response()->json([
                'status' => 'ok',
                'code' => '200',
                'message' => 'transaction successfully updated',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'code' => '500',
                'message' => $e->getMessage(),
            ])->setStatusCode(500);
        }
    }
    public function deleteTransaction(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'product_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => 'something gone wrong']);
        }

        DB::beginTransaction();
        try {

            // check stock product
            $stock_product = DB::table('stock')
                ->where('product_id', '=', $request->get('product_id'))
                ->select('stock.stock')->where('stock.product_id', '=', $request->get('product_id'))->lockForUpdate();
            $current_stock = $stock_product->get()->first();

            if (!$stock_product->exists()) {
                return response()->json(['message' => 'something gone wrong'])->setStatusCode(400);
            }

            // get last number sold transactions with id
            $last_number_sold = DB::table('transactions')->where('id', $id)->select('number_sold')->first();

            // rollback stock
            $rollback_stock = ($current_stock->stock + $last_number_sold->number_sold);


            // update table stock
            // $updatedStok = ($rollback_stock - $request->get('amount'));
            $stock_product->update(['stock' =>  $rollback_stock]);


            // update transaction
            $transaction =  Transaction::where(['id' => $id, 'product_id' => $request->get('product_id')])->delete();

            DB::commit();
            return response()->json([
                'status' => 'ok',
                'code' => '200',
                'message' => 'transaction successfully deleted',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'code' => '500',
                'message' => $e->getMessage(),
            ])->setStatusCode(500);
        }
    }
}
