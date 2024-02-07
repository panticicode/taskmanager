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

        $tasks = Task::whereIn('id', $request->task_id ?? [])->get();

        $project = Project::create([
            'name' => $request->name
        ]);
        foreach ($tasks as $key => $task) 
        {
            
           $projectTask = ProjectTask::create([
                'user_id' => Auth::id(),
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
       $route = $this->get_url($id);

       $project = Project::find($id);
       $tasks = $this->tasks; 
       $taskIds = $project->project_tasks->pluck('task_id', 'id')->toArray();
       return view('dashboard.projects.edit', compact('project', 'route', 'tasks', 'taskIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjectRequest $request, string $id)
    {
        $project = Project::find($id);
        
        $project->update([
            'name' => $request->name
        ]);

        
        

        
        if($request->has('task_id')) 
        {   
            //Get Ids From relations table
            $ids = array_values($project->tasks()->whereHas('project_task', function($query){})->pluck('task_id', 'task_id')->toArray());
            //Delete Current Tasks  
            $project->tasks()->detach($ids);
         
            $tasks = $request->task_id;

            //Add New Tasks
            foreach($tasks as $task_id) 
            {
                $project->tasks()->attach($task_id, ['user_id' => Auth::id(), 'created_at' => now(), 'updated_at' => now()]);
            }
        }

        return redirect()->back()->with('success', 'Project Created Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Project $project)
    {
        $session = 'danger';
        if($request->has('tasks'))
        {
            $projectTask =  ProjectTask::where('project_id', $project->id)
                                ->where('task_id', (int) $request->tasks)->withTrashed();
            if($projectTask->first()->trashed())
            {
                $projectTask->restore();
                $message = 'Task Restored Successfully';
                $session = 'success';
            }    
            else
            {
                $projectTask->delete();
                $message = 'Task Deleted Successfully';
            }                   
        }
        else
        {
            $message = 'Project Deleted Successfully';
            $project->delete();
        }
        return redirect()->back()->with($session, $message);
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
    public function project_task_order(Request $request, $projectId) 
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
                $query->where('project_id', (int) $projectId)->tasks;
            })->orderBy('id', 'asc')->tasks;
        }
     
        $project = Project::find((int) $projectId); 

        //Check if Project Exist or Not Empty
        
        if($project->tasks->isEmpty())
        {
            $tasks = [];
        }
        else
        {
            $tasks = $project->tasks()->whereHas('project_task', function($query){})->get()->toQuery();
        }

        $taskId = 1;

        if($request->ajax())
        {  
            return Datatables::of($tasks)
            //datatables()->eloquent($projects)
            ->addColumn('row_id', function($task)
            {   
                return $task->id;
            })
            ->editColumn('id', function($task) use (&$taskId) 
            {
                return $taskId++;
            })
            ->editColumn('task', function($task) use (&$taskId) 
            {
                return $task->name;
            })
            ->editColumn('created_at', function($task)
            {
                return $task->created_at->diffForHumans();
            })
            ->editColumn('deleted_at', function($td)
            {
                return $td->deleted_at ? $td->deleted_at->diffForHumans() : [];
            })
            ->addColumn('action', function($task) use (&$id, $projectId)
            {
                $name = $task->name;
                $id = (int) $projectId;

                $delete = route('dashboard.projects.destroy', ['project' => $projectId, 'tasks' => $task->id]);
               
                $manage = route('dashboard.projects.task.manage', ['project' => $id]);
          
                $route = route('dashboard.projects.edit', $task->id);
              
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
        return view('dashboard.projects.tasks', compact('projects', 'route', 'projectId', 'project'));
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
    public function project_tasks(Request $request, Project $project)
    {
        $projectId = $project->id;
       
        $tasks = ProjectTask::where('project_id', $project->id)->withTrashed();
 
        $taskId = 1;
        if($request->ajax())
        {  

            return Datatables::of($tasks)
            //datatables()->eloquent($projects)
            ->addColumn('row_id', function($td) use ($project)
            {    
                return $td->id;
            })
            ->editColumn('id', function($td) use (&$taskId) 
            {
                return $taskId++;
            })
            ->editColumn('task', function($td)
            {
                return $td->task->name;
            })
            ->editColumn('created_at', function($td)
            {
                return $td->created_at->diffForHumans();
            })
            ->editColumn('deleted_at', function($td)
            {
                return $td->deleted_at ? $td->deleted_at->diffForHumans() : [];
            })
            ->addColumn('action', function($td) use (&$id, $projectId)
            {
                $name = $td->task->name;
                $id = (int) $projectId;

                $delete = route('dashboard.projects.destroy', ['project' => $projectId, 'tasks' => $td->task->id]);
               
                $manage = '#';//route('dashboard.projects.task.manage', ['project' => $id]);
          
                $route = route('dashboard.projects.edit', $td->task->id);
              
                $token = csrf_token();
                if($td->trashed())
                {
                    $class = 'btn-success px-1';
                    $btn = 'Restore';
                }
                else
                {
                    $class = 'btn-danger px-2';
                    $btn = 'Delete';
                }
                
                return <<<DELIMITER
                    <div class="d-flex justify-content-end">
                        <a href="$route" class="btn btn-warning btn-sm mb-2 me-1">
                            Edit
                        </a>
                        <form action="$delete" method="post" class="delete-item">
                            <input type="hidden" name="_token" value="$token">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="button" class="btn $class btn-sm mb-2">$btn</button>
                        </form>
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
        return view('dashboard.tasks.projects', compact('projects', 'route', 'projectId', 'project'));
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
