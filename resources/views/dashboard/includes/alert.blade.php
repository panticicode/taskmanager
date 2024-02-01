<div class="col-md-12">
    @if(\Session::has('success'))
        <div class="alert alert-success text-center py-0" role="alert">{{ \Session::get('success') }}</div>
    @endif
    @if(\Session::has('danger'))
        <div class="alert alert-danger text-center py-0" role="alert">{{ \Session::get('danger') }}</div>
    @endif
    @if(\Session::has('warning'))
        <div class="alert alert-warning text-center py-0" role="alert">{{ \Session::get('warning') }}</div>
    @endif
    @if(\Session::has('info'))
        <div class="alert alert-info text-center py-0" role="alert">{{ \Session::get('info') }}</div>
    @endif
</div>

