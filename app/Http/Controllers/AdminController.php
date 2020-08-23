<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
class AdminController extends Controller
{
    public function login(Request $request)
    {
        if($request->ismethod('post'))
        {
            $data = $request->input();
            if(Auth::attempt(['email'=>$data['username'],'password'=>$data['password']])){
                return redirect('admin/dashboard');
            }
            else{
                return redirect('./admin')->with('flash_message_error','Invalid Username or Password');
            }
        }
        return view('admin.admin_login');
    }

    public function dashboard()
    {
        return view('admin.dashboard');
    }
    public function logout()
    {
        Session::flush();
        return redirect('/admin')->with('flash_message_success','You are Logged out!');
    }
}
