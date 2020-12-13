<?php
namespace App\Transformers;

use App\Product;
use League\Fractal;

class ProductTransformer extends Fractal\TransformerAbstract
{
    public function transform(Product $product)
    {
        return [
            'id'            => (int) $product->id,
            'name'          => $product->name,
            'price'         => (double) $product->price,
            'profit'        => (double) $product->profit,
            'discount'      => (double) $product->discount,
            'is_active'     => (int) $product->is_active,
            'd_type'        => (int) $product->d_type,
            'created_at'    => $product->created_at->format('d-m-Y'),
            'updated_at'    => $product->updated_at->format('d-m-Y'),
            'final_price'   => (double) $product->final_price,
        ];
    }
}
