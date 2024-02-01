@extends('layouts.app')

@section('content')
<div class="container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        {{ __('Hello') }}<span class="mx-1"></span>{{ Auth::user()->name }}
                        <a href="{{route('dashboard.tasks.create')}}" class="btn btn-sm btn-primary float-end">Create</a>
                        <a href="{{route('dashboard.tasks.show', $task->id)}}" class="btn btn-sm btn-success absolute-right">Preview</a>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form id="createTask" action="{{route('dashboard.tasks.update', $task->id)}}" method="post">
                            @csrf
                            @method('put')
                            
                            <div class="mb-3">
                                <label for="task" class="form-label">Edit Task</label>
                                <input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" autocomplete="off" value="{{ old('name') ?? $task->name }}">
                                @error('name')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <div class="form-text">If this task is important check priority, if not leave blank.</div>
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
</div>
@endsection

@section('scripts')
<script type="module" defer>
    $(() => {
        $("input[type='file']").on("change", (evt) => {
            const $this = evt.currentTarget
            const file = $this.files[0]

            if (file) 
            {
                const reader = new FileReader()

                reader.onload = (e) => {
                    $($this).nextAll('img').attr('src', e.target.result)
                }

                reader.readAsDataURL(file)
            }
        })
        $(".delete-btn").on("click", (evt) => {
            $this = evt.currentTarget

            $($this).parent().closest('.col-md-4').remove()
        })
    })
</script>
@endsection