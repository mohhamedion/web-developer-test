<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable = ['name','external_id','price','description','quantity'];



    public function categories(){
        return $this->belongsToMany(Category::class,'product_category');
    }

    public function attachCategories($categories){
        $existCategories =$this->categories()->whereIn('categories.id',$categories)->pluck('categories.id')->toArray();
        $categoriesIds = array_diff($categories,$existCategories);
        $this->categories()->attach($categoriesIds);
    }
}
