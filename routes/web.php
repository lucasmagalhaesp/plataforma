<?php

use Illuminate\Support\Facades\Route;

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

/* Route::get('/', function () {
    return view('welcome');
}); */

Route::get("/", function(){
    return redirect("registrations");
});

Route::get("/registrations/getRegistrationsData", "RegistrationsController@getRegistrationsData");
Route::get("/registrations/allRegistrations", "RegistrationsController@allRegistrations");
Route::resource("/registrations", "RegistrationsController");
Route::get("/courses/allCourses", "CoursesController@allCourses");
Route::resource("/courses", "CoursesController");
Route::get("/students/allStudents", "StudentsController@allStudents");
Route::resource("/students", "StudentsController");
