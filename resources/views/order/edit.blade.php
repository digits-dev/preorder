@extends('crudbooster::admin_template')
@section('content')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.min.css" integrity="sha256-F2TGXW+mc8e56tXYBFYeucG/SgD6qQt4SNFxmpVXdUk=" crossorigin="anonymous">

<style type="text/css">

table.table.table-bordered td {
  border: 1px solid black;
}

table.table.table-bordered tr {
  border: 1px solid black;
}

table.table.table-bordered th {
  border: 1px solid black;
}

.noselect {
  -webkit-touch-callout: none; /* iOS Safari */
    -webkit-user-select: none; /* Safari */
     -khtml-user-select: none; /* Konqueror HTML */
       -moz-user-select: none; /* Old versions of Firefox */
        -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; /* Non-prefixed version, currently supported by Chrome, Edge, Opera and Firefox */
}


</style>
@endpush

    @if(g('return_url'))
        <p><a title='Return' href='{{g("return_url")}}' class="noprint"><i class='fa fa-chevron-circle-left'></i>
        &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>
    @else
        <p><a title='Main Module' href='{{CRUDBooster::mainpath()}}' class="noprint"><i class='fa fa-chevron-circle-left'></i>
        &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class='panel panel-default'>
        <div class='panel-heading'>
        <h3 class="box-title text-center"><b>Pre Order Edit</b></h3>
        </div>
        <form action="{{ route('preorder.order-edit') }}" method="POST" id="preorder" autocomplete="off" role="form" enctype="multipart/form-data">
        <input type="hidden" name="_token" id="token" value="{{csrf_token()}}" >
        <input type="hidden" name="order_id" id="order_id" value="{{$order_details->id}}">
        <input type="hidden" id="order_payment" value="{{$order_details->pay_status}}">

        <div class='panel-body' id="order-details">

            <div class="col-md-6">
                <div class="table-responsive">
                    <table class="table table-bordered" id="order-details-1">
                        <tbody>
                            <tr>
                                <td style="width: 25%">
                                    <b>Customer Name:</b>
                                </td>
                                <td colspan="2">
                                    {{ $order_details->customer_name }}
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 25%">
                                    <b>Customer Email:</b>
                                </td>
                                <td colspan="2">
                                    {{ $order_details->email_address }}
                                </td>
                            </tr>

                            <tr>
                                <td style="width: 25%">
                                    <b>Customer Contact #:</b>
                                </td>
                                <td colspan="2">
                                    {{ $order_details->contact_number }}
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 25%">
                                    <b>Claiming Invoice#:</b>
                                </td>
                                <td style="width: 25%">
                                    <div class="input-group date">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input class="form-control" type="text" name="claimed_date" id="claimed_date" value="{{ (old('claimed_date')) ?  old('claimed_date') : $order_details->claimed_date }}" readonly>
                                    </div>
                                </td>
                                <td>
                                    <input class="form-control" type="text" style="width:100%" placeholder="INV#" name="claiming_invoice_number" id="claiming_invoice_number" value="{{ (old('claiming_invoice_number')) ?  old('claiming_invoice_number') : $order_details->claiming_invoice_number }}" >

                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-6">
                <div class="table-responsive">
                    <table class="table table-bordered" id="order-details-2">
                        <tbody>
                            <tr>
                                <td style="width: 25%">
                                    <b>Order Ref#:</b>
                                </td>
                                <td>
                                    {{ $order_details->reference }}
                                </td>
                                <td style="width: 30%">
                                    {!! $order_details->order_status !!}
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 25%">
                                    <b>Store:</b>
                                </td>
                                <td colspan="2">
                                    {{ $order_details->store_name }}
                                </td>
                            </tr>

                            <tr>
                                <td style="width: 25%">
                                    <b>Mode of Payment:</b>
                                </td>
                                <td colspan="2">
                                    {{ $order_details->payment_method }}
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 25%">
                                    <b>Reservation Invoice#:</b>
                                </td>
                                <td colspan="2">
                                    <input class="form-control" type="text" style="width:100%" placeholder="INV#" name="invoice_number" id="invoice_number" value="{{ (old('invoice_number')) ?  old('invoice_number') : $order_details->invoice_number }}" >

                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <br>

            <div class="col-md-12">
                <div class="box-header text-center">
                    <h3 class="box-title"><b>Order Items</b></h3>
                </div>

                <div class="box-body no-padding">
                    <div class="table-responsive">
                        <table class="table table-bordered noselect" id="dr-items">
                            <thead>
                                <tr style="background: #0047ab; color: white">
                                    <th width="10%" class="text-center">{{ trans('label.table.digits_code') }}</th>
                                    <th width="25%" class="text-center">{{ trans('label.table.item_description') }}</th>
                                    <th width="10%" class="text-center">{{ trans('label.table.qty') }}</th>
                                    <th width="10%" class="text-center">{{ trans('label.table.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order_items as $item)
                                    <tr>
                                        <td class="text-center">{{$item['digits_code']}} </td>
                                        <td>{{$item['item_description']}}</td>
                                        <td class="text-center">{{$item['qty']}}</td>
                                        <td class="text-center">{{ number_format($item['amount'],2,".",",") }}</td>
                                    </tr>
                                @endforeach

                                <tr class="tableInfo">
                                    <td colspan="2" align="right"><strong>{{ trans('label.table.total_quantity') }}</strong></td>
                                    <td align="center" colspan="1">{{$order_details->total_qty}}</td>
                                    <td align="center" colspan="1">P {{ number_format($order_details->total_amount,2,".",",") }}</td>

                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            </div>

        <div class='panel-footer'>
            @if(g('return_url'))
            <a href="{{ g("return_url") }}" class="btn btn-default">{{ trans('label.form.back') }}</a>
            <button class="btn btn-primary pull-right" type="submit" id="btnSubmit"> <i class="fa fa-save" ></i> {{ trans('label.form.save') }}</button>
            @else
            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('label.form.back') }}</a>
            <button class="btn btn-primary pull-right" type="submit" id="btnSubmit"> <i class="fa fa-save" ></i> {{ trans('label.form.save') }}</button>
            @endif
        </div>
    </form>
    </div>

@endsection
@push('bottom')
<script
      src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"
      integrity="sha512-37T7leoNS06R80c8Ulq7cdCDU5MNQBwlYoy1TX/WUsLFC2eYNqtKlV0QjH7r8JpG/S0GUMZwebnVFLPd6SU5yg=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.min.js" integrity="sha256-CT21YfDe01wscF4AKCPn7mDQEHR2OC49jQZkt5wtl0g=" crossorigin="anonymous"></script>

<script type="text/javascript">
$(document).ready(function() {

    $(function(){
        $('body').addClass("sidebar-collapse");
    });

    $('#claimed_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        minDate: 0,
        dateFormat: 'yy-mm-dd'
    });

    if($("#order_payment").val() == "PAID"){
        $("#claimed_date").datepicker().datepicker("setDate", new Date());
        $("#claimed_date").prop("required",true);
        $("#claiming_invoice_number").prop("required",true);
    }
    else if($("#order_payment").val() == "RESERVED"){
        $("#claimed_date").datepicker("disable");
        $("#claiming_invoice_number").prop("disabled","disabled");
    }

    $('#btnSubmit').bind('keypress keydown keyup', function(e){
       if(e.keyCode == 13) { e.preventDefault(); }
    });

    $("#btnSubmit").click(function(event) {
        event.preventDefault();

        if($("#preorder").valid()){
            Swal.fire({
              title: 'Do you want to save the changes?',
              icon: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Save',
              cancelButtonText: 'Cancel',
            }).then((result) => {
              if (result.isConfirmed) {
                    $(this).prop("disabled", true);
                    $("#preorder").submit();
              }
            });

        }
    });

});
</script>
@endpush
