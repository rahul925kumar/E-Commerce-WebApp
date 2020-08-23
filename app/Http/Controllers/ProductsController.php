<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Image;
use Illuminate\Http\UploadedFile;
use RealRashid\SweetAlert\Facades\Alert;
use DB;
use Session;
use App\Products;
use App\Category;
use App\ProductAttribute;
use App\ProductsImages;

class ProductsController extends Controller
{
   public function addProduct(Request $request,$id=NULL)
   {
       //Add products
        $validData =  $request->validate([
            'product_name' => ['required'],
            'product_code' =>'required',
            'product_color' => 'required',
            'product_description' => 'required',
            'product_price' => 'required'
       ]);
       if($request->ismethod('post'))
       {
           $data=$request->all();
           //print_r($data);
           $product = new products();
           $product->name =$data['product_name'];
           $product->code =$data['product_code'];
           $product->color_code = $data['product_colour'];
           if(!empty($data['product_description']))
           {
            $product->description =$data['product_description'];
           }
           else{
            $product->description = '';
           }
           $product->price =$data['product_price'];

           //image upload
           if($request->hasfile('image'))
           {
               echo $img_tmp = Input::file('image');
               if($img_tmp->isValid())
               {
               //image path
               $exteneion = $img_tmp->getClientOriginalExtension();
               $filename = rand(111,99999).'.'.$exteneion;
               $img_path = 'uploads/products/'.$filename;
               
                // $post_thumbnail = $request->file('slika');
                // $filename  = time() . '.' . $post_thumbnail->getClientOriginalExtension();
                // Image::make($post_thumbnail)->backup();
                // ini_set('memory_limit', '256M');
                // image resize
               Image::make($img_tmp)->resize(500,500)->save($img_path);


               $product->image = $filename;
               }

           }
           $product->save();
           return redirect('/admin/view-products')->with('flash_message_success','Product added successfully');
           

       }
       //Categories Drop Down Code
       $categories = Category::where(['parent_id'=>0])->get();
       $categories_dropdown = "<option value='' selected disbaled>Select </option>";
       foreach($categories as $cat)
       {
            $categories_dropdown .= "<option value='".$cat->id."'>".$cat->name."</option>";
            $sub_categories = Category::where(['parent_id'=>$cat->id])->get();
            foreach($sub_categories as $sub_cat){
                $categories_dropdown .= "<option value='".$sub_cat->id."'>&nbsp;--&nbsp".$sub_cat->name."</option>";
            }
       }
        return view('admin.products.add_product')->with(compact('categories_dropdown'));
   }
   //View Products
   public function viewProducts(Request $request, $id=null)
   {
       $products = Products::get();
       return view('admin.products.view_products')->with(compact('products'));
   }

   //View Products
   public function editProducts(Request $request, $id=null){
       if($request->ismethod('post'))
       {
           $data = $request->all();
           if($request->hasfile('image'))
           {
               echo $img_tmp = Input::file('image');
               if($img_tmp->isValid())
               {
               //image path
               $exteneion = $img_tmp->getClientOriginalExtension();
               $filename = rand(111,99999).'.'.$exteneion;
               $img_path = 'uploads/products/'.$filename;
               // image resize
               Image::make($img_tmp)->resize(500,500)->save($img_path);
               }
            }else{
                   $filename = $data['current_image'];
            }
               if(empty($data['product_description']))
               {
                   $data['product_description'] = '';
               }
               Products::where(['id'=>$id])->update(['name'=>$data['product_name'], 'category_id'=>$data['category_id'],
               'code'=>$data['product_code'],'color_code'=>$data['product_colour'],
               'description'=>$data['product_description'],
                'price'=>$data['product_price'], 'image'=>$filename]);
                 return redirect('/admin/view-products')->with('flash_message_success','Product has been updated succesfully');
       }
       $productDetails = Products::where(['id'=>$id])->first();

        //Category Dropdown code
        $categories = Category::where(['parent_id'=>0])->get();
        $categories_dropdown = "<option value='' selected disbaled>Select </option>";
        foreach($categories as $cat)
        {
            if($cat->id==$productDetails->category_id)
            {
                $selected ="selected";
            }else{
                $selected = "";
            }
            $categories_dropdown .= "<option value='".$cat->id."' ".$selected.">".$cat->name."</option>";
             //code for showing subcategories in main category
            $sub_categories = Category::where(['parent_id'=>$cat->id])->get();
            foreach($sub_categories as $sub_cat){
                if($sub_cat->id==$productDetails->category_id){
                    $selected = "selected";
                }else{
                    $selected = "";
                }
            $categories_dropdown .= "<option value = '".$sub_cat->id."' ".$selected.">&nbsp;--&nbsp;".$sub_cat->name."</option>";
            }
          
        }

       return view('admin.products.edit_product')->with(compact('productDetails','categories_dropdown'));
   }
   //Delete Products
   public function deleteProducts(Request $request, $id=null)
   {
       Products::where(['id'=>$id])->delete();
       Alert::success(' Deleted Successfully','Success Message');
       return redirect()->back()->with('flash_message_error','Product Deleted');
   }
   //Update Status of product
   public function updateStatus(Request $request, $id=null)
   {
        $data = $request->all();
        Products::where('id',$data['id'])->update(['status'=>$data['status']]);
   }

   //product  function to send the details of products
   public function products($id=null){
       $productDetails = Products::with('attributes')->where('id',$id)->first();
       $ProductsAltImages = ProductsImages::where('product_id',$id)->get();
       $featuredProducts = Products::where(['featured_products'=>1])->get();
    //    echo $featuredProducts; die();
    //    echo $ProductsAltImages; die();
       //echo $productDetails; die(); 
       return view('shopblade.product_detail')->with(compact('productDetails','ProductsAltImages','featuredProducts'));
   }

   public function addAttributes(Request $request, $id=NULL)
   {
       $productDetails = Products::where(['id'=>$id])->first();
       if($request->ismethod('post'))
       {
           $data = $request->all();
        //    echo '<pre>'; print_r($data); die();
        foreach($data['sku'] as $key => $val)
        {
            if(!empty($val))
            {
                //prevent duplicate sku record
                $attrCountSKU = ProductAttribute::where('sku',$val)->count();
                if($attrCountSKU > 0)
                {
                    return redirect('/admin/add-attributes/'.$id)->with('flash_message_erroe','SKU is already exsits please select another');

                } 
                //prevent duplicate size record
                $attrCountSizes = ProductAttribute::where(['product_id'=>$id,'size'=>$data['size']
                    [$key]])->count();
                    if($attrCountSizes>0){
                    return redirect('/admin/add-attributes/'.$id)->with('flash_message_error',''.$data['size'][$key].'Size is already exist please select another size');
                    }
                    $attribute = new ProductAttribute;
                    $attribute->product_id = $id;
                    $attribute->sku = $val;
                    $attribute->size = $data['size'][$key];
                    $attribute->price = $data['price'][$key];
                    $attribute->stock = $data['stock'][$key];
                    $attribute->save();

            }
        }
        return redirect('/admin/add-attributes/'.$id)->with('flash_message_success','Products attributes added successfully!');
       }
        return view('admin.products.add_attributes')->with(compact('productDetails'));
   }
   public function deleteAttribute($id=null){
    ProductAttribute::where(['id'=>$id])->delete();
    return redirect()->back()->with('flash_message_error','Product Attribute is deleted!');

}

    public function editAttributes(Request $request,$id=null)
    {
        if($request->isMethod('post')){
            $data = $request->all();
            foreach($data['attr'] as $key=>$attr){
                ProductAttribute::where(['id'=>$data['attr'][$key]])->update(['sku'=>$data['sku'][$key],
                'size'=>$data['size'][$key],'price'=>$data['price'][$key],'stock'=>$data['stock'][$key]]);
            }
            return redirect()->back()->with('flash_message_success','Products Attributes Updated!!!');
        }
    }

    public function addImages(Request $request,$id=null)
    {
        $productDetails = Products::where(['id'=>$id])->first();
        if($request->isMethod('post')){
            $data = $request->all();
            if($request->hasfile('image')){
                $files = $request->file('image');
                foreach($files as $file){
                    $image = new ProductsImages;
                    $extension = $file->getClientOriginalExtension();
                    $filename = rand(111,9999).'.'.$extension;
                    $image_path = 'uploads/products/'.$filename;
                    Image::make($file)->save($image_path);
                    $image->image = $filename;
                    $image->product_id = $data['product_id'];
                    $image->save();
                }
            }
            return redirect('/admin/add-images/'.$id)->with('flash_message_success','Image has been updated');
        }
        $productImages = ProductsImages::where(['product_id'=>$id])->get();
        return view('admin.products.add_images')->with(compact('productDetails','productImages'));
    }

    public function deleteAltImage($id=NULL)
    {
        // ProductsImages::where(['id'=>$id])->delete();
        // return redirect()->back()->with('flash_message_error','Product Attribute is deleted!');
        $productImage = ProductsImages::where(['id'=>$id])->first();

        $image_path = 'uploads/products/';
        if(file_exists($image_path.$productImage->image)){
            unlink($image_path.$productImage->image);
        }
        ProductsImages::where(['id'=>$id])->delete();
        Alert::success('Deleted','Success Message');
        return redirect()->back();
    }

    public function updateFeatured(Request $request, $id=null)
    {
         $data = $request->all();
         Products::where('id',$data['id'])->update(['featured_products'=>$data['status']]);
    }

        public function getprice(Request $request)
        {
            $data = $request->all();
           //  echo "<pre>";print_r($data);die;
           $proArr = explode("-",$data['idSize']);
           $proAttr = ProductsAttributes::where(['product_id'=>$proArr[0],'size'=>$proArr[1]])->first();
           echo $proAttr->price;
       }

       //Add to cart Function
       public function addtocart(Request $request)
       {
            $data = $request->all();
            // echo "<pre>"; print_r($data); die();
            if(empty($data['user_email']))
            {
                $data['user_email'] = '';
            }
            $session_id = Session::get('session_id');
            if(empty($session_id)){
            $session_id = str_random(40); //  to generate the random string
            Session::put('session_id',$session_id);
            }

            $sizeArr = explode('-',$data['size']);
            $CountProducts = DB::table('cart')->where(['product_id'=>$data['product_id'],'product_color'=>$data['product_color'],
            'price'=>$data['price'],'size'=>$sizeArr[1],'session_id'=>$session_id])->count();
            if($CountProducts > 0){
                return redirect()->back()->with('flash_message_success','Product already exists in cart.');
            }
            else{
                DB::table('cart')->insert(['product_id'=>$data['product_id'],'product_name'=>$data['product_name'],
            'product_code'=>$data['product_code'],'product_color'=>$data['product_color'],'price'=>$data['price'],
            'size'=>$sizeArr[1],'quantity'=>$data['quantity'],'user_email'=>$data['user_email'],
            'session_id'=>$session_id]);
            }
            // die();
            return redirect('/cart')->with('flash_message_success','Product has been added in cart succesfully');
       }

       public function cart(Request $request)
        {
            $session_id = Session::get('session_id');
             $userCart = DB::table('cart')->where(['session_id'=>$session_id])->get();
            //  echo "<pre>";print_r($userCart);die;
            foreach($userCart as $key=>$products)
            {
                // echo $products->id;
                $productDetails = Products::where(['id'=>$products->product_id])->first();
                // echo $userCart[$key]->image = $productDetails->image; die();
                $userCart[$key]->image = $productDetails->image;
            }
            return view('shopblade.products.cart')->with(compact('userCart'));
       }
       public function deleteCartProduct($id=NULL){
        DB::table('cart')->where('id',$id)->delete();
        return redirect('/cart')->with('flash_message_error','Product has been deleted!');

       }

       public function updateCartQuantity($id=NULL, $quantity=null){
           DB::table('cart')->where('id'.$id)->increment('quantity',$quantity);
           return redirect('/cart')->with('flash_message_success','Product Quantity has been updated Successfully');
       }
    
}
