<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Product;
use App\Category;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Validator;
use Exception;

class ProductController extends Controller
{
    //
    const PRODUCTS_PER_PAGE = 50;

    public function index(Request $request)
    {
        $products = Product::take($this::PRODUCTS_PER_PAGE);
        //filter
        $request->price &&   $products->where('price',$request->price);
        $request->price_order &&  $products->orderBy('price',$request->price_order);
        $request->created_at && $products->whereDate('created_at',$request->created_at);
        $request->created_at_order &&  $products->orderBy('created_at',$request->created_at_order);
        $request->page &&  $products->skip(($request->page-1)*$this::PRODUCTS_PER_PAGE);

        return response()->json(['success'=>true,'payload'=>$products->with('categories')->get()],Response::HTTP_OK);  
    }


    public function show($product_id)
    {
        try {     
            if(!$product = Product::find($product_id))
            {
                throw new Exception("Product don't exist");
            }
        } catch (\Exception $th)
        {
            return  response()->json(['success'=>false,"error"=>$th->getMessage()]);
        }
        return response()->json($product);
    }
 

    public function store(StoreProductRequest $request)
    {
        try {
            $this->throwExceptionIfCategoriesNotExist($request->category_id);
            $product = Product::create($request->post());
            $product->attachCategories($request->category_id);
            return  response()->json(['success'=>true,"payload"=>$product->id],Response::HTTP_OK);

        } catch (\Exception $th) {
              return  response()->json( ['success'=>false,"error"=>$th->getMessage()] );
        }
     } 
     

     public function update(UpdateProductRequest $request,$product_id)
     {
         try
         {
             $this->throwExceptionIfCategoriesNotExist($request->category_id);
             if(!$product = Product::find($product_id)){
                 throw new Exception("Product don't exist");
             }
             $product->update($request->post());
             $request->category_id && $product->attachCategories($product,$request->category_id);
             return true;
         } 
        catch (\Throwable $th)
        {
            return response()->json( ['success'=>false,"error"=>$th->getMessage()]);
        }
     }
     



    public function getProductsByCategoryId($category_id)
    {
        $products = Product::whereHas('categories',function($q) use ($category_id){
            $q->where('category_id',$category_id);
        });
        return response()->json(['success'=>true,'payload'=>$products->get()],Response::HTTP_OK);  
    }



    public function destroy($product_id)
    {
        try 
        {
            $product = Product::find($product_id);
            $product->categories()->detach();
            $product->delete();
            return true;
        } catch (\Throwable $th) {
            return response()->json( ['success'=>false,"error"=>$th->getMessage()]);
        }
    }

    private function throwExceptionIfCategoriesNotExist($categories_id)
    {
        if(Category::whereIn('id',$categories_id)->count() !== count($categories_id)){
            throw new Exception("category not exist");
        }
        return true;
    }

 
    
}