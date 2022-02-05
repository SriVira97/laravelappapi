<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Marks;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/students', function(){

    $alldata =  DB::table('marks')
    ->orderBy('total', 'asc')
    ->join('students', 'students.id', '=', 'marks.student_id')
    ->join('subjects', 'subjects.id', '=', 'marks.subject_id')
    ->groupBy('students.name','marks.student_id')
    ->get([
        'students.name',
        'marks.student_id',
        DB::raw('sum(marks.marks) as total'),
        DB::raw('count(marks.subject_id) as subjects')
    ]);

    //Marks::leftJoin('students', 'marks.student_id','=', 'students.id')
    return $alldata;

})->name('student.all');

Route::get('/studentbyid/{id}', function($id){

    $student = Student::find($id);
    return $student;
})->name('student.one');

Route::post('/marksupdate/{markid}', function($markid,Request $request){
    $marks =  Marks::find($markid)->update([
        'marks' => $request->marks
        ]);
    return $marks;
})->name('marks.update');

Route::get('/studentmarks/{id}', function($id){

    $marks = Marks::join('subjects','subjects.id', '=', 'marks.subject_id')
    ->select('marks.marks','subjects.subject','subjects.id')->where('marks.student_id', $id)->get();

    return $marks;
})->name('student.marks');

Route::post('/student', function(Request $request){
    $insert = Student::create([
        'name'=> request('name'),
        'active' => request('active')
    ]);
    return $insert;
})->name('student.post');

Route::post('/subject', function(Request $request){
    $insert = Subject::create([
        'subject'=> request('subject'),
        'active' => request('active')
    ]);
    return $insert;
})->name('subject.post');

Route::post('/marks', function(Request $request){
    $insert = Marks::create([
        'student_id'=> request('student_id'),
        'subject_id'=> request('subject_id'),
        'marks' => request('marks')
    ]);
    return $insert;
})->name('marks.post');


