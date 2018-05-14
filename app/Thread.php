<?php

namespace App;

use App\Filters\ThreadFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\ThreadSubcriptions;
use App\Notifications\ThreadWasUpdated;
use App\Events\Event\ThreadHasNewReply;
use App\Events\Event\ThreadReceviedNewReply;



class Thread extends Model
{
    use RecordsActivity;
    protected $guarded = [];
    protected $with = ['creator','channel'];
    protected $appends = ['isSubscribedTo'];


    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($thread){
            $thread->replies->each->delete();
        });

    }

    public function path()
    {
      return "/threads/{$this->channel->slug}/{$this->id}";
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


    //public function notifySubscribers($reply)
    //{
    //  $this->subscriptions
    //    ->where('user_id','!=',$reply->user_id)
    //    ->each
    //    ->notify($reply);
    // }

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
}
