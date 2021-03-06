<?php

namespace App;

use App\Filters\ThreadFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis; 
use App\ThreadSubcriptions;
use App\Notifications\ThreadWasUpdated;
use App\Events\Event\ThreadHasNewReply;
use App\Events\Event\ThreadReceviedNewReply;
//use Laravel\Scout\Searchable;
use Stevebauman\Purify\PurifyServiceProvider;


class Thread extends Model
{
    //use RecordsActivity,Searchable;
    use RecordsActivity;
    protected $guarded = [];
    protected $with = ['creator','channel'];
    protected $appends = ['isSubscribedTo'];
    protected $casts = ['locked'=>'boolean'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($thread){
            $thread->replies->each->delete();
        });

        static::created(function ($thread){
          $thread->update(['slug'=>$thread->title]);
        });

    }

    public function path()
    {
      return "/threads/{$this->channel->slug}/{$this->slug}";
    }

    public function replies()
    {
      return $this->hasMany(Reply::class);
    }

    public function creator()
    {
      return $this->belongsTo(User::class,'user_id');
    }

    public function addReply($reply)
    {
      //bermasalah?
      $reply= $this->replies()->create($reply);

      event(new ThreadReceviedNewReply($reply));
      
      return $reply;
    }
    
    public function channel(){
      return $this->belongsTo(Channel::class);
    }

    /**
     * Apply all relevant thread filters.
     *
     * @param  Builder       $query
     * @param  ThreadFilters $filters
     * @return Builder
     */
    public function scopeFilter($query,ThreadFilters $filters)
    {
      return $filters->apply($query);
    }

    public function subscribe($userId = null)
    {
      $this->subscriptions()->create([
        'user_id' => $userId ?: auth()->id()
      ]);

      return $this;
    }

    public function unsubscribe($userId=null)
    {
      $this->subscriptions()
        ->where('user_id',$userId ?:auth()->id())
        ->delete();
        
    }

    public function subscriptions()
    {
      return $this->hasMany(ThreadSubscription::class);
    }

    public function getIsSubscribedToAttribute()
    {
      return $this->subscriptions()
        ->where('user_id',auth()->id())
        ->exists();
    }

    public function hasUpdatesFor($user)
    {
      $key= $user->visitedThreadCacheKey($this);      
      return $this->updated_at > cache($key);
    }

    public function getRouteKeyName()
    {
      return 'slug';
    }

    public function setSlugAttribute($value)
    {
      if(static::whereSlug($slug = str_slug($value))->exists()){
        $slug = "{$slug}-{$this->id}";
      }

      $this->attributes['slug'] = $slug;
    } 

    public function markBest($reply)
    {
      $this->update(['best_reply_id'=> $reply->id]);
    }

    public function toSearchableArray()
    {
      return $this->toArray()+['path'=>$this->path()];
    }

    public function getBodyAttribute($body)
    {
      return \Purify::clean($body);
    }
}
