<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $fillable = ['name','external_id'];

    public function categories(){
        return $this->belongsToMany(User::class);
    }
}
