<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Blog;
use App\Role;
use App\User;
use App\Blogtag;
use App\Category;
use App\Blogcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class AdminController extends Controller
{

    public function index(Request $request)
    {

        // first check if you are loggedin and admin user ...
        //return Auth::check();
        if (!Auth::check() && $request->path() != 'login') {
            return redirect('/login');
        }

        if (!Auth::check() && $request->path() == 'login') {

            return view('admin');
        }
        // you are already logged in... so check for if you are an admin user..
        $user = Auth::user();
        if ($user->userType == 'User') {
            return redirect('/login');
        }
        if ($request->path() == 'login') {
            return redirect('/');
        }

        return $this->checkForPermission($user, $request);

    }
    // public function index(Request $request){
        
    //     if(!Auth::check() && $request->path() != 'login'){
    //         return redirect('/login');
    //     }
    //     $login = Auth::check() && $request->path() == 'login' && Auth::user()->userType != 'User';        
    //     if($login){
    //         return redirect('/');
    //         return view('welcome');
    //     }
    //     // return 'here';
    //     // if(!Auth::check() && $request->path() != 'login'){
    //         //     return redirect('welcome');
    //         // }
    //     $user = Auth::user();
    //     return $this->checkForPermission($user, $request);
    //     return view('welcome');
    // }
    public function checkForPermission($user, $request){
        $user = Auth::user();
        $permission = json_decode($user->role->permission);
            
            // echo $permission[1]->name;
            // echo $request->path();
            if($request->path()== 'editblog/{slug}'){
                return view ('admin');
            }else{
                $hasPermission = false;
                foreach($permission as $p){
                    if($p->name==$request->path()){
                        if($p->read==true){
                            $hasPermission = true;
                        }
                    }
                }
                if($hasPermission) {
                    return view('admin');
                }
                return view ('admin');
                return view('error404pg');
            }
        }
    public function logout(){
        Auth::logout();
        return redirect('/login');
    }
    
    
    public function addTag(Request $request)
    {
        $this->validate($request, [
            'tagName' => 'required'
        ]);

       return Tag::create([
           'tagName' => $request->tagName,
       ]);
    }
    public function getTag()
    {
        return Tag::orderBy('id', 'desc')->get();
    }
    public function editTag(Request $request)
    {
        // validate request
        $this->validate($request, [
            'tagName' => 'required',
            'id' => 'required',
        ]);
        return Tag::where('id', $request->id)->update([
            'tagName' => $request->tagName,
        ]);
    }
    public function deleteTag(Request $request)
    {
        // validate request
        $this->validate($request, [
        'id' => 'required',
        ]);
        return Tag::where('id', $request->id)->delete();
    }
    public function upload(Request $request){

        $this->validate($request, [
            'file' => 'required|mimes:jpeg,jpg,png',
            ]);

        $picName = time() . '.' . $request->file->extension();
        $request->file->move(public_path('uploads'), $picName);
        return $picName;
    }
    public function uploadEditorImage(Request $request){

        $this->validate($request, [
            'image' => 'required|mimes:jpeg,jpg,png',
            ]);

        $picName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('uploads'), $picName);
        return response()->json([
            'success' => 1,
            'file' => [
                'url' => "http://127.0.0.1:8000/uploads/$picName",
            ],
        ]);
        return $picName;
    }

    public function deleteImage(Request $request){
        $fileName = $request -> imageName;
        $this->deleteFileFromServer($fileName);
        return 'done';
    }
    public function deleteFileFromServer($fileName){
        $filePath = public_path().'/uploads/'.$fileName;
        if(file_exists($filePath)){
            @unlink($filePath);
        }
        return;
    }
    public function addCategory(Request $request)
    {
        $this->validate($request, [
            'categoryName' => 'required',
            'iconImage' => 'required',
            ]);

       return Category::create([
           'categoryName' => $request->categoryName,
           'iconImage' => $request->iconImage,
       ]);
    }
    public function getCategory()
    {
        return Category::orderBy('id', 'desc')->get();
    }
    public function editCategory(Request $request)
    {
        // validate request
        $this->validate($request, [
            'categoryName' => 'required',
            'iconImage' => 'required',
            'id' => 'required',
        ]);
        return Category::where('id', $request->id)->update([
            'categoryName' => $request->categoryName,
            'iconImage' => $request->iconImage,
            ]);
    } public function deleteCategory(Request $request)
    {
        // validate request
        $this->validate($request, [
            'id' => 'required',
        ]);
        return Category::where('id', $request->id)->delete();
    }
    public function addAdmin(Request $request)
    {
        $this->validate($request, [
            'fullName' => 'required',
            'email' => 'bail|required|email|unique:users',
            'password' => 'bail|required|min:6',
            'role_id' => 'required',
            ]);

        $password = bcrypt($request->password);
        $user = User::create([
            'fullName' => $request->fullName,
            'email' => $request->email,
            'password' => $password,
            'role_id' => $request->role_id,
            ]);
         return $user;
        }
    
    public function getUsers()
    {
        return User::orderBy('id', 'desc')->get();
    }
    
    public function editAdmin(Request $request)
    {
        $this->validate($request, [
            'fullName' => 'required',
            'email' => "bail|required|email|unique:users,email,$request->id",
            'password' => 'min:6',
            'role_id' => 'required',
        ]);

        $data = [
            'fullName' => $request->fullName,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ];
        if ($request->password) {
            $password = bcrypt($request->password);
            $data['password'] = $password;
        }
        $user = User::where('id', $request->id)->update($data);
        return $user;
    }
    public function adminLogin(Request $request)
    {
        // // validate request
        // $this->validate($request, [
        //     'email' => 'bail|required|email',
        //     'password' => 'bail|required|min:6',
        // ]);
        //return 4545;
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
             $user = Auth::user();
            if ($user->role->isAdmin == 0) {
                Auth::logout();
                return response()->json([
                    'msg' => 'You are not allowed access',
                ], 401);
            }
            return response()->json([
                'msg' => 'You are logged in',
                'user' => $user,
            ]);
        } else {
            return response()->json([
                'msg' => 'Incorrect login details',
            ], 401);
        }
    }
    public function addRole(Request $request)
    {
        $this->validate($request, [
            'roleName' => 'required'
            ]);
    
       return Role::create([
           'roleName' => $request->roleName,
           ]);
    }
    public function editRole(Request $request)
    {
        $this->validate($request, [
            'roleName' => 'required',
            'id' => 'required'
            ]);
    
       return Role::where('id', $request->id)->update([
           'roleName' => $request->roleName,
           ]);
    }
    public function deleteRole(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
            ]);
        return Role::where('id', $request->id)->delete();
    }
    public function getRoles()
    {
        return Role::all();
    }
    public function assignRole(Request $request)
    {
        $this->validate($request, [
            'permission' => 'required',
            'id' => 'required',
        ]);
        return Role::where('id', $request->id)->update([
            'permission' => $request->permission,
        ]);
    }
    public function slug(){
        $title = 'This is a nice title changed';
        return Blog::create([
            'title' => $title,
            'slug' => $title,
            'post' => 'some post',
            'post_excerpt' => 'aesd',
            'user_id' => 1,
            'metaDescription' => 'asdf',

        ]);
        return $title;
    }
    public function createBlog(Request $request){
        $this->validate($request, [
            'title' => 'required',
            'post' => 'required',
            'post_excerpt' => 'required',
            'metaDescription' => 'required',
            'jsonData' => 'required',
        ]);
        $categories = $request->category_id;
        $tags = $request->tag_id;

        $blogCategories = [];
        $blogTags = [];
        DB::beginTransaction();
        try {
            $blog = Blog::create([
                'title' => $request->title,
                'slug' => $request->title,
                'post' => $request->post,
                'post_excerpt' => $request->post_excerpt,
                'user_id' => Auth::user()->id,
                'metaDescription' => $request->metaDescription,
                'jsonData' => $request->jsonData,
            ]);
            // insert blog categories
            foreach ($categories as $c) {
                array_push($blogCategories, ['category_id' => $c, 'blog_id' => $blog->id]);
            }
            Blogcategory::insert($blogCategories);
            // insert blog tags
            foreach ($tags as $t) {
                array_push($blogTags, ['tag_id' => $t, 'blog_id' => $blog->id]);
            }
            Blogtag::insert($blogTags);
            DB::commit();
            return 'done';
        } catch (\Throwable $th) {
            DB::rollback();
            return 'not done';
        }
    }
    // update blog
    public function updateBlog(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'post' => 'required',
            'post_excerpt' => 'required',
            'metaDescription' => 'required',
            'jsonData' => 'required',
            'category_id' => 'required',
            'tag_id' => 'required',
        ]);
        $categories = $request->category_id;
        $tags = $request->tag_id;
        $blogCategories = [];
        $blogTags = [];

        DB::beginTransaction();
        try {
            $blog = Blog::where('id', $id)->update([
                'title' => $request->title,
                'slug' => $request->title,
                'post' => $request->post,
                'post_excerpt' => $request->post_excerpt,
                'user_id' => Auth::user()->id,
                'metaDescription' => $request->metaDescription,
                'jsonData' => $request->jsonData,
            ]);


            // insert blog categories
            foreach ($categories as $c) {
                array_push($blogCategories, ['category_id' => $c, 'blog_id' => $id]);
            }
            // delete all previous categories
            Blogcategory::where('blog_id', $id)->delete();
            Blogcategory::insert($blogCategories);
            // insert blog tags
            foreach ($tags as $t) {
                array_push($blogTags, ['tag_id' => $t, 'blog_id' => $id]);
            }
            Blogtag::where('blog_id', $id)->delete();
            Blogtag::insert($blogTags);
            DB::commit();
            return 'done';
        } catch (\Throwable $e) {

            DB::rollback();
            return 'not done';
        }
    }
    public function getBlogs(Request $request){
        return Blog::with(['cat', 'tag', 'user'])->orderBy('id' ,'desc')->get();
    }
    public function deleteBlog(Request $request)
    {
        return Blog::where('id', $request->id)->delete();
    }
    public function singleBlogItem(Request $request, $id)
    {
       return Blog::with(['cat', 'tag'])->where('id', $id)->first();
    }
}