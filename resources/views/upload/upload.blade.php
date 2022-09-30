
@extends('crudbooster::admin_template')
@section('content')

  <div class='panel panel-default'>
      <div class='panel-body'>

        @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {!! implode('', $errors->all('<div>:message</div>')) !!}

        </div>

        @endif

          <form method='post' id='form' enctype='multipart/form-data' action='{{$uploadRoute}}'>
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <div class="box-body">
                  <div class='callout callout-success'>
                      <h4>Welcome to Data Importer Tool</h4>
                      Before uploading a file, please read below instructions : <br />
                      * File format should be : CSV file format<br />
                      * Don't include items not found in item master<br />

                  </div>

                  <table class="table table-striped">
                    <thead>
                        <tr>
                          <th scope="col">Import Template File</th>
                          <th scope="col">File to Import</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><a href='{{ $uploadTemplate }}' class='btn btn-primary' role='button'>Download Template</a></td>
                          <td><input type='file' name='import_file' id='file_name' class='form-control' required accept='.csv' />
                            <div class='help-block'>File type supported only : CSV</div></td>
                        </tr>

                  </table>


                </div>
                  </div>
                  <div class='panel-footer'>
                      <a href='{{ CRUDBooster::mainpath() }}' class='btn btn-default'>Cancel</a>
                      <input type='submit' class='btn btn-primary pull-right' value='Upload' />
                  </div>
          </form>
      </div>

@endsection
