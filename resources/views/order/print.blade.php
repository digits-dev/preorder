@extends('crudbooster::admin_template')
@section('content')

@push('head')
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
@media print {
    .no-print{
        display: none;
    }
}


</style>
@endpush

    @if(g('return_url'))
        <p><a title='Return' href='{{g("return_url")}}' class="no-print"><i class='fa fa-chevron-circle-left'></i>
        &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>
    @else
        <p><a title='Main Module' href='{{CRUDBooster::mainpath()}}' class="no-print"><i class='fa fa-chevron-circle-left'></i>
        &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>
    @endif

    <div class='panel panel-default'>
        <div class='panel-heading'>
        <h3 class="box-title text-center"><b>Pre-Order Details</b></h3>
        </div>

        <div class='panel-body' id="order-details">

            <div class="col-md-6">
                <div class="table-responsive">
                    <table class="table table-bordered" id="order-details-1">
                        <tbody>
                            <tr>
                                <td style="width: 25%">
                                    <b>Customer Name:</b>
                                </td>
                                <td colspan="3">
                                    {{ $order_details->customer_name }}
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 25%">
                                    <b>Customer Email:</b>
                                </td>
                                <td colspan="3">
                                    {{ $order_details->email_address }}
                                </td>
                            </tr>

                            <tr>
                                <td style="width: 25%">
                                    <b>Customer Contact #:</b>
                                </td>
                                <td colspan="3">
                                    {{ $order_details->contact_number }}
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 25%">
                                    <b>Claim Status:</b>
                                </td>
                                <td width: 25%>
                                    {!! $order_details->claim_status !!}
                                </td>
                                <td style="width: 25%">
                                    <b>Date: </b>{{ (empty($order_details->claimed_date)) ? '' : $order_details->claimed_date }}
                                </td>
                                <td style="width: 25%">
                                    <b>Invoice #: </b>{{ (empty($order_details->claiming_invoice_number)) ? '' : $order_details->claiming_invoice_number }}
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
                                <td style="width: 50%">
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
                                    <b>Payment Status:</b>
                                </td>
                                <td>
                                    {!! $order_details->payment_status !!}
                                </td>
                                <td style="width: 50%">
                                    <b>Pre-order Invoice #: </b>{{ (empty($order_details->invoice_number)) ? '' : $order_details->invoice_number }}
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
                        <table class="table table-bordered" id="order-items">
                            <thead>
                                <tr style="background: #0047ab; color: white">
                                    <th width="10%" class="text-center">{{ trans('label.table.digits_code') }}</th>
                                    <th width="35%" class="text-center">{{ trans('label.table.item_description') }}</th>
                                    <th width="10%" class="text-center">{{ trans('label.table.qty') }}</th>
                                    <th width="10%" class="text-center">{{ trans('label.table.amount') }}</th>
                                    <th width="15%" class="text-center">Claimed Date</th>
                                    <th width="10%" class="text-center">Claiming Invoice #</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order_items as $item)
                                    <tr>
                                        <td class="text-center">{{$item['digits_code']}} </td>
                                        <td>{{$item['item_description']}}</td>
                                        <td class="text-center">{{$item['qty']}}</td>
                                        <td class="text-center">{{ number_format($item['amount'],2,".",",") }}</td>
                                        <td>{{$item['claimed_date']}}</td>
                                        <td>{{$item['claiming_invoice_number']}}</td>
                                    </tr>
                                @endforeach

                                <tr class="tableInfo">
                                    <td colspan="2" align="right"><strong>{{ trans('label.table.total_quantity') }}</strong></td>
                                    <td align="center" colspan="1">{{$order_details->total_qty}}</td>
                                    <td align="center" colspan="1">P {{ number_format($order_details->total_amount,2,".",",") }}</td>
                                    <td colspan="2"></td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            </div>

        <div class='panel-footer'>
            @if(g('return_url'))
            <a href="{{ g("return_url") }}" class="btn btn-default no-print">{{ trans('label.form.back') }}</a>
            @else
            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default no-print">{{ trans('label.form.back') }}</a>
            @endif
        </div>
    </div>

@endsection
@push('bottom')
<script type="text/javascript">
$(document).ready(function() {

    $(function(){
        $('body').addClass("sidebar-collapse");
        window.print();
    });
});
</script>
@endpush
