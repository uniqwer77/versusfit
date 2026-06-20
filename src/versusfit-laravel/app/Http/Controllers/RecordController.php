<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Http\Request;

class RecordController extends Controller
{
    public function store(Request $request) 
    {
        $data = $request->validate([ 
            'challenge_id' => 'required|exists:challenges,id', 
            'value' => 'required|integer|min:1', 
        ]); 

        Record::updateOrCreate(
            [
                'challenge_id' => $data['challenge_id'],
                'user_id'      => $request->user()->id,
            ],
            [
                'value'        => $data['value'] 
            ]
        );

        return back()->with('success', 'Результат успешно обновлен!'); 
    }
}
