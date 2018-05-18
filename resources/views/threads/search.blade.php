@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <ais-index
            app-id="{{ config('scout.algolia.id')}}"
            api-key="{{ config('scout.algolia.key')}}"
            index-name="threads"
            query="{{ request('q') }}"
        >
                
            <div class="col-md-8">
                <ais-results>
                    <template scope="{ result }">
                            <div class="panel panel-default">
                                <div class="panel-heading">          
                                    <div class="level">
                                        <div class="flex">
                                        <h4>
                                            <a :href="result.path">
                                            <strong>
                                                <ais-highlight :result="result" attribute-name="title"></ais-highlight>                                
                                            </strong>
                                            </a>
                                        </h4>
                                    </div>
                                </div>
                                <div class="panel-body">     
                                    <div class="body">
                                        <ais-highlight :result="result" attribute-name="body"></ais-highlight>
                                    </div>                        
                                </div>
                            </div>
                    </template>
                </ais-results>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Search
                    </div>
                    <div class="panel-body">
                        <ais-search-box>
                            <ais-input placeholder="Find a thread..." :autofocus="true" class="form-control">
                            </ais-input>
                        </ais-search-box>
                    </div> 
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Filter
                    </div>
                    <div class="panel-body">
                        <ais-refinement-list attribute-name="channel.name"> </ais-refinement-list>
                    </div> 
                </div>
                @if(count($trending))
                        <div class="panel-heading">
                            Trending Threads
                        </div>

                        <div class="panel-body">
                            <ul class="list-group">
                                @foreach($trending as $thread)
                                <li class="list-group-item">
                                        <a href="{{url($thread->path)}}}">
                                        {{$thread->title}}
                                        </a>                           
                                </li> 
                                @endforeach
                            </ul>
                        </div>
                @endif
                </div>
            </div>
        </ais-index>
    </div>
</div>
@endsection
