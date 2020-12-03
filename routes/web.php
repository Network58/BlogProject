<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminCheck;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::prefix('app')->middleware(AdminCheck::class)->group(function(){
    Route::post('create_tag', 'AdminController@addTag');
    Route::post('edit_tag', 'AdminController@editTag');
    Route::post('delete_tag', 'AdminController@deleteTag');
    Route::get('get_tags', 'AdminController@getTag');
    Route::post('upload', 'AdminController@upload');
    Route::post('delete_image', 'AdminController@deleteImage');
    Route::post('create_category', 'AdminController@addCategory');
    Route::get('get_category', 'AdminController@getCategory');
    Route::post('edit_category', 'AdminController@editCategory');
    Route::post('delete_category', 'AdminController@deleteCategory');
    Route::post('create_admin', 'AdminController@addAdmin');
    Route::get('get_users', 'AdminController@getUsers');
    Route::post('edit_user', 'AdminController@editAdmin');
    Route::post('admin_login', 'AdminController@adminLogin');
    Route::post('create_role', 'AdminController@addRole');
    Route::get('get_roles', 'AdminController@getRoles');
    Route::post('edit_role', 'AdminController@editRole');
    Route::post('delete_role', 'AdminController@deleteRole');
    Route::post('assign_roles', 'AdminController@assignRole');
    Route::post('create_blog', 'AdminController@createBlog');
    Route::get('get_blogs', 'AdminController@getBlogs');
    Route::post('delete_blog', 'AdminController@deleteBlog');
    Route::get('blog_single/{id}', 'AdminController@singleBlogItem');
    Route::post('update_blog/{id}', 'AdminController@updateBlog');
});



Route::post('createBlog', 'AdminController@uploadEditorImage');
Route::get('slug', 'AdminController@slug');


Route::get('/logout', 'AdminController@logout');
Route::get('/', 'AdminController@index');

// Route::get('/login', 'AdminController@index');
// Route::any('{slug}', 'AdminController@index')->where('slug', '([a-z\d-\/_.]+)?');
Route::any('{slug}', 'AdminController@index')->where('slug', '([-a-z/0-9_\s]+)');

Route::get('/adminP', 'BlogController@index');


// Route::any('{any}', function () {
//     return view('welcome');
// });