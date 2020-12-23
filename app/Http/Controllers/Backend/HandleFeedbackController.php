<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Handle_feedback;
use App\Models\User;
use Illuminate\Http\Request;

class HandleFeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        try {
            $handle_feedack=new Handle_feedback();
            $handle_feedack->feedback_id= $request->get('feedback_id');
            $handle_feedack->description= $request->get('description');
            $handle_feedack->user_id= auth()->user()->id;
            $handle_feedack->user_create_id=  auth()->user()->id;
            $handle_feedack->user_update_id=  auth()->user()->id;
            $handle_feedack->date_confirm=  now();
            $handle_feedack->save();
            $feedback = Feedback::findOrFail($handle_feedack->feedback_id);
            $feedback->handle=1;
            $feedback->save();
            return response()->json(['success' => true],200);
        } catch (ModelNotFoundException $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $handle_feedback=Handle_feedback::findOrFail($id);
            $user=User::findOrFail($handle_feedback->user_id)->full_name;
            $time=strftime("%d -%m -%Y", strtotime($handle_feedback->created_at));
            return response()->json([
                'handle_feedback'=>$handle_feedback,
                'user'=>$user,
                'time'=>$time,
            ]);
        } catch (ModelNotFoundException $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
