<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Registration;
use App\Course;
use App\Student;
use DB;

class RegistrationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("registrations.index");
    }

    public function allRegistrations()
    {
        $registrations = Registration::groupBy("student_id")->get();
        $data = [];
        foreach($registrations as $reg){
            $student = Student::find($reg->student_id);
            $coursesStudent = Registration::where("student_id", $reg->student_id)->get();
            $courses = [];
            foreach($coursesStudent as $course){
                $courses[] = $course->course_id;
            }

            $data[] = [
                "id" => $reg->id,
                "name" => $student->name,
                "courses" => $courses
            ];
        }
        return response()->json(["success" => true, "registrations" => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $registrationData = $request->data;

        foreach($registrationData["courses_id"] as $courseID){
            $registration = new Registration;
            $registration->course_id = $courseID;
            $registration->student_id = $registrationData["student_id"];
            $registration->save();
        }

        return response()->json(["success" => true]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $registration = Registration::find($request->id);
        return response()->json(["success" => true, "registration" => $registration]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $studentID = Registration::select("student_id")->where("id", $request->id)->first()->student_id;
        $registration = Registration::where("student_id", $studentID)->get();
        $courses = [];
        foreach($registration as $reg){
            $courses[] = $reg->course_id;
        }

        return response()->json(["success" => true, "student" => $studentID, "courses" => $courses]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $studentID = Registration::select("student_id")->where("id", $request->id)->first()->student_id;
        Registration::where("student_id", $studentID)->delete();

        $registrationData = $request->data;

        foreach($registrationData["courses_id"] as $courseID){
            $registration = new Registration;
            $registration->course_id = $courseID;
            $registration->student_id = $registrationData["student_id"];
            $registration->save();
        }

        return response()->json(["success" => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $studentID = Registration::select("student_id")->where("id", $request->id)->first()->student_id;
        Registration::where("student_id", $studentID)->delete();
        return response()->json(["success" => true]);
    }

    public function getRegistrationsData(){
        $students = Student::all();
        $courses = Course::all();
        return response()->json(["success" => true, "students" => $students, "courses" => $courses]);
    }
}
