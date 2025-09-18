<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function __construct(){
        $this->Product = new product();
    }

    public function index(){
        $data = $this->Product::all();

        if ($data->isEmpty()) {
            return response()->json(['data'=>'','message' => 'No products found'], 404);
        } else {
            return response()->json(['data'=>$data,'message' => 'products found'], 200);
        }    
    }

   public function store(Request $request)
   {
    // echo "<pre>";print_r($request->all());die;
    // echo "<pre>";print_r($_FILES);die;
    $validator = Validator::make($request->all(), [
        'name'        => 'required|string|max:255',
        'description' => 'required|string',
        // 'image'       => 'required|image|mimes:jpg,jpeg,png,gif',
    ]);

    if ($validator->fails()) {
        if ($validator->errors()->has('name')) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first('name')
            ], 422);
        }

        if ($validator->errors()->has('description')) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first('description')
            ], 422);
        }
    }

    if ($request->hasFile('image')) {
        if (!$request->file('image')->isValid()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Uploaded file is not valid'
            ], 400);
        }
    }
    $imageName = '';
    $uploadPath = public_path('uploads');
    if (!File::exists($uploadPath)) {
        File::makeDirectory($uploadPath, 0777, true, true);
    }


    if ($request->hasFile('image')) {
        $imageName = time().'.'.$request->image->extension();
        $request->image->move(public_path('uploads'), $imageName);
        $validated['image'] = 'uploads/'.$imageName;
    }else{
       return response()->json([
            'data' =>$request->hasFile('image'),
                'status' => 'error',
                'message' => 'Uploaded file is not valid ss'
            ], 400); 
    }

    
    $product = $this->Product;
    $product->name  = $request->name;
    $product->description  = $request->description;
    $product->price  = $request->price;
    $product->unit  = $request->unit;
    $product->image = $imageName;
    $product->save();

    return response()->json([
        'message' => 'Product created successfully',
        'inserted_id' => $product->id,
        'data' => $product
    ], 201);
}


    public function show($product){
        $product = $this->Product::find($product);

        if (!$product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $product
        ], 200);
    }

    public function update(Request $request, $product){
        $product = $this->Product::find($product);
        if (!$product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Product not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'image'       => 'nullable|string'
        ]);

        if ($validator->fails()) {
            if ($validator->errors()->has('name')) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $validator->errors()->first('name')
                ], 422);
            }

            if ($validator->errors()->has('description')) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $validator->errors()->first('description')
                ], 422);
            }

            if ($validator->errors()->has('image')) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $validator->errors()->first('image')
                ], 422);
            }
        }
        $product->name = $request->name;
        $product->description = $request->description;
        $product->image = $request->image;
        $product->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Product updated successfully',
            'data'    => $product
        ], 200);
    }

    public function destroy($product){
        $product = $this->Product::find($product);
        if (!$product) {
            return response()->json([
            'status'  => 'error',
            'message' => 'Product not found'
            ], 404);
        }

        $product->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Product deleted successfully',
            'deleted_id' => $product
        ], 200);
    }
}
