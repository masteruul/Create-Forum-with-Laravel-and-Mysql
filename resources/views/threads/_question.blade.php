{{--Editing the question--}}
    <div class="panel panel-default" v-if="editing">
        <div class="panel-heading">
            <div class="level">
               <input  type="text" class="text form-control" v-model="form.title">
            </div>
        </div>

        <div class="panel-body">
            <div class="form-group">
                <textarea class="form-control" rows="10" v-model="form.body">
                </textarea>                
            </div>
        </div>

        <div class="panel-footer">
            <div class="level">
               <button class="btn btn-xs mr-1" @click="editing = true" v-show="! editing">Edit</button>
                <button class="btn btn-primary btn-xs mr-1" @click="update">Update</button>
                <button class="btn btn-alert btn-xs mr-1" @click="resetForm">Cancel</button>

                @can('update', $thread)
                <form action="{{ $thread->path() }}" method="POST" class="ml-a">
                    {{csrf_field()}}
                    {{method_field('DELETE') }}
                    <button type="submit" class="btn btn-link">Delete Thread</button>
                </form>
                @endcan
            </div>
            
        </div>
    </div>    


{{--Editing the question--}}
    <div class="panel panel-default" v-if="! editing">
        <div class="panel-heading">
            <div class="level">
                <img src="{{$thread->creator->avatar_path}}" alt="{{$thread->creator->name}}" width="25" height="25" class="mr-1">
                <span class="flex">
                    <a href="{{ route('profile',$thread->creator)}}">{{$thread->creator->name}}</a> posted:
                    <span v-text="form.title"></span>
                </span>
            </div>
        </div>

        <div class="panel-body" v-text="form.body">
        </div>

        <div class="panel-footer">
            <button class="btn btn-xs" @click="editing = true" v-if="authorize('updateThread',thread)">Edit</button>
        </div>
    </div>