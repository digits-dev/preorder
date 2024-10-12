@extends('crudbooster::admin_template')
@section('content')

@push('head')
<style type="text/css">

table.table.table-bordered td {
  border: 1px solid black !important;
}

table.table.table-bordered tr {
  border: 1px solid black !important;
}

table.table.table-bordered th {
  border: 1px solid black !important;
}

.noselect {
  -webkit-touch-callout: none; /* iOS Safari */
    -webkit-user-select: none; /* Safari */
     -khtml-user-select: none; /* Konqueror HTML */
       -moz-user-select: none; /* Old versions of Firefox */
        -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; /* Non-prefixed version, currently supported by Chrome, Edge, Opera and Firefox */
}
 /* General print settings */
@media print {


    /* Hide any elements you don't want to print (e.g., buttons, headers) */
    button, .no-print {
        display: none;
    }

    /* Optional: If you want to print in a specific format (A4 for example) */
    @page {
        size: A4;
        margin: 20mm;
    }

    /* Make the table and layout elements look good on print */
    body {
        font-family: Arial, sans-serif;
        color: #000;
        font-size: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        white-space: pre-wrap;
    }

    th, td {
        border: 1px solid #000;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f0f0f0;
    }

    /* Optional: Prevent page breaks inside tables */
    table, tr, td {
        page-break-inside: avoid;
    }

    /* Style labels (e.g. Customer Name) and their content */
    .label {
        font-weight: bold;
    }

    /* Ensure content fits within the page and remove any extra padding/margins */
    .order-details {
        padding: 0;
        margin: 0;
    }

    /* Set the header to be prominent and aligned properly */
    h1, h2 {
        text-align: center;
        margin-top: 0;
    }

    /* Styling specific sections (like the Order Items table) */
    .order-items {
        margin-top: 10px;
    }

    .order-items th {
        background-color: #007bff;
        color: white;
    }

    img {
        width: 10%;
        height: auto;
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
            <div style="font-size:25px; font-weight:bold; display: inline;" class="col-md-8">Pre-Order Details</div>

            <div class="col-md-4 img-logo" style="display: inline;">
                <a href="#" style="vertical-align: top; ">
                <img src="{{ asset($order_details->concept_logo) }}" id="store-logo" class="img-responsive pull-right" alt="Preorder" width="170" height="70">
                </a>
            </div>

            <div style="clear:both;"></div>

        </div>

        <div class='panel-body' id="order-details">

            <div class="col-md-6 col-xs-6 col-sm-6 order-details">
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
                                <td style="width: 35%">
                                    <b>Customer Email:</b>
                                </td>
                                <td colspan="3">
                                    {{ $order_details->email_address }}
                                </td>
                            </tr>

                            <tr>
                                <td style="width: 35%">
                                    <b>Customer Contact #:</b>
                                </td>
                                <td colspan="3">
                                    {{ $order_details->contact_number }}
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 35%">
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

            <div class="col-md-6 col-xs-6 col-sm-6 order-details">
                <div class="table-responsive">
                    <table class="table table-bordered" id="order-details-2">
                        <tbody>
                            <tr>
                                <td style="width: 35%">
                                    <b>Order Ref#:</b>
                                </td>
                                <td>
                                    {{ $order_details->reference }}
                                </td>
                                <td style="width: 50%">
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 35%">
                                    <b>Store:</b>
                                </td>
                                <td colspan="2">
                                    {{ $order_details->store_name }}
                                </td>
                            </tr>

                            <tr>
                                <td style="width: 35%">
                                    <b>Mode of Payment:</b>
                                </td>
                                <td colspan="2">
                                    {{ $order_details->payment_method }}
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 35%">
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

            <div class="col-md-12 col-xs-12 col-sm-12 order-items">
                <div class="box-header text-center">
                    <h3 class="box-title"><b>Order Items</b></h3>
                </div>

                <div class="box-body no-padding">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="order-items">
                            <thead>
                                <tr style="background: #0047ab; color: white">
                                    <th style="width: 10%;" class="text-center">{{ trans('label.table.digits_code') }}</th>
                                    <th style="width: 35%;" class="text-center">{{ trans('label.table.item_description') }}</th>
                                    <th style="width: 10%;" class="text-center">{{ trans('label.table.qty') }}</th>
                                    <th style="width: 10%;" class="text-center">{{ trans('label.table.amount') }}</th>
                                    <th style="width: 15%;" class="text-center">Claimed Date</th>
                                    <th style="width: 10%;" class="text-center">Claiming Invoice #</th>
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
                                    <td colspan="2" style="text-align: right;"><strong>{{ trans('label.table.total_quantity') }}</strong></td>
                                    <td style="text-align: center;" colspan="1">{{$order_details->total_qty}}</td>
                                    <td style="text-align: center;" colspan="1">P {{ number_format($order_details->total_amount,2,".",",") }}</td>
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
