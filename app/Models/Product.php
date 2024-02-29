<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['product_name', 'type_of_product'];

    public function stock()
    {
        return $this->hasOne(Stock::class, 'foreign_key');
    }



    public function createNewProduct($payload)
    {
        DB::beginTransaction();
        try {
            // insert INTO table product
            $this->product_name = $payload->product_name;
            $this->type_of_product = $payload->type_of_product;
            $this->save();

            // insert into table stock
            $stock = new Stock();
            $stock->product_id = $this->id;
            $stock->stock = 0;
            $stock->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return throw $e;
        }
    }

    public function deleteProduct($id)
    {
        DB::beginTransaction();
        try {
            $stock = DB::table('stock')->where('product_id', $id)->delete();
            // delete product
            $this->destroy($id);

            // menghapus semua stock berdasarkan product_id

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return throw $e;
        }
    }

    // public func

}
