<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class RecordController extends Controller
{
    public function store(Request $request) 
    {
        $data = $request->validate([ 
            'challenge_id' => 'required|exists:challenges,id', 
            'value' => 'required|numeric|min:1', 
        ]); 

        $record = Record::firstOrCreate(
            [
                'challenge_id' => $data['challenge_id'],
                'user_id'      => $request->user()->id,
            ],
            [
                'value'        => 0 
            ]
        );

        $record->increment('value', $data['value']);

        $record->refresh(); 

        Redis::publish('challenge_updates', json_encode([
            'type' => 'leaderboard_update',
            'challenge_id' => (int)$record->challenge_id,
            'user_id' => (int)$record->user_id,
            'name' => $request->user()->name, 
            'challenge_value' => (int)$record->value,  
            'created_at' => $record->updated_at->toISOString(),
        ]));

        return back()->with('success', 'Результат успешно обновлен!'); 
    }
}
