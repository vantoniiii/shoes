<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $name = $request->input('name');
        $show_product = $request->input('show_product');

        if($id){ //UNTUK AMBIL 1 BARANG MENGGUNAKAN ID (ambil satuan)

            $category = ProductCategory::with(['products'])->find($id);

            if($category){
                return response()->json(['sukses' => 'Data kategori berhasil diambil','data'=>$category]);
            }
            else{
                return response()->json(['gagal'=>'Data kategori tidak ada']);
            }
        }

        $category = ProductCategory::query();

        if($name){ //FILTER BERDASARKAN NAMA
            $category->where('name','like', '%' . $name . '%');
        }

        if($show_product){
            $category->with('products');
        }

        return response()->json(['sukses'=>'Data kategori berhasil diambil',$category->paginate($limit)]);
    }
}
