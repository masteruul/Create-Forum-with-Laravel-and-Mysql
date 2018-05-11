@extends('layouts.app')

@section('content')
<thread-view :initial-replies-count="{{ $thread->replies_count }}" inline-template>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="level">
                            <span class="flex">
                                <a href="{{ route('profile',$thread->creator)}}">{{$thread->creator->name}}</a> posted:  
                                {{ $thread->title }}
                            </span>

                            @can('update', $thread)
                            <form action="{{ $thread->path() }}" method="POST">
                                {{csrf_field()}}
                                {{method_field('DELETE') }}
                                <button type="submit" class="btn btn-link">Delete Thread</button>
                            </form>
                            @endcan
                        </div>
                    </div>

                    <div class="panel-body">
                        {{$thread->body}}
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p>
                            This thread was published {{ $thread->created_at->diffForHumans()}} by
                            <a href="#">{{$thread->creator->name}}</a>, and currently
                            has <span v-text="repliesCount"></span>
                        </p>
                        <p>
                            <subscribe-button :active="{{json_encode($thread->isSubscribedTo)}}"></subscribe-button>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <replies @added="repliesCount++" @removed="repliesCount--"></replies>

    </div> 
    
</thread-view>
@endsection
