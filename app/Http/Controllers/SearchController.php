<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Thread;
use App\Trending;

class SearchController extends Controller
{
    public function show(Trending $trending)
    {
        if(request()->expectsJson()){
            return Thread::search(request('q'))->paginate(10);        
            
        }

        return view('threads.search',[
            //'threads'=>$threads,
            'trending'=>$trending->get()
        ]);
    }
}
