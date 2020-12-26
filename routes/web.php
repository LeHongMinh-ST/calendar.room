<?php

use Facade\FlareClient\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

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
//=== Minh route ===

//Backend route
Route::group([
    'prefix' => 'admin',
    'namespace' => 'Backend',
], function () {

    Route::middleware('admin.auth')->group(function () {
        Route::get('/', function () {
            return redirect()->route('backend.dashboard');
        });
        Route::get('/dashboard', 'DashboardController@index')->name('backend.dashboard');
        Route::get('/dashboard/get-data','DashboardController@getData');
        //===Route semester
        Route::group(['prefix' => 'semester'], function () {
            Route::get('/', 'SemesterController@index')->name('backend.semester.index');
            Route::get('/getData', 'SemesterController@getData')->name('backend.semester.getData');
            Route::get('/create', 'SemesterController@create')->name('backend.semester.create');
            Route::post('/store', 'SemesterController@store')->name('backend.semester.store');
            Route::get('/getWeek/{id}', 'SemesterController@getWeek')->name('backend.semester.getWeek');
            Route::get('/show/{id}', 'SemesterController@show')->name('backend.semester.show');
            Route::get('{id}/edit', 'SemesterController@edit')->name('backend.semester.edit');
            Route::put('/update/{id}', 'SemesterController@update')->name('backend.semester.update');
            Route::delete('/delete/{id}', 'SemesterController@destroy')->name('backend.semester.destroy');
            Route::get('showWeek/{id}','SemesterController@showWeek')->name('backend.semester.showWeek');
            Route::get('/getNote/{id}','SemesterController@getNote')->name('backend.semester.getNote');
            Route::put('/saveNote/{id}', 'SemesterController@saveNote')->name('backend.semester.saveNote');
            Route::post('/check-time-semester','SemesterController@checkTime')->name('backend.semester.checkTime');
            Route::post('/check-semester-unique','SemesterController@checkSemesterUnique')->name('backend.semester.checkSemesterUnique');

        });

        Route::group([
            'prefix' => 'subject',
            'as' => 'subject.'
        ], function () {
            Route::get('/', 'SubjectController@index')->name('index');
            Route::get('/getData', 'SubjectController@getData')->name('getData');
            Route::get('show/{id}', 'SubjectController@show')->name('show');
            Route::get('create', 'SubjectController@create')->name('create');
            Route::post('/store', 'SubjectController@store')->name('store');
            Route::put('update/{id}', 'SubjectController@update')->name('update');
            Route::delete('destroy/{id}', 'SubjectController@destroy')->name('destroy');
            Route::get('{id}/edit', 'SubjectController@edit')->name('edit');
            Route::post('/check-subject-id-unique','SubjectController@checkSubjectIdUnique');
            Route::post('/check-subject-id-unique-update','SubjectController@checkSubjectIdUniqueUpdate');
            Route::put('/toggleActive/{id}','SubjectController@toggleActive')->name('toggleActive');
        });
        Route::group([
            'prefix' => 'department',
            'as' => 'department.'
        ], function () {
            Route::get('/', 'DepartmentController@index')->name('index');
            Route::get('/getData', 'DepartmentController@getData')->name('getData');
            Route::get('show/{id}', 'DepartmentController@show')->name('show');
            Route::get('create', 'DepartmentController@create')->name('create');
            Route::post('/store', 'DepartmentController@store')->name('store');
            Route::put('update/{id}', 'DepartmentController@update')->name('update');
            Route::delete('destroy/{id}', 'DepartmentController@destroy')->name('destroy');
            Route::get('{id}/edit', 'DepartmentController@edit')->name('edit');
            Route::put('/toogleActive/{id}','DepartmentController@toogleActive')->name('toogleActive');
            Route::post('/check-department-id-unique','DepartmentController@checkDepartmentIdUnique');
            Route::post('/check-department-id-unique-update','DepartmentController@checkDepartmentIdUniqueUpdate');
            Route::post('/check-name-unique','DepartmentController@checkNameUnique');
            Route::post('/check-name-unique-update','DepartmentController@checkNameUniqueUpdate');
            Route::post('/check-isActive-faculty','DepartmentController@checkIsActiveFaculty');
        });
        Route::group([
            'prefix' => 'feedback',
            'as' => 'feedback.'
        ], function () {
            Route::get('/', 'FeedbackController@index')->name('index');
            Route::post('/getData', 'FeedbackController@getData')->name('getData');
            Route::get('show/{id}', 'FeedbackController@show')->name('show');
            Route::put('update/{id}', 'FeedbackController@update')->name('update');
            Route::delete('destroy/{id}', 'FeedbackController@destroy')->name('destroy');
            Route::get('{id}/edit', 'FeedbackController@edit')->name('edit');
            Route::post('/storehandle', 'HandleFeedbackController@store')->name('handle.store');
            Route::get('show/handle/{id}', 'HandleFeedbackController@show')->name('handle.show');

        });

        //===Route Faculty
        Route::group(['prefix' => 'faculty'], function (){
            Route::get('/', 'FacultyController@index')->name('backend.faculty.index');
            Route::get('/getData', 'FacultyController@getData')->name('backend.faculty.getData');
            Route::get('/create', 'FacultyController@create')->name('backend.faculty.create');
            Route::post('/store', 'FacultyController@store')->name('backend.faculty.store');
            Route::put('/update/{id}', 'FacultyController@update')->name('backend.faculty.update');
            Route::delete('/delete/{id}', 'FacultyController@destroy')->name('backend.faculty.destroy');
            Route::put('/status/{id}','FacultyController@status')->name('backend.faculty.status');
            Route::get('/{id}/edit', 'FacultyController@edit')->name('backend.faculty.edit');

            Route::post('/check-faculty-id-unique','FacultyController@checkUniqueFacultyID')->name('backend.faculty.checkUniqueFacultyID');
            Route::post('/check-faculty-name-unique','FacultyController@checkUniqueFacultyName')->name('backend.faculty.checkUniqueFacultyName');
        });

        //===Route room
        Route::group(['prefix' => 'room'], function () {
            Route::get('/', 'RoomController@index')->name('backend.room.index');
            Route::get('/getData', 'RoomController@getData')->name('backend.room.getData');
            Route::get('/create', 'RoomController@create')->name('backend.room.create');
            Route::post('/store', 'RoomController@store')->name('backend.room.store');
            Route::get('/show/{id}', 'RoomController@show')->name('backend.room.show');
            Route::put('/update/{id}', 'RoomController@update')->name('backend.room.update');
            Route::delete('/delete/{id}', 'RoomController@destroy')->name('backend.room.destroy');
            Route::put('/status/{id}','RoomController@status')->name('backend.room.status');
            Route::get('/{id}/edit', 'RoomController@edit')->name('backend.room.edit');

            Route::post('/check-room-id-unique','RoomController@checkRoomIdUnique')->name('backend.room.checkRoomIdUnique');
            Route::post('/check-name-room-unique','RoomController@checkNameRoomUnique')->name('backend.room.checkNameRoomUnique');

        });

        //===Route assignment
        Route::group(['prefix' => 'assignment'], function () {
            Route::get('/', 'AssignmentController@index')->name('backend.assignment.index');
            Route::get('/getData', 'AssignmentController@getData')->name('backend.assignment.getData');
            Route::get('/create', 'AssignmentController@create')->name('backend.assignment.create');
            Route::post('/store', 'AssignmentController@store')->name('backend.assignment.store');
            Route::get('/show/{id}', 'AssignmentController@show')->name('backend.assignment.show');
            Route::get('/{id}/edit', 'AssignmentController@edit')->name('backend.assignment.edit');
            Route::put('/update/{id}', 'AssignmentController@update')->name('backend.assignment.update');
            Route::delete('/delete/{id}', 'AssignmentController@destroy')->name('backend.assignment.destroy');

            Route::post('/check-time-assignment','AssignmentController@checkTimeAssignment')->name('backend.assignment.checkTimeAssignment');
            Route::post('/getTimeOfSemester', 'AssignmentController@getTimeOfSemester')->name('backend.assignment.getTimeOfSemester');
        });

        //===Minh route===
        Route::group([
            'prefix'=>'users',
        ],function (){
            Route::get('/','UserController@index')->name('users.index');
            Route::get('/getdata','UserController@getData')->name('users.getData');
            Route::post('/','UserController@store')->name('users.store');
            Route::get('/show/{id}','UserController@show')->name('users.show');
            Route::delete('/delete/{id}','UserController@destroy')->name('users.delete');
            Route::put('/update/{id}','UserController@update')->name('user.update');
            Route::put('/role/{id}','UserController@role')->name('users.role');
            Route::get('/{id}/edit','UserController@edit')->name('users.edit');
            Route::post('/check-email-unique','UserController@checkEmailUnique');
            Route::post('/check-email-unique-update','UserController@checkEmailUniqueUpdate');
            Route::post('/check-user-name-unique','UserController@checkUserNameUnique');
            Route::post('/check-user-name-unique-update','UserController@checkUserNameUniqueUpdate');
        });

        Route::group(['prefix' => 'schedules'], function () {
            Route::get('/','ScheduleController@index')->name('schedules.index');
            Route::post('/getdata','ScheduleController@getData')->name('schedules.getdata');
            Route::delete('/delete/{id}','ScheduleController@destroy');
            Route::delete('/delete-select','ScheduleController@deleteSelected');
            Route::delete('/comfirm','ScheduleController@comfirm');
            Route::put('/change-status/{id}','ScheduleController@changeStatus');
            Route::get('/show/{id}','ScheduleController@show');
        });

        //===Route statistics
        Route::group(['prefix' => 'statistics'], function (){
            Route::get('/getData-number-session','StatisticsController@getDataNumberSession')->name('backend.statistics.getDataNumberSession');
            Route::get('/number-session','StatisticsController@index')->name('backend.statistics.index');
            Route::get('/getData-Subject-Group','StatisticsController@getDataSubjectGroup')->name('backend.statistics.getDataSubjectGroup');
            Route::get('/subject-group','StatisticsController@statisticsSubjectGroup')->name('backend.statistics.statisticsSubjectGroup');
            Route::post('/get-data-Semester','StatisticsController@getSemester')->name('backend.statistics.getSemester');
        });

        //===End Minh route===

    });
});

//Auth route
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login_from');
Route::post('/login', 'Auth\LoginController@login')->name('login');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

//Frontend route
Route::group([
    'namespace' => 'Frontend',
], function () {
    Route::get('/', function () {
        return redirect()->route('calendar');
    });
    Route::get('/calendar', 'CalendarController@index')->name('calendar');

    Route::group(['prefix' => 'calendar'], function () {
        Route::middleware('admin.user')->group(function(){

            Route::get('/register','CalendarController@create')->name('calendar.register');
            Route::post('/store','CalendarController@store')->name('calendar.store');
            Route::put('/update/{id}','CalendarController@update')->name('calendar.update');
            Route::post('/get-week-now', 'CalendarController@getWeekNow');
            Route::post('/check-unique-schedules','CalendarController@checkUniqueSchedules');
            Route::post('/check-week-semester','CalendarController@checkWeekSemester');
            Route::post('/check-day-start','CalendarController@checkDayStart');
            Route::post('/check-max-week','CalendarController@checkMaxWeek');
            Route::post('/import-excel','CalendarController@importExcel');
            Route::get('/{id}/edit','CalendarController@edit')->name('calendar.edit');


            Route::group(['prefix' => 'register-schedules'], function () {
                Route::get('/','RegisterScheduleController@index')->name('register.schedules.index');
                Route::get('/show/{id}','RegisterScheduleController@show');
                Route::post('/getdata','RegisterScheduleController@getData');
                Route::put('/update/{id}','RegisterScheduleController@update');
                Route::delete('/delete-select','RegisterScheduleController@deleteSelected');
                Route::post('/check-unique-update-schedules/{id}','RegisterScheduleController@checkUniqueUpdateSchedules');
                Route::get('/{id}/edit','RegisterScheduleController@edit');

            });
        });

    });


    Route::get('/home', function (){
        return redirect()->route('calendar');
    })->name('home');

    Route::post('/change-schedules','CalendarController@changeSchedules')->name('calendar.changeSchedules');
    Route::get('/get-schedules','CalendarController@getSchedules')->name('calendar.getSchedules');

});

Route::group(['prefix' => 'profile'], function () {
    Route::middleware('admin.user')->group(function(){
        Route::get('/','Backend\UserController@profile')->name('profile.index');

        Route::put('/update','Backend\UserController@updateProfile')->name('profile.update');
    });
});

Route::middleware('admin.user')->group(function(){
    Route::get('/change-password','Backend\UserController@changePassword')->name('profile.change-password');
    Route::put('/change-password','Backend\UserController@updatePassword')->name('profile.update-password');
    Route::post('/check-old-password','Backend\UserController@checkOldPassWord')->name('profile.check-password');
    Route::put('/update-info','Backend\UserController@updateProfie')->name(('profile.updateProfiel'));
    Route::get('/feedback/create', 'Backend\FeedbackController@create')->name('feedback.create');
    Route::post('/feedback/store', 'Backend\FeedbackController@store')->name('feedback.store');


});
//===End Minh route ===


