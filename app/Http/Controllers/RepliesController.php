<?php

namespace App\Http\Controllers;

use App\User;
use App\Reply;
use App\Thread;
use App\Http\Requests\CreatePostRequest;
use App\Notifications\YouWereMentioned;

class RepliesController extends Controller
{

    public function __construct()
    {
         $this->middleware('auth',['except'=>'index']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->paginate(10);
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
    public function store($channelId,Thread $thread, CreatePostRequest $form)
    {   
        if($thread->locked){
            return response('Thread is locked',422);
        }

        //bermasalah?
        return $thread->addReply([
                'body'=>request('body'),
                'user_id'=>auth()->id()
            ])->load('owner');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function show(Reply $reply)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function edit(Reply $reply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function update(Reply $reply)
    {   
        $this->authorize('update',$reply);
        request()->validate(['body' => 'required|spamfree']);
        $reply->update(request(['body']));
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reply $reply)
    {
        $this->authorize('update',$reply);

        $reply->delete();

        if(request()->expectsJson()){
            return response(['status'=>'Reply deleted']);
        }

        return back();
    }

}
