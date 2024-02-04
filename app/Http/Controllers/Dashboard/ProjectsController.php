<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use Illuminate\Http\Request;
use App\Models\ProjectTask;
use App\Models\Project;
use App\Models\Task;
use DataTables;
use Auth;

class ProjectsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next){
            $this->id = [];
            $this->projects = Project::orderBy('created_at', 'asc')->get();
            $this->user = Auth::user();
            $this->tasks = Task::where('user_id', Auth::id())->select('id', 'name')->get();
            
            return $next($request);
        });
    }
    public function getId()
    {
        return $this->id;
    }
    /**
     * Display a listing of the resource.
     */
    public function index($tasks=null)
    {   
        $this->id = $tasks;
        
        $projects = $this->projects;
        $route = $this->get_url($this->id);

        return view('dashboard.projects.index', compact('projects', 'tasks', 'route'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id=null)
    {
       $route = $this->get_url($id);
       $task = Task::find($id);
       $tasks = $this->tasks;
       return view('dashboard.projects.create', compact('task', 'route', 'tasks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectRequest $request, $id=null)
    {
        if($request->priority == "on")
        {
            $request->priority = true;
        }
        $task = Task::find($id);
        $tasks = Task::whereIn('id', $request->task_id)->get();

        $project = Project::create([
            'name' => $request->name
        ]);
        foreach ($tasks as $key => $task) 
        {
            
           $projectTask = ProjectTask::create([
                'task_id' => $task->id ,
                'project_id' => $project->id
            ]);
        }
        return redirect()->back()->with('success', 'Project Created Successfully');
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
        $id = (int) $request->id;

        if(!$id || is_null($id))
        {
            $projects = Project::orderBy('id', 'asc');
        }
        else
        {
            $projects = Project::whereHas('tasks', function($query) use ($id) {
                $query->where('project_id', $id);
            })->orderBy('id', 'asc');
        }
       
        //$projects = $projects->first()->tasks;

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
                $task = route('dashboard.projects.task', $project->id);
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
    public function project_task_order(Request $request, $tasks) 
    {
        $id = (int) $request->id;

        $projects = Project::query();
        if(!$id || is_null($id))
        {
            $projects->orderBy('id', 'asc');
        }
        else
        {
            $projects->whereHas('tasks', function($query) use ($id, $tasks) {
                $query->where('project_id', (int) $tasks)->tasks;
            })->orderBy('id', 'asc')->tasks;
        }
        $projects = Project::find((int) $tasks)->tasks->toQuery();
        

        if($request->ajax())
        {  
            return Datatables::of($projects)
            //datatables()->eloquent($projects)
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
            ->addColumn('action', function($project) use (&$id)
            {
                $name = $project->name;
           
                $delete = route('dashboard.projects.destroy', $project->id);
                $task = route('dashboard.projects.task', $id+1);
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
        $route = ''; 
        $projects = $this->projects;
        return view('dashboard.projects.tasks', compact('projects', 'route', 'tasks'));
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
    }
    public function tasks(Request $request, $id)
    {
        $tasks = Project::whereHas('tasks', function($query) use ($id){
            $query->where('project_id', $id);
        })->get();
        $projects = $tasks;
        $route = $this->get_url($id);
  
        return view('dashboard.projects.tasks', compact('projects', 'tasks', 'route'));
    }
    public function get_url($id)
    {
        if(is_null($this->tasks))
        {
            $route = route('dashboard.projects.create');
        }
        else
        {
            $route = url('dashboard/tasks') . '/' . $id . '/projects/';
        }
        return $route;
    }
    
}
