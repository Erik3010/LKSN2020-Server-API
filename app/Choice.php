<?php

namespace App;

use App\Vote;
use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    protected $guarded = [];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    // public function pollResult()
    // {
    //     $choices = $this->poll->choices;

    //     $divisions = Vote::where('poll_id', $this->poll->id)->groupBy('division_id')->pluck('division_id');

    //     foreach ($divisions as $division) {
    //         $votes = Vote::where(['poll_id' => $this->poll->id, 'division_id' => $division])->get();
    //         foreach ($votes as $vote) {

    //         }
    //     }
    // }
}
