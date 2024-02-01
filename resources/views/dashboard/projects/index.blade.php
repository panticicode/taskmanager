@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">
              <div class="card-header">
                  <div class="d-flex">
                   Project
                  <a href="{{route('dashboard.projects.create')}}" class="btn btn-sm btn-primary ms-auto me-2">Create</a>
                  <a href="{{route('dashboard.tasks.index')}}" class="btn btn-sm btn-success">Task</a>
                  </div>
              </div>
              <div class="card-body">
                 <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th width="30px">#</th>  
                      <th>Name</th>
                      <th>Created At</th>
                      <th class="text-right">Action</th>
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
          url: "{{ url('dashboard/projects/order_change') }}",
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
            url: "{{ route('dashboard.projects_order') }}",
            data:  (d) => {
                d.search = $('input[type="search"]').val()
            }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', className: ['text-center', 'mx-auto', 'px-0', 'btn-width']},
        ],
        search: {
            input: '#search'
        },
        columnDefs: [{
            targets: [2, 3]
        }],
        rowCallback: (row, data) => {
            $(row).attr('data-id', data.row_id); 
        },
        initComplete:  () => {
            $('input[type="search"]').attr('id', 'search');
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
        
        if(confirm('Are you sure you want to delete this record?'))
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


