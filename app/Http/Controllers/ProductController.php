<?php

namespace App\Http\Controllers;


use App\Product;
use GuzzleHttp\Client;
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
    public function pullProducts(){
        $res = Http::withToken('eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjhhZjdhMDAxYmRhZGJmYjRlMGU4NzhmZmM5NTZiYzYxOTg2OTIwN2EyZWFlZDc4Y2ZlNGNhYTNmNjdjNGE2NTA5MTEzZTI3MGQ3Y2JiYTY5In0.eyJhdWQiOiI4Zjc4NjY2NC0wNTg5LTQ3MTgtODBkMS1lMTY4M2FmYmM3MjQiLCJqdGkiOiI4YWY3YTAwMWJkYWRiZmI0ZTBlODc4ZmZjOTU2YmM2MTk4NjkyMDdhMmVhZWQ3OGNmZTRjYWEzZjY3YzRhNjUwOTExM2UyNzBkN2NiYmE2OSIsImlhdCI6MTYwNzM1MzY1NSwibmJmIjoxNjA3MzUzNjU1LCJleHAiOjE2Mzg4ODk2NTUsInN1YiI6IjkxYmVmM2Q4LTcyYzYtNGQ5YS1hODAzLWUwZDEwMWVmODdhNiIsInNjb3BlcyI6WyJnZW5lcmFsLnJlYWQiXSwiYnVzaW5lc3MiOiI5MWJlZjNkOC03OTQ5LTQ0MjctOTg2NS1hYTI3MDVlNTYyOGQiLCJyZWZlcmVuY2UiOiIxMDAxMTEifQ.TCIEHRr02SixtoxdLovYBXu77xM69xUwOvlBlRqOz1kc0-t3A_U9MEYSEovlcpKypCuylTuOfmWSSUzeC7EaqrZTfn7OS2S7TfnuXb9FV6kYKeZaq9x5_XLNFAVSGhafv2dzs4-FOeo_nM39wlzWM_wVz4-HjqGQ_sUtXxtTogMlfBHF5xHZg0_3GsaL8Df9kRzIJoj-vt7tR1crNIq5qOb1xCT2RvtlAhlPqcHUbg7VaSyyBzgP2BhbaQ7uKFJP31FjjuHwPNnmv2oR2y-p1tx3o-0JeMuznqnsrH9HDPBFx2-QbJMFX-qR4X3AYykaYl4FpNbU5pGlG9DsZGJNr96Jgn3VcdQc4MxswJFJOgN1aYQj6VLtf3DCFb0YGUjiyKMRJViK5sIeJ8-T0styy3BqFZ2z2uuJ3bCJcEq6cPvmXBQoP0f2ctFGBkw0QSGz_6T5U_yJVmbtXxegISpffmW9NNclnVELSOR1j2ani3vz4HB5K4oMotsZUdNdVl6SfmUz0ETZc3gfQdcTbTjGMOxX7BivarRi2ckK9Qy8EbZIExT3MOYgh-L_Cw0cI7rAuAUvQYR38xwMawyqMVuvePUvlpFiuHEerbISxPIRNJbs3VVdlPi9DoKk0sjISVGm29O87Atm4glYXIZeBEJsIju9wOjSNtf0mYLqsIOt7Xc')->get('https://api.foodics.dev/v5/products');
        $data = json_decode($res->getBody()->getContents(),true);
        $items = $data['data'];
        foreach($items as $item)
        {
            $item['profit'] = $item['price'] - $item['cost'];
            Product::create($item);
        }
    }
}
