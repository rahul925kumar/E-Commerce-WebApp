<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use RealRashid\SweetAlert\Facades\Alert;

class CategoryController extends Controller
{
    public function addCategory(Request $request)
    {
        if($request->ismethod('post'))
        {
            $data = $request->all();
            $category = new Category;
            $category->name = $data['category_name'];
            $category->parent_id = $data['parent_id'];
            $category->url = $data['category_url'];
            $category->description = $data['category_description'];
            $category->save();
            return redirect('/admin/add-category')->with('flash_message_success','Category Added Successfully');
        }
        $levels = Category::where(['parent_id'=>0])->get();
        // die();
        return view('admin.category.add_category')->with(compact('levels'));
    }

    // View Categories
    public function viewCategories()
    {
        $categories = Category::get();
        return view('admin.category.view_category')->with(compact('categories'));
    }
    //Edit Category
    public function editCategory(Request $request, $id=NULL)
    {
        if($request->ismethod('post'))
        {   
            $data = $request->all();
            Category::where(['id'=>$id])->update(['name'=>$data['category_name'],
            'parent_id'=>$data['parent_id'],'description'=>$data['category_description']
            ,'url'=>$data['category_url']]);
            return redirect('/admin/view-categories')->with('flash_message_success','Category Updated Successfully!!!');
        }
        $levels = Category::where(['parent_id'=>0])->get();
        $categoryDetails = Category::where(['id'=>$id])->first();
        
        return view('admin.category.edit_category')->with(compact('levels','categoryDetails'));
    }
    //Delete Category
    public function deleteCategory(Request $request, $id=NULL)
    {
        Category::where(['id'=>$id])->delete();
        Alert::Success('Delete','Success Message');
        return redirect()->back();
    }
    public function updateStatus(Request $request, $id=null)
    {
         $data = $request->all();
         Category::where('id',$data['id'])->update(['status'=>$data['status']]);
    }
}
