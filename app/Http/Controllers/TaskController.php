<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;

class TaskController extends Controller
{
    protected $tasks;
    public function __construct(TaskRepository $tasks)
    {
        $this->middleware('auth');
        $this->tasks = $tasks;
    }
    public function index(Request $request)
    {
        return view('tasks.index', [
            'tasks' => $this->tasks->forUser($request->user()),
        ]);
    }
    public function store(Request $request)
    {
        try {
            $this->validate($request, ['nama' => 'required|max:191']);
            $request->user()->tasks()->create(['nama' => $request->nama,]);
        } catch (\Throwable $th) {
            return response($th);
        }
        return redirect('/tasks');
    }

    public function edit($id)
    {
        $task = Task::whereId($id)->first();
        return view('tasks/edit')->with('task', $task);
    }

    public function update(Request $request, $id)
    {
        $task = Task::find($id)->update($request->all());
        return redirect('/tasks')->with('succes', 'JOS');
    }

    public function search(Request $request)
    {
        $keyword = $request->search;
        $tasks = Task::where('nama', 'like', '%' . $keyword . "%")
            ->paginate(5);
        return view('tasks.index', compact('tasks'))->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function delete(Request $request, $id)
    {
        $task = Task::find($id)->delete();
        return redirect('/tasks')->with('success', 'Dihapus!');
    }
}