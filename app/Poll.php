<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Poll extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $appends = ['creator'];

    protected $hidden = ['user', 'updated_at'];

    public function choices()
    {
        return $this->hasMany(Choice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCreatorAttribute()
    {
        return $this->user->username;
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
