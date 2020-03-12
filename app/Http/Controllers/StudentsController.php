<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Student;
use DB;

class StudentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("students.index");
    }

    public function allStudents()
    {
        $students = Student::all();
        return response()->json(["success" => true, "students" => $students]);
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
    public function store(Request $request, Student $student)
    {
        $studentData = $request->data;

        $student->name = $studentData["name"];
        $student->email = $studentData["email"];
        $student->birth_date = $studentData["birth_date"];

        $student->save();

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
        $student = Student::find($request->id)->first();
        return response()->json(["success" => true, "student" => $student]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $student = Student::find($request->id);
        return response()->json(["success" => true, "student" => $student]);
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
        $student = Student::find($request->id);
        $studentData = $request->data;

        $student->name = $studentData["name"];
        $student->email = $studentData["email"];
        $student->birth_date = $studentData["birth_date"];

        $student->save();

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
        Student::find($request->id)->delete();
        return response()->json(["success" => true]);
    }
}
