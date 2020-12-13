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
        $res = Http::withToken(Env('Foodics_Token'))->get('https://api.foodics.dev/v5/products');
        $data = json_decode($res->getBody()->getContents(),true);
        $items = $data['data'];
        foreach($items as $item)
        {
            $item['profit'] = $item['price'] - $item['cost'];
            $item['final_price'] = $item['price'];
            Product::create($item);
        }
        return response()->json(['message'=>'data Retrieved successfully'], 200);
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

