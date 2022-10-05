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

input[type="number"]::-webkit-outer-spin-button, 
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type="number"] {
    -moz-appearance: textfield;
}

label.error{
    color: red;
}

@media only screen and (max-width: 600px) { 
    body{
        padding: 1px;
        margin: 1px;
    }
    .col-sm-3, .col-sm-4, .col-sm-6, .col-sm-12{
        padding: 5px;
        margin: 0px;
    }
    .panel-heading, .panel-body{
        padding: 1px;
        margin: 2px;
    }

    h1{
        display: none;
    }

    h3{
        font-size: 18px;
        margin: 5px;
    }

    table.items tr.tableInfo{
        display: none;
    }

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
        <div class="alert alert-danger">
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
        <h3 class="box-title text-center"><b>Pre-Order</b></h3>
        </div>
        
        <form action="{{ route('preorder.order') }}" method="POST" id="preorder" autocomplete="off" role="form" enctype="multipart/form-data">
        <input type="hidden" name="_token" id="token" value="{{csrf_token()}}" >
        <input type="hidden" name="over_qty" id="over_qty" value="0" >
        <input type="hidden" name="with_freebies" id="with_freebies" value="0" >
        <input type="hidden" name="max_freebies" id="max_freebies" value="0" >
        <input type="hidden" name="max_order_qty" id="max_order_qty" value="0" >
        
        <div class='panel-body' id="order-details">

            <div class="col-md-6 col-sm-6">
                <div class="table-responsive">
                    <table class="table table-bordered" id="order-details-1">
                        <tbody>
                            <tr>
                                <td style="width: 30%">
                                    <b>Customer Email: <span style="color:red">*</span></b>
                                    <span class="label label-info" id="order_count"></span>
                                </td>
                                <td>
                                    <input class="form-control" type="text" placeholder="" name="email_address" id="email_address" value="{{ old('email_address') }}" required>
                                    <span class="error" id="invalid_email" style="color: red">* Email address is invalid!</span>
                                </td>
                            </tr>

                            <tr>
                                <td style="width: 30%">
                                    <b>Customer Name: <span style="color:red">*</span></b>
                                </td>
                                <td>
                                    <input class="form-control" type="text" placeholder="First Name Last Name" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required>
                                </td>
                            </tr>
                            
                            <tr>
                                <td style="width: 30%">
                                    <b>Customer Contact #: <span style="color:red">*</span></b>
                                </td>
                                <td>
                                    <input class="form-control" type="text" placeholder="09123456789" name="contact_number" id="contact_number" value="{{ old('contact_number') }}" required>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 30%">
                                    <b>Mode of Payment: <span style="color:red">*</span></b>
                                </td>
                                <td>
                                    <select name='payment_methods_id' id="payment_methods_id" class='form-control' required>
                                        <option value=''>Please select mode of payment</option>
                                        @foreach ($paymentMethods as $payment)
                                            <option {{ old('payment_methods_id') == $payment->id ? "selected" : "" }} value={{ $payment->id }}>{{ $payment->payment_method }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-6 col-sm-6">

                <div class="col-md-4 col-sm-4">
                    <div class="form-group">
                        <label class="control-label">Campaign: <span style="color:red">*</span></label>
                        <select id='campaigns_id' name="campaigns_id" class='form-control' required>
                            <option value=''>Please select a campaign</option>
                            @foreach ($campaigns as $campaign)
                                <option data-limit="{{ $campaign->max_order_count }}" value="{{ $campaign->id }}">{{ $campaign->campaigns_name }}</option>
                            @endforeach
                            
                        </select>
                    </div>
                </div>

                <div class="col-md-4 col-sm-4">
                    <div class="form-group">
                        <label class="control-label">Channel: <span style="color:red">*</span></label>
                        <select id='channels_id' name="channels_id" class='form-control' required>
                            @foreach ($channels as $channel)
                                <option value="{{ $channel->id }}">{{ $channel->channel_name }}</option>
                            @endforeach
                            
                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4">
                    <div class="form-group">
                        <label class="control-label">Pickup Location: <span style="color:red">*</span></label>
                        <select id='stores_id' name="stores_id" class='form-control' required>
                            @foreach ($stores as $store)
                                <option value="{{ $store->id }}">{{ $store->store_name }}</option>
                            @endforeach
                            
                        </select>
                    </div>
                </div>

                <div class="col-md-4 col-sm-4 col-xs-4">
                    <div class="form-group">
                        <label class="control-label">Model: </label>
                        <select id='model' class='form-control' disabled>
                            <option value=''>Please select a model</option>
                            
                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-4">
                    <div class="form-group">
                        <label class="control-label">Color: </label>
                        <select id='color' class='form-control' disabled>
                            <option value=''>Please select a color</option>
                            
                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-4">
                    <div class="form-group">
                        <label class="control-label">Size: </label>
                        <select id='size' class='form-control' disabled>
                            <option value=''>Please select a size</option>
                            
                        </select>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12">
                    <div class="form-group">
                        <label class="control-label">Search Item: <span style="color:red">*</span></label>
                        <input class="form-control" type="text" name="item_search" id="item_search" readonly>
                        <p class='help-block'>Please enter digits code or item description</p>
                    </div>
                </div>
            </div>

            <br>

            <div class="col-md-12 col-sm-12">
                <div class="box-header text-center">
                    <h3 class="box-title"><b>Order Items</b></h3>
                </div>

                <div class="box-body no-padding">
                    <div class="table-responsive" >
                        <table class="table table-bordered noselect items" id="order-items">
                            <thead>
                                <tr style="background: #0047ab; color: white">
                                    <th width="15%" class="text-center" data-title="{{ trans('label.table.digits_code') }}">{{ trans('label.table.digits_code') }}</th>
                                    <th width="35%" class="text-center" data-title="{{ trans('label.table.item_description') }}">{{ trans('label.table.item_description') }}</th>
                                    <th width="10%" class="text-center" data-title="{{ trans('label.table.qty') }}">{{ trans('label.table.qty') }}</th>
                                    <th width="20%" class="text-center" data-title="{{ trans('label.table.amount') }}">{{ trans('label.table.amount') }}</th>
                                    <th width="10%" class="text-center" data-title="{{ trans('label.table.reservable_qty') }}">{{ trans('label.table.reservable_qty') }}</th>
                                    <th width="10%" class="text-center" data-title="{{ trans('label.table.action') }}">{{ trans('label.table.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="dynamicRows"> </tr>
                                <tr class="tableInfo">
                                    <td align="center"> <strong>{{ trans('label.table.total_skus') }} : <span id="totalSKUS"></span></strong> </td>
                                    <td align="right"> <strong>{{ trans('label.table.total_quantity') }}</strong> </td>
                                    <td> <input type='number' name="total_quantity" class="form-control text-center" id="totalQuantity" value="0" readonly> </td>
                                    <td>
                                        <input type='text' class="form-control text-center" id="totalAmount" value="0" readonly> 
                                        <input type="hidden" name="total_amount" id="total_Amount" value="0">
                                    </td>
                                    <td colspan="2"> </td>
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

var token = $("#token").val();
var stack = [];
var orderLimit = false;
$(document).ready(function() {
    $('.error').hide();
    $(function(){
        $('body').addClass("sidebar-collapse");
        $('.tableInfo').hide();

        $('form').on('focus', 'input[type=number]', function (e) {
            $(this).on('wheel.disableScroll', function (e) {
                e.preventDefault()
            });
        });

        $('form').on('blur', 'input[type=number]', function (e) {
            $(this).off('wheel.disableScroll')
        });
    });

    $('#order-items').on('click', '.delete_item', function () {
        var v = $(this).attr("id");
        stack = jQuery.grep(stack, function (value) {
            return value != v;
        });

        $(this).closest("tr").remove();
        $('#model').removeAttr('disabled');
        $("#freebies_"+v).remove();
        getTotalComputations();
    });

    $('#order-items').on('keyup', '.order_qty', function(){
        var id = $(this).attr("data-id");
        var code = $(this).attr("data-code");
        var qty = parseInt($(this).val());
        var rate = parseFloat($(this).attr("data-rate"));
        var price = calculatePrice(qty, rate);
        var rsv_qty = $("#ajax_"+code).val();
        //check if input qty > reservable qty
        
        if($(this).val().length == 0){
            setTimeout(() => {
                Swal.fire('Warning!','Please enter valid qty!','warning');
            }, 1000);
        }
        if($(this).val() == 0){
            setTimeout(() => {
                Swal.fire('Warning!','Please enter at least 1 qty!','warning');
            }, 1000);
        }
        if(qty > rsv_qty){
            $("#over_qty").val(1);
            Swal.fire('Warning!','Over quantity detected!','warning');
        }
        else{
            $("#over_qty").val(0);
        }
        $("#amount_" + id).val(price);
        getTotalComputations();
    });
    
    $('#email_address').keyup(function(){
        $('.error').hide();
    });

    $('#email_address').focusout(function(){
        
        let email = $(this).val();
        $.ajax({
        url: "{{ route('preorder.getCustomer') }}",
            dataType: "json",
            type: "POST",
            data: {
                _token: token,
                email_address: email
            },
            success: function(data){
                if(data != null){
                    $('#customer_name').val(data.customer_name);
                    $('#contact_number').val(data.contact_number);
                    $('#order_count').text('0 orders');
                    $('#payment_methods_id option[value="'+data.payment_methods_id+'"]').attr('selected', 'selected').trigger('change'); 
                }
            }
        });
    });

    $('#campaigns_id').change(function(){

        let selected_campaign = $(this).val();
        let limit = $("#campaigns_id option:selected").attr('data-limit');
        $('#max_order_qty').val(limit);
        $('#model').removeAttr('disabled');
        $('#model').empty().append('<option selected="selected" value="">Please select a model</option>');
        
        $.ajax({
            url: "{{ route('item.getItemModels') }}",
            dataType: "json",
            type: "POST",
            data: {
                _token: token,
                campaign_id: selected_campaign
            },
            success: function(data){
                $.each(data, function (i, item) {
                    
                    $('#model').append($('<option>', { 
                        value: item.id,
                        text : item.model_name 
                    }));
                });
            }
        });

        $.ajax({
            url: "{{ route('preorder.getCustomerOrders') }}",
            dataType: "json",
            type: "POST",
            data: {
                _token: token,
                email_address: $('#email_address').val(),
                campaign: selected_campaign
            },
            success: function(orderCount){
                if(orderCount != null){
                    $('#order_count').text(orderCount+' orders');
                    if(parseInt(limit) <= parseInt(orderCount) && orderCount != 0){
                        Swal.fire('Warning!','Order limit reached for this customer!','warning');
                        orderLimit=true;
                    }
                }
                else{
                    $('#order_count').text('0 orders');
                    orderLimit=false;
                }
            }
        });
    });

    $('#model').change(function(){
        let selected_model = $(this).val();
        $('#color').removeAttr('disabled');
        $('#color').empty().append('<option selected="selected" value="">Please select a color</option>');
        $.ajax({
            url: "{{ route('item.getItemColors') }}",
            dataType: "json",
            type: "POST",
            data: {
                _token: token,
                model_id: selected_model
            },
            success: function(data){
                $.each(data, function (i, item) {
                    
                    $('#color').append($('<option>', { 
                        value: item.id,
                        text : item.color_name 
                    }));
                });
            }
        });

    });

    $('#color').change(function(){
        let selected_color = $(this).val();
        let selected_model = $('#model').val();
        $('#size').removeAttr('disabled');

        $.ajax({
            url: "{{ route('item.getItemSizes') }}",
            dataType: "json",
            type: "POST",
            data: {
                _token: token,
                model_id: selected_model,
                color_id: selected_color
            },
            success: function(data){
                $.each(data, function (i, item) {
                    
                    $('#size').append($('<option>', { 
                        value: item.id,
                        text : item.size 
                    }));
                });
            }
        });
    });

    $('#size').change(function(){
        let search = $('#model option:selected').text()+' '+$('#size option:selected').text()+' '+$('#color option:selected').text();
        $("#item_search").val(search);
        $("#item_search").trigger("autocomplete");
        $("#item_search").autocomplete('search', search);
    });

    $("#item_search").autocomplete({
        source: function (request, response) {
        $.ajax({
            url: "{{ route('preorder.item-search') }}",
            cache: true,
            dataType: "json",
            type: "POST",
            data: {
                "_token": token,
                "search": request.term,
                "model": $('#model option:selected').val(),
                "color": $('#color option:selected').val(),
                "size": $('#size option:selected').val(),
                "campaign": $('#campaigns_id option:selected').val()
            },
            success: function (data) {

                if (data.status_no == 1) {
                    
                    var data = data.items;
                    $('#ui-id-2').css('display', 'none');
                    response($.map(data, function (item) {
                        return {
                            id: item.id,
                            included_freebie: item.included_freebie,
                            item_code: item.digits_code,
                            value: item.item_description,
                            current_price: item.current_srp,
                            reservable_qty: item.wh_reserved_qty,
                        }
                    }));
                } else {
                    $('.ui-menu-item').remove();
                    $('.addedLi').remove();
                    $("#ui-id-2").append("<li class='addedLi'>"+data.message+"</li>");
                    var searchVal = $("#search").val();
                    if (searchVal.length > 0) {
                        $("#ui-id-2").css('display', 'block');
                    } else {
                        $("#ui-id-2").css('display', 'none');
                    }
                }
            }
        })
    },
    select: function (event, ui) {
        var e = ui.item;
        if (e.id) {
            if(e.reservable_qty == 0){
                Swal.fire('Warning!','No available qty!','warning');
                $(this).val('');
                resetDropDown();
                return false;
            }
            if (!in_array(e.id, stack)) {
                stack.push(e.id);
                var max_f = e.included_freebie;
                $("#model").attr("disabled","disabled");

                var new_row = '<tr class="nr" id="rowid' + e.id + '">' +
                        '<td><input class="form-control text-center" type="text" tabindex="-1" name="digits_code[]" value="' + e.item_code + '" readonly></td>' +
                        '<td><input class="form-control" type="text" tabindex="-1" id="item_description' + e.item_code + '" value="' + e.value + '" readonly></td>' +
                        '<td><input class="form-control text-center order_qty item_quantity" data-id="' + e.id + '" data-rate="' + e.current_price + '"  data-code="' + e.item_code + '" type="number" min="1" max="100" oninput="validity.valid||(value=0);" id="qty_' + e.item_code + '" name="qty[]" value="1" readonly></td>' +
                        '<td><input class="form-control text-center amount" type="text" id="amount_'+e.id+'" value="'+ e.current_price+'" name="amount[]" readonly></td>' +
                        '<td><input class="form-control text-center item-reservable" type="text" tabindex="-1" name="reservable_qty[]" data-code="' + e.item_code + '" id="ajax_'+e.item_code+'" value="'+e.reservable_qty+'" readonly></td>'+
                        '<input type="hidden" name="item_id[]" value="' + e.id + '">' +
                        '<td class="text-center"><button id="'+e.id+'" class="btn btn-xs btn-danger delete_item"><i class="glyphicon glyphicon-trash"></i></button></td>' +
                        '</tr>';
                if(e.included_freebie != null){
                    $("#max_freebies").val(max_f.split(",").length);
                    $("#with_freebies").val(1);
                    new_row +='<tr id="freebies_'+e.id+'">'+
                            '<td colspan="6">'+
                            '<table class="table table-bordered noselect items" id="order-freebies" style="display:none;">'+
                            
                            '<tbody>'+
                                '<tr class="dynamicFreebiesRows' + e.id + '"> </tr>'+
                                '<tr class="tableFreebiesInfo">'+
                                    '<td colspan="1"> </td>'+
                                    '<td align="center"> <strong>{{ trans("label.table.total_free_skus") }} : <span id="totalFreeSKUS'+e.item_code+'">0</span></strong> </td>'+
                                    '<td align="right"> <strong>{{ trans("label.table.total_free_quantity") }}</strong> </td>'+
                                    '<td align="center"> <span id="totalFreeQuantity'+e.item_code+'">0</span> </td>'+
                                    '<td align="center"> <span id="totalFreeAmount'+e.item_code+'">0</span> </td>'+
                                    '<td colspan="2"> </td>'+
                                '</tr>'+
                            '</tbody>'+
                            '</table>'+
                        '</td></tr>';
                }
            
                $(new_row).insertAfter($('table#order-items tr.dynamicRows:last'));
                getTotalComputations();
                $('.tableInfo').show();
                //update available qty
                $(function(){
                    setInterval(function() { 
                        updateReservableQty();
                    },500);
                });
                if(e.included_freebie != null){
                    //get freebies items
                    $.ajax({
                        url: "{{ route('preorder.freebies-search') }}",
                        cache: true,
                        dataType: "json",
                        type: "POST",
                        data: {
                            "_token": token,
                            "search": e.included_freebie,
                        },
                        success: function (data_freebies) {
                            $("#order-freebies").show();
                            $.each(data_freebies.freebies, function (i, item) {
                                if(item.wh_reserved_qty != 0){
                                    var freebies_row = '<tr class="nr-freebies fcategory'+ item.category +'" id="rowid' + item.id + '">' +
                                        '<td width="5%" style="vertical-align: middle;text-align: center;"><input class="text-center freebies-checkbox check-box-' + e.item_code + '" data-id="' + e.item_code + '" data-code="' + item.digits_code + '" data-category="'+ item.category +'" type="checkbox" tabindex="-1" id="checkbox-' + item.digits_code + '" name="freebies[]"></td>' +
                                        '<td width="10%"><input class="form-control text-center" type="text" tabindex="-1" name="f_digits_code[]" value="' + item.digits_code + '" readonly></td>' +
                                        '<td width="35%"><input class="form-control" type="text" tabindex="-1" id="f_item_description' + item.digits_code + '" value="' + item.item_description + '" readonly></td>' +
                                        '<td width="10%"><input class="form-control text-center order_freebies_qty freebies_quantity' + e.item_code + '" data-id="' + item.id + '" data-rate="' + item.current_srp + '"  data-code="' + item.digits_code + '" type="number" min="1" max="100" oninput="validity.valid||(value=0);" id="f_qty_' + item.digits_code + '" name="f_qty[]" value="1" readonly></td>' +
                                        '<td width="20%"><input class="form-control text-center freebies_amount' + e.item_code + '" type="text" data-code="' + item.digits_code + '" id="f_amount_' + item.digits_code + '" name="f_amount[]" value="'+ item.current_srp+'" readonly></td>' +
                                        '<td width="10%"><input class="form-control text-center freebies-reservable" type="text" tabindex="-1" name="f_reservable_qty[]" data-code="' + item.digits_code + '" id="ajax_'+item.digits_code+'" value="'+item.wh_reserved_qty+'" readonly></td>'+
                                        '<input type="hidden" name="f_item_id[]" value="' + item.id + '">' +
                                        '<td width="10%" class="text-center"><button id="'+item.id+'" data-id="' + e.item_code + '" class="btn btn-xs btn-danger delete_freebies"><i class="glyphicon glyphicon-trash"></i></button></td></tr>';
                                    
                                    $(freebies_row).insertAfter($('table#order-freebies tr.dynamicFreebiesRows' + e.id + ':last'));
                                    $('#checkbox-'+ item.digits_code).trigger('click');
                                    $(".fcategory"+item.category).css("background-color", item.background_color);
                                }
                            });

                            //update available qty
                            $(function(){
                                setInterval(function() { 
                                    updateFreebiesReservableQty();
                                },500);

                                
                            });
                        }
                    });
                }
            } 
            else {
                $('#qty_' + e.item_code).val(function (i, oldval) {
                    return ++oldval;
                });

                var q = $('#qty_' + e.item_code).val();
                var r = $("#qty_" + e.item_code).attr("data-rate");

                $('#amount_' + e.id).val(function (i, amount) {
                if (q != 0) {
                    var itemPrice = (q * r);
                    return itemPrice;
                } else {
                    return 0;
                }
                });
                
                getTotalComputations();
            }

            $(this).val('');
            resetDropDown();
            
            return false;
        }
    },
        minLength: 1,
        autoFocus: true
    });

    

    $('#btnSubmit').bind('keypress keydown keyup', function(e){
       if(e.keyCode == 13) { e.preventDefault(); }
    });

    $("#btnSubmit").click(function(event) {
        event.preventDefault();
        let rowCount = parseInt($('#order-items tr.nr').length);
        let rowFreebiesCount = parseInt($('#order-freebies tr.nr-freebies').length);
        
        if(validateEmail($('#email_address').val())==false){
            $('#invalid_email').show();
            return false;
        }

        if(orderLimit){
            Swal.fire('Warning!','Order limit reached for this customer!','warning');
        }

        // if($("#over_qty").val() == 1){
        //     Swal.fire('Warning!','Over quantity detected!','warning'); 
        // }

        if(checkQty()){
            Swal.fire('Warning!','Please check qty!','warning');
            return false;
        }

        if(rowCount == 0){
            Swal.fire('Warning!','Please add at least 1 item!','warning');
            return false;
        }

        if(rowFreebiesCount != $("#max_freebies").val()){
            Swal.fire('Warning!','Please select '+$("#max_freebies").val()+' freebies only!','warning');
            return false;
        }

        if(checkFreebies()){
            Swal.fire('Warning!','Please check freebies, select 1 each type!','warning');
            return false;
        }

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

$(document).on('click', '.freebies-checkbox', function(){
    let cItemFreebies = $(this).attr('data-id');
    $("#totalFreeSKUS"+cItemFreebies).text(calculateFreeBiesTotalSKU(cItemFreebies));
    $("#totalFreeQuantity"+cItemFreebies).text(calculateFreeBiesTotalQuantity(cItemFreebies));
    $("#totalFreeAmount"+cItemFreebies).text(calculateFreeBiesTotalAmount(cItemFreebies));
});

$(document).on('click', '.delete_freebies', function () {
    var v = $(this).attr("id");
    
    $(this).closest("tr").remove();
    let cItemFreebies = $(this).attr('data-id');
    $("#totalFreeSKUS"+cItemFreebies).text(calculateFreeBiesTotalSKU(cItemFreebies));
    $("#totalFreeQuantity"+cItemFreebies).text(calculateFreeBiesTotalQuantity(cItemFreebies));
    $("#totalFreeAmount"+cItemFreebies).text(calculateFreeBiesTotalAmount(cItemFreebies));
});

function resetDropDown() {
    $("#model").val('');
    $("#color").val('');
    $("#size").val('');
    $("#color").attr('disabled','disabled');
    $("#size").attr('disabled','disabled');
    $('#color').empty().append('<option selected="selected" value="">Please select a color</option>');
    $('#size').empty().append('<option selected="selected" value="">Please select a size</option>');
}

function validateEmail(email) {
    var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if(!regex.test(email)) {
        return false;
    }else{
        return true;
    }
}

function in_array(search, array) {
  for (i = 0; i < array.length; i++) {
    if (array[i] == search) {
      return true;
    }
  }
  return false;
}

function calculatePrice(qty, rate) {
  if (qty != 0) {
    var price = (qty * rate);
    return price;
  } else {
    return '0';
  }
}

function calculateTotalAmount() {
  let subTotal = 0;
  $('.amount').each(function () {
    subTotal += parseFloat($(this).val());
  });
  $("#total_Amount").val(subTotal);
  return currencyFormat(subTotal);
}

function calculateTotalQuantity() {
  let totalQuantity = 0;
  $('.item_quantity').each(function () {
    totalQuantity += parseInt($(this).val());
  });
  return totalQuantity;
}

function checkFreebies() {
    let categoryError = false;
    var f_category = [];
    $('.freebies-checkbox').each(function () {
        if($(this).prop("checked") == true) {
            if(f_category.indexOf($(this).attr('data-category')) != -1){
                categoryError=true;
            }
            else{
                f_category.push($(this).attr('data-category'));
            }
        }
    });
    return categoryError;
}

function calculateFreeBiesTotalSKU(item_code) {
    let freeBiesTotalSKU = 0;
  
    $('.check-box-'+item_code).each(function () {
        if($(this).prop("checked") == true) {
            freeBiesTotalSKU += 1;
        }
    });
  
    return freeBiesTotalSKU;
}

function calculateFreeBiesTotalQuantity(item_code) {
    let freeBiesTotalQty = 0;
  
    $('.freebies_quantity'+item_code).each(function () {
        let selectedItem = $(this).attr('data-code');
        let selectedCheckbox = $("#checkbox-"+selectedItem).attr('data-code');
        if($("#checkbox-"+selectedItem).prop("checked") == true && selectedItem == selectedCheckbox) {
            freeBiesTotalQty += parseInt($("#f_qty_"+selectedItem).val());
        }
    });
  
    return freeBiesTotalQty;
}

function calculateFreeBiesTotalAmount(item_code) {
    let freeBiesTotalAmount = 0;
  
    $('.freebies_amount'+item_code).each(function () {
        let selectedItem = $(this).attr('data-code');
        let selectedCheckbox = $("#checkbox-"+selectedItem).attr('data-code');
        if($("#checkbox-"+selectedItem).prop("checked") == true && selectedItem == selectedCheckbox) {
            freeBiesTotalAmount += parseFloat($("#f_amount_"+selectedItem).val());
        }
    });
  
    return freeBiesTotalAmount.toFixed(2);
}

function checkQty() {
  let error_qty = false;
  $('.item_quantity').each(function () {
    let itemCode = $(this).attr('data-code');
    if(parseInt($(this).val()) == 0 || $(this).val() == ''){
        error_qty=true;
    }
    if(parseInt($(this).val()) > $('#ajax_'+itemCode).val()){
        error_qty=true;
    }
  });
  return error_qty;
}

function getTotalComputations() {
    let skuCount = parseInt($('#order-items tr.nr').length);
    $("#totalSKUS").text(skuCount);
    $("#totalAmount").val(calculateTotalAmount());
    $("#totalQuantity").val(calculateTotalQuantity());
}

function updateReservableQty(){
$('.item-reservable').each(function () {
     
    let item = $(this).attr("data-code");
    let currentItem = $(this).attr("id");
    $.ajax({
    url: "{{ route('preorder.item-reservable') }}",
        dataType: "json",
        type: "POST",
        data: {
            _token: token,
            item_code: item
        },
        success: function(data){
            $("#"+currentItem).val(data);
        }
    });
});
}

function updateFreebiesReservableQty(){
    $('.freebies-reservable').each(function () {
        var item = $(this).attr("data-code");
        var currentItem = $(this).attr("id");
        $.ajax({
        url: "{{ route('preorder.item-reservable') }}",
            dataType: "json",
            type: "POST",
            data: {
                _token: token,
                item_code: item
            },
            success: function(data){
                $("#"+currentItem).val(data);
            }
        });
    });
}

function currencyFormat(num) {
  return 'P ' + num.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
}

</script>
@endpush
