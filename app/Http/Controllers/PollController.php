<?php

namespace App\Http\Controllers;

use App\Poll;
use App\Vote;
use App\Choice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PollController extends Controller
{
    public function generateResult($poll_id, &$result) {
        $data = [];

        $poll = Poll::with('choices')->find($poll_id);

        $divisions = Vote::where('poll_id', $poll_id)->groupBy('division_id')->pluck('division_id');
        foreach($divisions as $division) {
            foreach($poll->choices as $choice) {
                $result[$choice->id] = 0;
                $data["division-$division"][$choice->id] = Vote::where([
                    'division_id' => $division,
                    'poll_id' => $poll->id,
                    'choice_id' => $choice->id
                ])->count();
            }
        }

        return $data;
    }

    public function result($data, &$result) {
        foreach($data as $d) {
            foreach($d as $choices) {
                $max = max($choices);
                $choice = array_keys($choices, $max);

                foreach($choice as $c) {
                    $point = 1 / count($choice);
                    $result[$c] = $result[$c] + $point;
                }
            }
        }
    }

    public function combineChoice($choices, $result) {
        $data = [];

        foreach($choices as $choice) {
            $data[] = [
                'choice_id' => $choice->id,
                'choice' => $choice->choice,
                'point' => $result[$choice->id]
            ];
        }

        return $data;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user())
            return response()->json(['message' => 'Unauthorized'], 401);

        $userHasVoted = Vote::where('user_id', Auth::id())->first();

        $polls = Poll::with(['choices'])->get();

        $result = [];
        $tempResult = [];
        foreach ($polls as $poll) {
            $tempResult["poll-$poll->id"] = $this->generateResult($poll->id, $result);
        }

        $this->result($tempResult, $result);

        foreach($polls as $poll) {
            $choices = $this->combineChoice($poll->choices, $result);
            $poll['result'] = (
                    Auth::user()->role == 'admin' || 
                    $userHasVoted || 
                    Carbon::parse($poll->deadline)->isAfter(now())
                    ) ? $choices : null;
        }

        return response()->json($polls, 200);
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
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin')
            return response()->json(['message' => 'Unauthorized user'], 401);

        $validate = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'deadline' => 'required|date',
            'choices' => 'array|min:2|distinct',
            'choices.*' => 'required|string|distinct'
        ]);

        $param = $request->except('choices');
        $param['created_by'] = Auth::id();
        $poll = Poll::create($param);

        foreach ($request->choices as $choice) {
            Choice::create([
                'choice' => $choice,
                'poll_id' => $poll->id
            ]);
        }

        if ($validate->fails())
            return response()->json(['message' => 'The given data was invalid'], 422);

        return response()->json(['message' => 'success'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function show(Poll $poll)
    {
        $choices = Poll::find($poll->id)->choices;

        $result = [];
        $tempResult[$poll->id] = $this->generateResult($poll->id, $result);

        $this->result($tempResult, $result);

        $poll['result'] = $this->combineChoice($choices, $result);
        $poll['choices'] = $choices;

        return response()->json($poll, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function edit(Poll $poll)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Poll $poll)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function destroy(Poll $poll)
    {
        if (Auth::user()->role !== 'admin')
            return response()->json(['message' => 'Unauthorized user'], 401);

        $poll->delete();

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function vote($poll_id, $choice_id)
    {
        $poll = Poll::find($poll_id);
        $choice = Choice::find($choice_id);

        if (Auth::user()->role === 'admin')
            return response()->json(['message' => 'Unauthorized'], 401);

        if (!Carbon::parse($poll->deadline)->isAfter(now()))
            return response()->json(['message' => 'voting deadline'], 422);

        $isAlreadyVoted = Vote::where([
            'user_id' => Auth::id(),
            'poll_id' => $poll->id
        ])->first();

        if ($isAlreadyVoted)
            return response()->json(['message' => 'already voted'], 422);

        Vote::create([
            'choice_id' => $choice->id,
            'user_id' => Auth::id(),
            'poll_id' => $poll->id,
            'division_id' => Auth::user()->division_id
        ]);

        return response()->json(['message' => 'voting success'], 201);
    }
}
