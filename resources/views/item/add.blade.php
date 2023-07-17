@extends('crudbooster::admin_template')

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

        label.error {
            color: red;
        }

    </style>
@endpush

@section('content')

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
        <h3 class="box-title text-center"><b>Item Creation</b></h3>
        </div>

        <form action="{{CRUDBooster::mainpath('add-save')}}" method="POST" id="item" autocomplete="off" role="form" enctype="multipart/form-data">
        <input type="hidden" name="_token" id="token" value="{{csrf_token()}}" >


        <div class='panel-body'>

            <div class="col-md-4">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td style="width: 25%">
                                    <b>Digits Code</b>
                                </td>
                                <td>
                                    <input class="form-control" type="number" style="width:100%" name="digits_code" id="digits_code" value="{{ (old('digits_code')) }}" required>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 25%">
                                    <b>UPC Code</b>
                                </td>
                                <td>
                                    <input class="form-control" type="text" style="width:100%" name="upc_code" id="upc_code" value="{{ (old('upc_code')) }}" required>
                                </td>
                            </tr>

                            <tr>
                                <td style="width: 25%">
                                    <b>Brand</b>
                                </td>
                                <td>
                                    <select name='brands_id' id="brands_id" class='form-control' required>
                                        <option value=''>Please select brand</option>
                                        @foreach ($brands as $brand)
                                            <option {{ old('brands_id') == $brand->id ? "selected" : "" }} value={{ $brand->id }}>{{ $brand->brand_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td style="width: 25%">
                                    <b>Item Description</b>
                                </td>
                                <td>
                                    <input class="form-control" type="text" style="width:100%" name="item_description" id="item_description" value="{{ (old('item_description')) }}" required>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-4">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td style="width: 25%">
                                    <b>Model</b>
                                </td>
                                <td>
                                    <select name='item_models_id' id="item_models_id" class='form-control' required>
                                        <option value=''>Please select model</option>
                                        @foreach ($models as $model)
                                            <option {{ old('item_models_id') == $model->id ? "selected" : "" }} value={{ $model->id }}>{{ $model->model_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 25%">
                                    <b>Size</b>
                                </td>
                                <td colspan="2">
                                    <select name='sizes_id' id="sizes_id" class='form-control' required>
                                        <option value=''>Please select size</option>
                                        @foreach ($sizes as $size)
                                            <option {{ old('sizes_id') == $size->id ? "selected" : "" }} value={{ $size->id }}>{{ $size->size }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td style="width: 25%">
                                    <b>Color</b>
                                </td>
                                <td colspan="2">
                                    <select name='colors_id' id="colors_id" class='form-control' required>
                                        <option value=''>Please select color</option>
                                        @foreach ($colors as $color)
                                            <option {{ old('colors_id') == $color->id ? "selected" : "" }} value={{ $color->id }}>{{ $color->color_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 25%">
                                    <b>Current SRP</b>
                                </td>
                                <td colspan="2">
                                    <input class="form-control" type="number" style="width:100%" name="current_srp" id="current_srp" value="{{ (old('current_srp')) }}" required>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-4">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td style="width: 25%">
                                    <b>Campaign</b>
                                </td>
                                <td colspan="2">
                                    <select name='campaigns_id' id="campaigns_id" class='form-control' required>
                                        <option value=''>Please select campaign</option>
                                        @foreach ($campaigns as $campaign)
                                            <option {{ old('campaigns_id') == $campaign->id ? "selected" : "" }} value={{ $campaign->id }}>{{ $campaign->campaigns_name }}</option>
                                        @endforeach
                                    </select>
                                </td>

                            </tr>

                            <tr>
                                <td style="width: 25%">
                                    <b>Item Type</b>
                                </td>
                                <td style="width: 25%; vertical-align: center;" >
                                    <div class="text-center">
                                        <label>
                                          <input class="is_freebies" type="radio" name="is_freebies" value="1">
                                          FREEBIE
                                        </label>
                                        <br>
                                        <label>
                                          <input class="is_freebies" type="radio" name="is_freebies" value="0" checked>
                                          MAIN ITEM
                                        </label>
                                      </div>
                                </td>
                                <td>
                                    <select name='freebies_categories_id' id="freebies_categories_id" class='form-control' style="display: none;">
                                        <option value=''>Please select freebies set</option>
                                        @foreach ($freebies_set as $set)
                                            <option {{ old('freebies_categories_id') == $set->id ? "selected" : "" }} value={{ $set->id }}>{{ $set->category_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td style="width: 25%">
                                    <b>Included Freebies</b><br>
                                    <span class="label label-info">For Units Only</span>
                                </td>
                                <td colspan="2">
                                    <select name='included_freebies[]' id="included_freebies" style="width: 100%" class='form-control' multiple='multiple'>
                                        <option value=''>Please select freebies set</option>
                                        @foreach ($freebies as $set)
                                            <option {{ old('included_freebies') == $set->id ? "selected" : "" }} value={{ $set->id }}>{{ $set->category_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td style="width: 25%">
                                    <b>WH Qty</b>
                                </td>
                                <td colspan="2">
                                    <input class="form-control" type="number" style="width:100%" name="dtc_wh" id="dtc_wh" value="{{ (old('dtc_wh')) }}" required>

                                </td>
                            </tr>
                        </tbody>
                    </table>
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

    $('#included_freebies').select2();

    $(".is_freebies").click(function(){
        if($(this).val() == 1){
            $("#freebies_categories_id").show();
            $("#freebies_categories_id").prop("required",true);
            $('#included_freebies').next(".select2-container").hide();
        }
        else{
            $("#freebies_categories_id").hide();
            $('#included_freebies').next(".select2-container").show();
            $("#freebies_categories_id").removeAttr("required");
        }
    });

    $('#btnSubmit').bind('keypress keydown keyup', function(e){
       if(e.keyCode == 13) { e.preventDefault(); }
    });

    $("#btnSubmit").click(function(event) {
        event.preventDefault();

        if($("#item").valid()){
            Swal.fire({
              title: 'Do you want to save the changes?',
              icon: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Save',
              cancelButtonText: 'Cancel',
            }).then((result) => {
              if (result.isConfirmed) {
                    $(this).prop("disabled", true);
                    $("#item").submit();
              }
            });

        }
    });

});
</script>
@endpush
