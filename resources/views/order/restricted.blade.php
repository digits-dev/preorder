@extends('crudbooster::admin_template')
@push('head')

@endpush

@section('content')
<div class="modal fade" id="restrictedModal" tabindex="-1" role="dialog" aria-labelledby="restrictedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content panel-danger">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="restrictedModalLabel">Access Restricted</h4>
            </div>
            <div class="modal-body">
                <h3>You do not have permission to access this page at this time.</h3>
                <br>
                <h2 style="color: red;" class="text-center">PLEASE WAIT UNTIL FURTHER NOTICE!</h2>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary pull-left" data-dismiss="modal">Close</button>
                <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('bottom')

<script>
    $(document).ready(function(){
        document.title = "Access Restricted!";
        $('#restrictedModal').modal('show');
    });
</script>
@endpush
