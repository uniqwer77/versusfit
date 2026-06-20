<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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

        $challenge = $request->user()->challenges()->create($data); 

        return redirect()->route('challenges.show', $challenge) 
            ->with('success', 'Челлендж создан');
    }

    public function show(Challenge $challenge)
    {
        $challenge->load('owner');

        // $members = $challenge->members()
        //     ->withSum(['records' => function ($query) use ($challenge) {
        //         $query->where('challenge_id', $challenge->id);
        //     }], 'value')
        //     ->orderByDesc('records_sum_distance') 
        //     ->get();

        // $totalDistance = $challenge->records()->sum('value');

        $members = $challenge->members()
            ->withSum(['records' => function ($query) use ($challenge) {
                $query->where('challenge_id', $challenge->id);
            }], 'value') // Считаем сумму колонки `value`
            ->orderByDesc('records_sum_value') // Сортируем по созданному Laravel полю `records_sum_value`
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

    public function join(Challenge $challenge)
    {
        if (!$challenge->members()->where('user_id', auth()->id())->exists()) {
            $challenge->members()->attach(auth()->id());
        }

        return redirect()->back()->with('success', 'Вы присоединились к челленджу!');
    }
}
