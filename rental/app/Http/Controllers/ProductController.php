<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\ProductStoreRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
 
class ProductController extends Controller
{
    public function index()
    {
       // All Product
       $products = Product::all();
     
       // Return Json Response
       return response()->json([
          'products' => $products
       ],200);
    }
 
    public function store(ProductStoreRequest $request)
    {
        try {
            $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
     
            // Create Product
            Product::create([
                'name' => $request->name,
                'image' => $imageName,
                'description' => $request->description
            ]);
     
            // Save Image in Storage folder
            Storage::disk('public')->put($imageName, file_get_contents($request->image));
     
            // Return Json Response
            return response()->json([
                'message' => "Film successfully added."
            ],200);
        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'message' => "Film failed to add"
            ],500);
        }
    }
 
    public function show($id)
    {
       // Product Detail per id
       $product = Product::find($id);
       if(!$product){
         return response()->json([
            'message'=>'Film Not Found.'
         ],404);
       }
     
       // Return Json Response
       return response()->json([
          'product' => $product
       ],200);
    }
 
    public function update(ProductStoreRequest $request, $id)
    {
        try {
            // Find product
            $product = Product::find($id);
            if(!$product){
              return response()->json([
                'message'=>'Film Not Found.'
              ],404);
            }
     
            $product->name = $request->name;
            $product->description = $request->description;
     
            if($request->image) {
                // Public storage
                $storage = Storage::disk('public');
     
                // Old iamge delete
                if($storage->exists($product->image))
                    $storage->delete($product->image);
     
                // Image name
                $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
                $product->image = $imageName;
     
                // Image save in public folder
                $storage->put($imageName, file_get_contents($request->image));
            }
     
            // Update Product
            $product->save();
     
            // Return Json Response
            return response()->json([
                'message' => "Film successfully updated."
            ],200);
        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'message' => "Film failed to updated"
            ],500);
        }
    }
 
    public function destroy($id)
    {
        // Detail 
        $product = Product::find($id);
        if(!$product){
          return response()->json([
             'message'=>'Film Not Found.'
          ],404);
        }
     
        // Public storage
        $storage = Storage::disk('public');
     
        // Iamge delete
        if($storage->exists($product->image))
            $storage->delete($product->image);
     
        // Delete Product
        $product->delete();
     
        // Return Json Response
        return response()->json([
            'message' => "Film successfully deleted."
        ],200);
    }

    public function sortByName()
    {
    // Retrieve all products and sort by name in ascending order
    $products = Product::orderBy('name', 'asc')->get();

    // Return JSON response
    return response()->json([
        'products' => $products
    ], 200);
    }
}