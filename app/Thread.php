<?php

namespace App;

use App\Filters\ThreadFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\ThreadSubcriptions;
class Thread extends Model
{
    use RecordsActivity;
    protected $guarded = [];
    protected $with = ['creator'];

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
      return  $this->replies()->create($reply);
      
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
}
