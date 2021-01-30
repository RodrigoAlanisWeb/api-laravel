<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function getAll()
    {
        $user = Auth::user();

        return response(['success' => true, 'tasks' => $user->tasks]);
    }

    public function getOne(Task $task)
    {
        if ($task->user_id == Auth::user()->id) {
            return response(['success' => true, 'task' => $task]);
        } else {
            return response(['success' => false, 'msg' => 'You Are Not The Owner Of The Task']);
        }
    }

    public function create(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:5|max:100',
            'description' => 'required|string'
        ]);

        $user = Auth::user();

        $task = Task::create([
            "title" => $request->title,
            "description" => $request->description,
            "user_id" => $user->id,
        ]);

        if ($task) {
            return response(['success' => true, 'task' => $task]);
        } else {
            return response(['success' => false]);
        }
    }

    public function delete(Task $task)
    {
        if ($task->user_id == Auth::user()->id) {
            $task->delete();
        } else {
            return response(['success' => false, 'msg' => 'You Are Not The Owner Of The Task']);
        }

        return response(['success' => true, 'msg' => 'Task Deleted Successfully']);
    }

    public function edit(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'string|min:5|max:100',
            'description' => 'string|min:10'
        ]);

        if ($task->user_id == Auth::user()->id) {
            if (isset($request->title)) {
                $task->title = $request->title;
            }

            if (isset($request->description)) {
                $task->description = $request->description;
            }

            $task->save();
        } else {
            return response(['success' => false, 'msg' => 'You Are Not The Owner Of The Task']);
        }

        return response(['success' => true, 'task' => $task]);
    }

    public function done(Task $task)
    {
        if ($task->user_id == Auth::user()->id) {
            $task->status = true;
            $task->save();
        } else {
            return response(['success' => false, 'msg' => 'You Are Not The Owner Of The Task']);
        }

        return response(['success' => true, 'task' => $task]);
    }
}
