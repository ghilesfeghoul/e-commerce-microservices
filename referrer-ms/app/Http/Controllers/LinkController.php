<?php

namespace App\Http\Controllers;

use App\Http\Resources\LinkResource;
use App\Models\Link;
use App\Models\LinkProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LinkController extends Controller
{
    public function index($id)
    {
        $links = Link::with('orders')->where('user_id', $id)->get();

        return LinkResource::collection($links);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return response([
                'error' => 'the field `products` is mandatory and must be a valid array'
            ], Response::HTTP_BAD_REQUEST);
        }
        $products_ids = $request->input('products');
        $ids = Product::whereIn('id', $products_ids)->pluck('id')->toArray();
        $diff = array_diff($products_ids, $ids);

        if (!empty($diff)) {
            return response([
                'error' => 'the field `products` contain invalid ids',
                'invalid_ids' => array_values($diff)
            ], Response::HTTP_BAD_REQUEST);
        }

        $link = Link::create([
            'user_id' => $request->user()->id,
            'code' => Str::random(6)
        ]);

        foreach ($request->input('products') as $product_id) {
            LinkProduct::create([
                'link_id' => $link->id,
                'product_id' => $product_id
            ]);
        }

        return $link;
    }

    public function show($code)
    {
        return Link::with('user', 'products')->where('code', $code)->first();
    }
}
