@extends('layouts.app')

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
                    
                    <form id="createTask" action="{{url('dashboard/projects') . '/tasks/' . $task->id}}" method="post">
                    	@csrf
					  	<div class="mb-3">
					    	<label for="task" class="form-label">Add Project</label>
					    	<input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" autocomplete="off" value="{{ old('name') }}">
                            @error('name')
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
<script type="module" defer>
    let  $action = $('#addTask').attr('action')
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
        
    })
</script>
@endsection