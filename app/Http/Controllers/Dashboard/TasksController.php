<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use Illuminate\Http\Request;
use App\Models\Task;
use DataTables;
use Auth;

class TasksController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next){
            $this->tasks = Task::orderBy('priority', 'asc')->get();
            $this->user = Auth::user();
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = $this->tasks;

        return view('dashboard.tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request)
    {
        if($request->priority == "on")
        {
            $request->priority = true;
        }

        $task = Task::create([
            'user_id' => Auth::id(),
            'name' => $request->name
        ]);
        $task->update(['priority' => $task->id]);
        return redirect()->route('dashboard.tasks.index')->with('success', 'Task Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        dd($task);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Task $task)
    {
        $task = Task::findOrFail($task->id);
        return view('dashboard.tasks.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, Task $task)
    {
        $task->name = $request->name;
        $task->save();
        return redirect()->route('dashboard.tasks.index')->with('info', 'Task Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Task $task)
    {
        $task->delete();
        return redirect()->back()->with('danger', 'Task Deleted Successfully');
    }
    /**
     * Reorder specified task from his position.
     */
    public function task_order(Request $request)
    {
        $tasks = Task::orderBy('priority', 'asc');
        $id = 0; 
        
        if($request->ajax())
        {
            return Datatables::of($tasks)
            ->addColumn('row_id', function($task)
            {
                return $task->id;
            })
            ->editColumn('id', function($task) use (&$id) 
            {
                $id++; 
                return $id;
            })
            ->editColumn('created_at', function($task)
            {
                return $task->created_at->format('d/m/Y');
            })
            ->addColumn('action', function($task)
            {
                $name = $task->name;

                $route = route('dashboard.tasks.edit', $task->id);
                return '
                    <div class="d-flex justify-content-center">
                        <a href="'. $route .'" class="btn btn-warning btn-sm mb-2 me-1">
                            Edit
                        </a>
                        <form action="'.  route('dashboard.tasks.destroy', $task->id) .'" method="post" class="delete-item">
                            <input type="hidden" name="_token" value="'.csrf_token().'">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="button" class="btn btn-danger btn-sm mb-2">Delete</button>
                        </form>
                        <a href="' .  url('dashboard/projects/tasks') . '/' . $task->id . '" class="btn btn-primary btn-sm ms-1 mb-2 d-none">Project</a>
                    </div>    
                ';
            })
            ->filter(function ($instance) use ($request) {
                
                if($request->search)
                { 
                    $search = $request->search;

                    $instance->where(function($query) use ($search){
                        $query->where('id', 'like', '%' . $search . '%');
                        $query->orWhere('name', 'like', '%' . $search . '%');
                        $query->orWhereDate('created_at', '=', date('Y-m-d', strtotime($search)));
                    });
                }
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('dashboard.tasks.index', compact('tasks'));

    }
    public function task_order_change(Request $request)
    {
        $data = $request->order;

        foreach ($data as $index => $id) 
        {
            Task::where('id', $id)->update(['priority' => $index]);
        }
        session()->flash('success', 'Task Order changed successfully.');
        return  response()->json([
            'message' => $request->session()->get('success'),
            'alert_type' => 'success'
        ]);
    //return response()->json(['success' => $data]);
    }
}
