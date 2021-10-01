<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function all(Request $request)
    {
        //UNTUK ISI INPUTAN DI POSTMAN
        $id = $request->input('id');
        $limit = $request->input('limit');
        $name = $request->input('name');
        $description = $request->input('description');
        $tags = $request->input('tags');
        $categories = $request->input('categories');
        $price_from = $request->input('price_from');
        $price_to = $request->input('price_to');


        if($id){ //UNTUK AMBIL 1 BARANG MENGGUNAKAN ID (ambil satuan)

            $product = Product::with(['category','galeries'])->find($id);

            if($product){
                return response()->json(['sukses' => 'Data Produk berhasil diambil','data'=>$product]);
            }
            else{
                return response()->json(['gagal'=>'Data Produk tidak ada']);
            }
        }


        //UNTUK FILTER BERDASARKAN PARAMETER TERTENTU
        $product = Product::with(['category','galeries']);

        if($name){ //FILTER BERDASARKAN NAMA
            $product->where('name','like', '%' . $name . '%');
        }

        if ($description){ //FILTER BERDASARKAN DESKRIPSI
            $product->where('name','like', '%' . $description . '%');
        }

        if ($tags){ //FILTER BERDASARKAN TAGS
            $product->where('name','like', '%' . $tags . '%');
        }

        if ($price_from){ //FILTER HARGA DARI
            $product->where('price','>=',$price_from);
        }

        if ($price_to){     //FILTER HARGA KE
            $product->where('price','<=',$price_to);
        }

        if($categories){    //FILTER BERDASARKAN KATEGORI
            $product->where('categories',$categories);
        }

        return response()->json(['sukses'=>'Data Produk berhasil diambil',
                                 'data'=>$product->paginate($limit)]);
    }
}
