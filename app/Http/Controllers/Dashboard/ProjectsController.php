<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use DataTables;
use Auth;

class ProjectsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next){
            $this->projects = Project::orderBy('created_at', 'asc')->get();
            $this->user = Auth::user();
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     */
    public function index($tasks = null)
    {
        $projects = $this->projects;

        return view('dashboard.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id=null)
    {
       if(is_null($id))
       {
            return redirect()->back()->with('warning', 'Please select a task before adding Project.');
       }
       $task = Task::find($id);
       return view('dashboard.projects.create', compact('task'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectRequest $request, $id)
    {
        if($request->priority == "on")
        {
            $request->priority = true;
        }
        $task = Task::find($id);
        if(is_null($task))
        {
            return redirect()->back()->with('warning', 'Please select a task before adding Project.');
        }
        $project = Project::create([
            'task_id' => $task->id,
            'name' => $request->name
        ]);
        return redirect()->route('dashboard.projects.index')->with('success', 'Project Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Project $project)
    {
        $project->delete();
        return redirect()->back()->with('danger', 'Project Deleted Successfully');
    }
    public function project_order(Request $request)
    {
        $projects = Project::orderBy('id', 'asc');
        $id = 0; 
        
        if($request->ajax())
        {
            return Datatables::of($projects)
            ->addColumn('row_id', function($project)
            {
                return $project->id;
            })
            ->editColumn('id', function($project) use (&$id) 
            {
                $id++; 
                return $id;
            })
            ->editColumn('created_at', function($project)
            {
                return $project->created_at->diffForHumans();
            })
            ->addColumn('action', function($project)
            {
                $name = $project->name;
                $delete = route('dashboard.projects.destroy', $project->id);
                $task = route('dashboard.tasks.index');
                $route = route('dashboard.projects.edit', $project->id);
                $token = csrf_token();

                return <<<DELIMITER
                    <div class="d-flex justify-content-end">
                        <a href="$route" class="btn btn-warning btn-sm mb-2 me-1">
                            Edit
                        </a>
                        <form action="$delete" method="post" class="delete-item">
                            <input type="hidden" name="_token" value="$token">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="button" class="btn btn-danger btn-sm mb-2">Delete</button>
                        </form>
                        <a href="$task" class="btn btn-primary btn-sm mb-2 ms-1">Task</a>
                    </div>    
                DELIMITER;
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
        return view('dashboard.projects.index', compact('projects'));

    }
    public function project_order_change(Request $request)
    {
        $data = $request->order;

        foreach ($data as $index => $id) 
        {
            Project::where('id', $id)->update(['priority' => $index]);
        }
        session()->flash('success', 'Project Order changed successfully.');
        return  response()->json([
            'message' => $request->session()->get('success'),
            'alert_type' => 'success'
        ]);
    //return response()->json(['success' => $data]);
    }
}
