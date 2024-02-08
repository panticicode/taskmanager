@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">
              <div class="card-header">
                  <div class="d-flex">
                   Project
                   <select id="project" class="form-select form-select-sm w-50 ms-3" name="assign">
                      <option disabled selected>Choose Project</option>
                      @foreach($projects as $item)
                        <option value="{{$item->id}}">{{$item->name}}</option>
                      @endforeach
                   </select>
                  <a href="{{route('dashboard.projects.create')}}" class="btn btn-sm btn-primary ms-auto me-2">Create</a>
                  <a href="{{route('dashboard.projects.index')}}" class="btn btn-sm btn-success">Projects</a>
                  </div>
              </div>
              <div class="card-body">
                 <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>#</th>  
                      <th>Task</th>
                      <th>Created</th>
                      <th>Deleted</th>
                      <th class="d-flex justent center">Action</th>
                    </tr>
                  </thead>
                  <tbody id="tableOfContents"></tbody>                  
                </table>
              </div>
          </div>  
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="module">
    let sendOrderToServer = () => {
        var order = [];
        var token = $('meta[name="csrf-token"]').attr('content');
        //by this function User can Update hisOrders or Move to top or under
        $('#tableOfContents tr').each((i, e) => {
          order.push({
            id: $(e).attr('data-id'),
            position: i+1
          })
        })
        // the Ajax Post update 
        $.ajax({
          type: "POST", 
          dataType: "json", 
          url: "{{route('dashboard.projects.task.manage', $project->id)}}",
              data: {
            order: order,
            _token: token
          },
          success: (res) => {
              if (res.status == "success") 
              {
                console.log(res)
              } 
              else
              {
                  //console.log(res)
                  table.draw()
                  $('main div').first().html(`
                      <div class="alert alert-${res.alert_type} text-center py-0" role="alert">
                        ${res.message}
                      </div>
                  `)
                  setTimeout(() => {
                      $(".alert").fadeOut(750)
                  }, 1000)
              }
          }
        })
    }

    var table = $("table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('dashboard.projects.task.manage', ['project' => $project->id]) }}",
            data:  (d) => {
                d.search = $('input[type="search"]').val()
                d.assign = $('select[name="assign"]').val()
            }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'task', name: 'task'},
            {data: 'created_at', name: 'created_at'},
            {data: 'deleted_at', name: 'deleted_at'},
            {data: 'action', name: 'action', className: ['text-center', 'mx-auto', 'px-0', 'btn-width']},
        ],
        search: {
            input: '#search',
        },
        assign : {
            input: '#project'
        },
        columnDefs: [{
            targets: [2, 3, 4]
        }],
        rowCallback: (row, data) => {
            $(row).attr('data-id', data.row_id); 
        },
        initComplete:  (settings, json) => {
            $('input[type="search"]').attr('id', 'search');

            //console.log(json)
            // Filter results on select change
            $('#project').on('change', (evt) => {
                var $this = evt.currentTarget

                table.ajax.url('{{ url("dashboard/projects/tasks") }}' + '/' + $this.value).draw();
            });
        }
    })
    $('#search').on('keyup', (evt) => {
        $this = evt.currentTarget
        table.ajax.url('{{ route("dashboard.projects_order") }}?search=' + $this.value).draw();
    });
    $( "#tableOfContents" ).sortable({
        items: "tr",
        cursor: 'move',
        update: () => {
            sendOrderToServer();
        }
    })
    setTimeout(() => {
        $(".alert").fadeOut(750)
    }, 1000)
    $(document).on("click", ".delete-item button", (evt) => {
        let $this = evt.currentTarget
        
        if(confirm('Are you sure you want to ' + $($this).text().toLowerCase() + ' this record?'))
        {
            $($this).parent().submit()
            return true;
        }
        else
        {
            return false
        }
    })
</script>
@endsection


