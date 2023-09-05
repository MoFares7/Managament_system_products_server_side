<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ProductController extends Controller
{

    public function index()
    {
        return Product::select('id','title','description','image')->get();
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required|max50',
            'description'=>'required',
            'image'=>'required|image',
        ]);

        $imageName = Str::random().'.'.$request->image->getClientOrginalExtension();
        Storage::disk('public')->putFileAs('product/image', $request->image, $imageName);
        Product::create($request->post(), ['image'=> $request->image]);
        return response()->json([
            'message' => 'Item added successfuly'
        ]);
    }

    public function show(Product $product)
    {
        //
    }

    public function edit(Product $product)
    {
        //
    }

    public function update(Request $request, Product $product): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'title'=>'required|max50',
            'description'=>'required',
            'image'=>'nullable',
        ]);

        $product->fill($request->post())->update();
        if($request->hasFile('image')){
            if($product->image){
                $exist = Storage::disk('public')->exists("product/image/{$request->image}");
                if($exist){
                    Storage::disk('public')->delete("product/image/{$request->image}");
                }
            }
        }

        $imageName = Str::random().'.'.$request->image->getClientOrginalExtension();
        Storage::disk('public')->putFileAs('product/image', $product->image, $imageName);
        $product->image = $imageName;
        $product->save();
        return response()->json([
            'message' => 'Item updated successfully'
        ]);
    }

    public function destroy(Product $product)
    {
        if($product->image){
            $exist = Storage::disk('public')->exists("product/image/{$product->image}");
            if($exist){
              Storage::disk('public')->delete("product/image/{$product->image}");

            }
        }
        $product->delete();
        return response()->json([
            'message' => 'the Items is Deleted'
        ]);
    }
}
