<?php

namespace App\Http\Controllers;


use App\Product;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth_type:1', ['except' => ['index','expensive','profitable']]);
    }

    /**
     *
     */
    public function pullProducts()
    {
        $res = Http::withToken('eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjhhZjdhMDAxYmRhZGJmYjRlMGU4NzhmZmM5NTZiYzYxOTg2OTIwN2EyZWFlZDc4Y2ZlNGNhYTNmNjdjNGE2NTA5MTEzZTI3MGQ3Y2JiYTY5In0.eyJhdWQiOiI4Zjc4NjY2NC0wNTg5LTQ3MTgtODBkMS1lMTY4M2FmYmM3MjQiLCJqdGkiOiI4YWY3YTAwMWJkYWRiZmI0ZTBlODc4ZmZjOTU2YmM2MTk4NjkyMDdhMmVhZWQ3OGNmZTRjYWEzZjY3YzRhNjUwOTExM2UyNzBkN2NiYmE2OSIsImlhdCI6MTYwNzM1MzY1NSwibmJmIjoxNjA3MzUzNjU1LCJleHAiOjE2Mzg4ODk2NTUsInN1YiI6IjkxYmVmM2Q4LTcyYzYtNGQ5YS1hODAzLWUwZDEwMWVmODdhNiIsInNjb3BlcyI6WyJnZW5lcmFsLnJlYWQiXSwiYnVzaW5lc3MiOiI5MWJlZjNkOC03OTQ5LTQ0MjctOTg2NS1hYTI3MDVlNTYyOGQiLCJyZWZlcmVuY2UiOiIxMDAxMTEifQ.TCIEHRr02SixtoxdLovYBXu77xM69xUwOvlBlRqOz1kc0-t3A_U9MEYSEovlcpKypCuylTuOfmWSSUzeC7EaqrZTfn7OS2S7TfnuXb9FV6kYKeZaq9x5_XLNFAVSGhafv2dzs4-FOeo_nM39wlzWM_wVz4-HjqGQ_sUtXxtTogMlfBHF5xHZg0_3GsaL8Df9kRzIJoj-vt7tR1crNIq5qOb1xCT2RvtlAhlPqcHUbg7VaSyyBzgP2BhbaQ7uKFJP31FjjuHwPNnmv2oR2y-p1tx3o-0JeMuznqnsrH9HDPBFx2-QbJMFX-qR4X3AYykaYl4FpNbU5pGlG9DsZGJNr96Jgn3VcdQc4MxswJFJOgN1aYQj6VLtf3DCFb0YGUjiyKMRJViK5sIeJ8-T0styy3BqFZ2z2uuJ3bCJcEq6cPvmXBQoP0f2ctFGBkw0QSGz_6T5U_yJVmbtXxegISpffmW9NNclnVELSOR1j2ani3vz4HB5K4oMotsZUdNdVl6SfmUz0ETZc3gfQdcTbTjGMOxX7BivarRi2ckK9Qy8EbZIExT3MOYgh-L_Cw0cI7rAuAUvQYR38xwMawyqMVuvePUvlpFiuHEerbISxPIRNJbs3VVdlPi9DoKk0sjISVGm29O87Atm4glYXIZeBEJsIju9wOjSNtf0mYLqsIOt7Xc')->get('https://api.foodics.dev/v5/products');
        $data = json_decode($res->getBody()->getContents(),true);
        $items = $data['data'];
        foreach($items as $item)
        {
            $item['profit'] = $item['price'] - $item['cost'];
            $item['final_price'] = $item['price'];
            Product::create($item);
        }
    }

    public function index()
    {
        $products = Product::IsActive()->paginate(10);

        return response()->json(['data'=> $products, 'message'=>'Success'], 200);
    }

    public function profitable()
    {
        $products = DB::table('products')->where('is_active', 1)
            ->orderBy('profit', 'DESC')
            ->limit(5)
            ->get();

        return response()->json(['data'=> $products, 'message'=>'Success'], 200);
    }

    public function expensive()
    {
        $products = DB::table('products')->where('is_active', 1)
            ->orderBy('price', 'DESC')
            ->limit(5)
            ->get();

//        $products = DB::select('SELECT products.*, (price-IFNULL(discount,0)) as finalprice FROM products
//ORDER BY finalprice Desc LIMIT 5');

        return response()->json(['data'=> $products, 'message'=>'Success'], 200);
    }

    public function read($product)
    {
        $product = Product::findOrFail($product);
        return response()->json(['data'=> $product, 'message'=>'Success'], 200);
    }


    public function add(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'name'=>'required|string|unique:products',
            'cost'=>'required|regex:/^\d+(\.\d{1,2})?$/',
            'price'=>'required|regex:/^\d+(\.\d{1,2})?$/',
        ]);

        try {

            $inputs = $request->all();
            $inputs['profit']= $inputs['price'] - $inputs['cost'];
            $inputs['is_active']=1;
            //check if exist any discount
            if($inputs['d_type'] == 1 && $inputs['discount'] > 0)
            {
                $inputs['final_price'] = $inputs['price'] * (1 - $inputs['discount'] / 100);

            }elseif($inputs['d_type'] == 2 && $inputs['discount'] > 0){
                $inputs['final_price'] = $inputs['price'] - $inputs['discount'] ;
            }else{
                $inputs['final_price'] = $inputs['price'] ;
            }
            $inputs['profit']= $inputs['final_price'] - $inputs['cost'];

            $product = Product::create($inputs);
            //return successful response
            return response()
                ->json(['data' => $product, 'message' => 'CREATED Successfully'], 200);

        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Product Creation Failed!'], 409);
        }

    }

    public function update(Request $request, $product)
    {

        $product =  Product::findOrFail($product);

        //validate incoming request
        $this->validate($request, [
            'name'=>'required|string|unique:products,name,id'.$product->id,
            'cost'=>'sometimes|regex:/^\d+(\.\d{1,2})?$/',
            'price'=>'sometimes|regex:/^\d+(\.\d{1,2})?$/',
        ]);

        try {
            $inputs = $request->all();
            if(!isset($inputs['price'])){
                $inputs['price'] = $product->price;
            }
            if(!isset($inputs['cost'])){
                $inputs['cost'] = $product->cost;
            }
            //check if exist any discount
            if($inputs['d_type'] == 1 && $inputs['discount'] > 0)
            {
                $inputs['final_price'] = $inputs['price'] * (1 - $inputs['discount'] / 100);

            }elseif($inputs['d_type'] == 2 && $inputs['discount'] > 0){
                $inputs['final_price'] = $inputs['price'] - $inputs['discount'] ;
            }else{
                $inputs['final_price'] = $inputs['price'] ;
            }


            $inputs['profit']= $inputs['final_price'] - $inputs['cost'];

            $product->update($inputs);
            //return successful response
            return response()
                ->json(['data' => $product, 'message' => 'update Successfully'], 200);

        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Product update Failed!'], 409);
        }

    }

    public function delete($product)
    {

        $product = Product::findOrFail($product);
        try {

            $product->delete();
            //return successful response
            return response()
                ->json(['data' => $product, 'message' => 'deleted Successfully'], 410);

        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Product delete Failed!'], 409);
        }

    }
}

