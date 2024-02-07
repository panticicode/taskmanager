@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                	{{ __('Projects') }}
                	<a href="{{route('dashboard.projects.index')}}" class="btn btn-sm btn-primary float-end">Projects</a>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form id="editProject" action="{{route('dashboard.projects.update', $project->id)}}" method="post">
                    	@csrf
                        @method('put')
					  	<div class="mb-3">
					    	<label for="name" class="form-label">Edit Project</label>
					    	<input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" autocomplete="off" value="{{ old('name') ?? $project->name }}">
                            @error('name')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
					  	</div>
                        <div class="mb-3">
                            <label for="task_id" class="form-label">Task</label>
                            <select id="task_id" class="form-control" name="task_id[]" multiple>
                                <option disabled>Chose Task</option>
                                @foreach($tasks as $key => $task)
                                    <option value="{{ $task->id }}" @if(in_array($task->id, $taskIds)) selected @endif>
                                        {{$task->name}} 
                                    </option>
                                @endforeach
                            </select>
                            @error('task_id')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

					  	<div class="mb-3 form-check">
					    	<input type="checkbox" class="form-check-input @error('priority') is-invalid @enderror" name="priority" id="priority">
					    	<label class="form-check-label" for="priority">Priority</label>
					    	@error('priority')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
					  	</div>
					  	<button type="submit" class="btn btn-primary">Submit</button>
					</form>	
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="module" defer>
    let  $action = $('#editProject').attr('action')
    $(() => {
        //$("img").hide()
        $("input[type='file']").on("change", (evt) => {
            const $this = evt.currentTarget
            const file = $this.files[0]

            if (file) 
            {
                const reader = new FileReader()

                reader.onload = (e) => {
                    $($this).nextAll('img').attr('src', e.target.result).addClass('img-fluid').removeClass('d-none')
                }

                reader.readAsDataURL(file)
            }
        })
        $("#task_id").select2();
    })
</script>
@endsection