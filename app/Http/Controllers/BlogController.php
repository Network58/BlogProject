<?php

namespace App\Http\Controllers;

use App\Blog;
use App\Category;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request){
        $categories = Category::select('id', 'categoryName')->get();
        $blogs = Blog::orderBy('id','desc')->with(['cat', 'user'])->limit(6)->get(['id','title','slug','post_excerpt']);
        // return $blogs;
        // return $categories;
        return view('welcome')->with(['category' =>$categories,'blogs'=>$blogs]);
    }
}
