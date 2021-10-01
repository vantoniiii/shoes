<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TransactionItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $status = $request->input('status');

        if ($id) {      //Buat cari data transaksi satuan by ID

            $transactions = Transaction::with(['items.product'])->find($id);

            if ($transactions) {

                return response()->json(['message' => 'Data transaksi berhasil di ambil',
                                         'data' => $transactions,
                                         ]);
            }

            else {
                return response()->json(['message' => 'Data transaksi tidak ada'],404);
            }
        }

        $transactions = Transaction::with(['items.product'])->where('users_id','=', Auth::user()->id); //Buat liat transaksi user yg mana

        if($status){        //Buat cari data transaksi berdasarkan status
            $transactions->where('status',$status);
        }

        return response()->json(['message'=>'Data transaksi berhasil diambil',
                                 'data'=>$transactions->paginate($limit)]);

    }

    public function checkout(Request $request)
    {
            $request->validate([
                'items' => 'required|array',    //untuk validasi item dalam bentuk array
                'items.*.id' => 'exists:products,id',    //untuk validasi didalam array ada ID, dan ID ada produk nya
                'total_price' => 'required',
                'shipping_price' => 'required',
                'status' => 'required|in:Pending,Success,Cancelled,Failed,Shipped,Shipping', //CUSTOM STATUS
            ]);

            $transaction = Transaction::create([
                'users_id' => Auth::user()->id,
                'address' => $request->address,
                'total_price' => $request->total_price,
                'shipping_price' => $request->shipping_price,
                'status' => $request->status,
            ]);


            foreach ($request->items as $product) {
                TransactionItem::create([
                    'users_id' => Auth::user()->id,
                    'products_id' => $product['id'],
                    'transactions_id' => $transaction->id,
                    'quantity' => $product['quantity'],
                ]);
            }

            return \response()->json(['message' => 'Transaksi Berhasil',
                'data' => $transaction->load('items.product')]);



//        catch (\Exception $error){
//            return response()->json(['error'=>$error],500);
//
//        }
    }
}
