<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Banners;
use App\Category;
use App\Products;

class IndexController extends Controller
{
    public function index()
    {   
        $banners = Banners::where('status','1')->orderby('sort_order','asc')->get();
        $categories = Category::with('categories')->where(['parent_id'=>0])->get();
        $products = Products::get();
        return view('shopblade.index')->with(compact('banners','categories','products'));
    }

    public function categories($id)
    {
        $categories = Category::with('categories')->where(['parent_id'=>0])->get();
        $products = Products::where(['categories'=>$id])->get();
        $product_name = Category::where(['id'=>$categories])->first();
        
        return view('shopblade.category')->with(compact('categories', 'products', 'product_name'));
    }
}
