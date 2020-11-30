<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Category;
use Storage;
class CategoryController extends Controller
{

    public function index(){
        return response()->json(Category::all());
    }
    
    public function store(StoreCategoryRequest $request)
    {
        try {
            $category = Category::create($request->validated());
            return response()->json(['success'=>true,'payload'=>$category->id],Response::HTTP_OK);
        } catch (\Exception $th) {
            return response()->json(['success'=>false,'error'=>$th->getMessage()],Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(UpdateCategoryRequest $request,$category_id)
    {
        try
        {
            $category = Category::find($category_id);
            $category->update($request->validated());
            return response(Response::HTTP_OK);
        } catch (\Exception $th)
        {
            return response()->json(['success'=>false,'error'=>$th->getMessage()],Response::HTTP_BAD_REQUEST);
        }
    }

    public function destroy($category_id)
    {
        try {
            $category = Category::find($category_id);
            $category->products()->detach();
            return response(Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'error'=>$th->getMessage()],Response::HTTP_BAD_REQUEST);
        }
    }


}
