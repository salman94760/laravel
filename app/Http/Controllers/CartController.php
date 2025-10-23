<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(){
        $this->cart = new cart();
    }

    public function store(Request $request){
    if (!User::where('id', $request->user_id)->exists()) {
        return response()->json([
            'status' => 'error',
            'message' => 'User not found'
        ], 400);
    }
    
    return response()->json([
            'status' => 'error',
            'message' => $request->all()
        ], 400);

    $validator = Validator::make($request->all(), [
        'name'        => 'required|string|max:255',
        'description' => 'required|string',
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
}
