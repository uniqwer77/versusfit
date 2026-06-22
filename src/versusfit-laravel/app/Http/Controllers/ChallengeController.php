<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Redis;

class ChallengeController extends Controller
{
    use AuthorizesRequests;
    
    public function index()
    {
        $challenges = Challenge::with('owner')
            ->latest()
            ->paginate(10); 
        return view('challenges.index', compact('challenges'));
    }

    public function create()
    {
        $this->authorize('create', Challenge::class);

        return view('challenges.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Challenge::class);

        $data = $request->validate([ 
            'title' => 'required|string|max:200', 
            'description' => 'nullable|string|max:5000', 
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
        ]); 
        $data['owner_id'] = $request->user()->id;

        $challenge = $request->user()->challenges()->create($data); 

        return redirect()->route('challenges.show', $challenge) 
            ->with('success', 'Челлендж создан');
    }

    public function show(Challenge $challenge)
    {
        $challenge->load('owner');
        $members = $challenge->members()
            ->withSum(['records' => function ($query) use ($challenge) {
                $query->where('challenge_id', $challenge->id);
            }], 'value') 
            ->orderByDesc('records_sum_value') 
            ->get();

        $totalDistance = $challenge->records()->sum('value');

        $isJoined = $challenge->members()->where('user_id', auth()->id())->exists();

        return view('challenges.show', compact('challenge', 'members', 'totalDistance', 'isJoined'));
    }

    public function edit(Challenge $challenge)
    {
        $this->authorize('update', $challenge);

        return view('challenges.edit', compact('challenge'));
    }

    public function update(Request $request, Challenge $challenge)
    {
        $this->authorize('update', $challenge);

        $data = $request->validate([ 
            'title' => 'required|string|max:200', 
            'description' => 'nullable|string|max:5000', 
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
        ]);

        $challenge->update($data);

        return redirect()->route('challenges.show', $challenge) 
            ->with('success', 'Челлендж обновлен');
    }

    public function destroy(Challenge $challenge)
    {
        $this->authorize('delete', $challenge);

        $challenge->delete(); 
        
        return redirect()->route('challenges.index') 
            ->with('success', 'Челлендж удален');
    }

    public function join(Request $request,Challenge $challenge)
    {
        if (!$challenge->members()->where('user_id', auth()->id())->exists()) {
            $challenge->members()->attach(auth()->id());
        }

        // Redis::publish('challenge_updates', json_encode([
        //     'type' => 'challenge_update',
        //     'challenge_id' => $challenge->challenge_id,
        //     'user_id' => auth()->id(),
        //     'name' => $request->user()->name,
        // ]));

        return redirect()->back()->with('success', 'Вы присоединились к челленджу!');
    }
}
