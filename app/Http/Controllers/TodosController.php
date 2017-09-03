<?php

namespace App\Http\Controllers;

use App\Todo;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Auth;

class TodosController extends Controller
{
    public function index()
    {
        if (Auth::guest()) {
            return redirect('/login');
        }

        $todos = Auth::user()->orderedTodos();

        return view('todos.index', compact('todos'));
    }

    public function store()
    {
        if (Auth::guest()) {
            return response()->json(['errors' => new MessageBag([
                'authorization' => 'Only authenticated users can create todos.',
            ])], 401);
        }

        $data = request()->only(['name', 'weight']);

        $validator = Validator::make($data, [
            'name' => ['required'],
            'weight' => ['numeric', 'nullable'],
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (array_key_exists('weight', $data)) {
            if ($data['weight'] == null) {
                $data['weight'] = 0;
            }
        } else {
            $data['weight'] = 0;
        }

        $todo = Auth::user()->todos()->create([
            'name' => $data['name'],
            'completed' => false,
            'weight' => (int) $data['weight'],
        ]);

        return response()->json(['todo' => $todo], 200);
    }

    public function update($id)
    {
        $todo = Todo::find($id);

        if (request('completed') !== null) {
            if (request('completed')) {
                $todo->complete();
            } else {
                $todo->markIncomplete();
            }
        }

        $todo->update([
            'name' => request('name') ? request('name') : $todo->name,
        ]);

        return response()->json(['todo' => $todo], 200);
    }
}
