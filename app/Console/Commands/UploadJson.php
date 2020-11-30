<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\Storage;
use Illuminate\Console\Command;
use App\Category;
use App\Product;
use App\Http\Requests\StoreCategoryRequest;
use Illuminate\Support\Facades\Validator;
use Exception;

class UploadJson extends Command
{

    const PRODUCT_RULES=[
        'name'=>'required|max:200',
        'price'=>'required|between:0,99.99',
        'description'=>'required|max:1000',
        'category_id'=>'required|array',
        'category_id.*'=>'int',
        'quantity'=>'required|int|min:1',
        'external_id'=>'required|int',
    ];
    const CATEGORY_RULES=[
        'name'=>'required|max:200',
    ];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'storing products from product.json';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->uploadCategory();
       
        $this->uploadProduct();
       
    }


    private function uploadProduct(){
        $json = Storage::disk('local')->get('products.json');
        $data =  json_decode($json);
        
        foreach($data as $row){
         try 
         {
             $row = (array)$row;
             $validator  =  Validator::make($row,$this::PRODUCT_RULES);
             if ($validator->passes()) {
                  $product =  Product::updateOrCreate(['external_id'=>$row['external_id']],$row);
                  $product->attachCategories($row['category_id']);
             }else{
                 throw new Exception($validator->errors());
             }
         } catch (\Throwable $th) {
             echo $th->getMessage();
             return false;
         }

          }
        echo "Products uploaded successfully\n";

    }

    private function uploadCategory(){
        $json = Storage::disk('local')->get('categories.json');
        $data =  json_decode($json);

        foreach($data as $row){
            $row = (array)$row;
            try {
                $validator  =  Validator::make($row,$this::CATEGORY_RULES);
           
                if ($validator->passes()) {
                    Category::updateOrCreate(['external_id'=>$row['external_id']],$row);
                }else{
                    throw new Exception($validator->errors());
                }
            } catch (\Throwable $th) {
                echo $th->getMessage();
                return false;
            }
         

        }
        echo "categories stored/updated successfully\n";
    }


    
 
 
}
