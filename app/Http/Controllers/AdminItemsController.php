<?php namespace App\Http\Controllers;

	use Session;
	use Illuminate\Http\Request;
	use DB;
	use CRUDBooster;
	use App\Models\Item;
	use App\Imports\ItemInventoryImport;
	use Maatwebsite\Excel\HeadingRowImport;
    use Maatwebsite\Excel\Imports\HeadingRowFormatter;
    use App\Exports\ExcelTemplateExport;
	use App\Exports\ItemExport;
	use App\Imports\ItemImport;
	use App\Models\Brand;
	use App\Models\Campaign;
	use App\Models\Color;
	use App\Models\FreebiesCategory;
	use App\Models\ItemCategory;
	use App\Models\ItemModel;
	use App\Models\Size;
	use Maatwebsite\Excel\Facades\Excel;

	class AdminItemsController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "digits_code";
			$this->limit = "20";
			$this->orderby = "digits_code,asc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = true;
			$this->button_action_style = "button_icon";
			$this->button_add = true;
			$this->button_edit = true;
			$this->button_delete = true;
			$this->button_detail = true;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = false;
			$this->table = "items";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Digits Code","name"=>"digits_code"];
			$this->col[] = ["label"=>"UPC Code","name"=>"upc_code"];
			$this->col[] = ["label"=>"Item Description","name"=>"item_description"];
			$this->col[] = ["label"=>"Brand","name"=>"brands_id","join"=>"brands,brand_name"];
			$this->col[] = ["label"=>"Category","name"=>"item_categories_id","join"=>"item_categories,category_name"];
			$this->col[] = ["label"=>"Model","name"=>"item_models_id","join"=>"item_models,model_name"];
			$this->col[] = ["label"=>"Size","name"=>"sizes_id","join"=>"sizes,size"];
			$this->col[] = ["label"=>"Actual Color","name"=>"colors_id","join"=>"colors,color_name"];
			$this->col[] = ["label"=>"Current SRP","name"=>"current_srp"];
			$this->col[] = ["label"=>"Campaign","name"=>"campaigns_id","join"=>"campaigns,campaigns_name"];
			$this->col[] = ["label"=>"Included Freebie (Units Only)","name"=>"included_freebies"];
			$this->col[] = ["label"=>"Is Freebie","name"=>"is_freebies"];
			$this->col[] = ['label'=>"Freebie Category (Freebies Only)","name"=>"freebies_categories_id","join"=>"freebies_categories,category_name"];
			$this->col[] = ["label"=>"Available Qty","name"=>"dtc_reserved_qty"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Digits Code','name'=>'digits_code','type'=>'number','validation'=>'required|min:1|max:99999999','width'=>'col-sm-6'];
			$this->form[] = ['label'=>'UPC Code','name'=>'upc_code','type'=>'text','validation'=>'required|min:1|max:50','width'=>'col-sm-6'];
			$this->form[] = ['label'=>'Item Description','name'=>'item_description','type'=>'text','validation'=>'required|min:1|max:100','width'=>'col-sm-6'];
			$this->form[] = ['label'=>'Brand','name'=>'brands_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-6','datatable'=>'brands,brand_name','datatable_where'=>"status='ACTIVE'"];
			$this->form[] = ['label'=>'Category','name'=>'item_categories_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-6','datatable'=>'item_categories_id,category_name','datatable_where'=>"status='ACTIVE'"];
			$this->form[] = ['label'=>'Model','name'=>'item_models_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-6','datatable'=>'item_models,model_name','datatable_where'=>"status='ACTIVE'"];
			$this->form[] = ['label'=>'Size','name'=>'sizes_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-6','datatable'=>'sizes,size','datatable_where'=>"status='ACTIVE'"];
			$this->form[] = ['label'=>'Actual Color','name'=>'colors_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-6','datatable'=>'colors,color_name','datatable_where'=>"status='ACTIVE'"];
			$this->form[] = ['label'=>'Current SRP','name'=>'current_srp','type'=>'number','validation'=>'required|min:0','width'=>'col-sm-6'];
			$this->form[] = ['label'=>'Campaigns','name'=>'campaigns_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-6','datatable'=>'campaigns,campaigns_name','datatable_where'=>"status='ACTIVE'"];
			$this->form[] = ['label'=>'Is Freebie','name'=>'is_freebies','type'=>'radio','validation'=>'required|min:0','width'=>'col-sm-6','dataenum'=>'0|No;1|Yes'];
			$this->form[] = ['label'=>'Included Freebie (Units Only)','name'=>'included_freebies','type'=>'select2-multi','multiple'=>true,'width'=>'col-sm-6','datatable'=>'freebies_categories,category_name','datatable_where'=>"status='ACTIVE'"];
			$this->form[] = ['label'=>'Freebie Category (Freebies Only)','name'=>'freebies_categories_id','type'=>'select','validation'=>'required|min:0','width'=>'col-sm-6','datatable'=>'freebies_categories,category_name','datatable_where'=>"status='ACTIVE'"];
			$this->form[] = ['label'=>'WH Qty','name'=>'dtc_wh','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-6'];
			// $this->form[] = ['label'=>'Reserved Qty','name'=>'dtc_reserved_qty','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-6'];
			# END FORM DO NOT REMOVE THIS LINE

			/* 
	        | ---------------------------------------------------------------------- 
	        | Sub Module
	        | ----------------------------------------------------------------------     
			| @label          = Label of action 
			| @path           = Path of sub module
			| @foreign_key 	  = foreign key of sub table/module
			| @button_color   = Bootstrap Class (primary,success,warning,danger)
			| @button_icon    = Font Awesome Class  
			| @parent_columns = Sparate with comma, e.g : name,created_at
	        | 
	        */
	        $this->sub_module = array();


	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add More Action Button / Menu
	        | ----------------------------------------------------------------------     
	        | @label       = Label of action 
	        | @url         = Target URL, you can use field alias. e.g : [id], [name], [title], etc
	        | @icon        = Font awesome class icon. e.g : fa fa-bars
	        | @color 	   = Default is primary. (primary, warning, succecss, info)     
	        | @showIf 	   = If condition when action show. Use field alias. e.g : [id] == 1
	        | 
	        */
	        $this->addaction = array();


	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add More Button Selected
	        | ----------------------------------------------------------------------     
	        | @label       = Label of action 
	        | @icon 	   = Icon from fontawesome
	        | @name 	   = Name of button 
	        | Then about the action, you should code at actionButtonSelected method 
	        | 
	        */
	        $this->button_selected = array();

	                
	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add alert message to this module at overheader
	        | ----------------------------------------------------------------------     
	        | @message = Text of message 
	        | @type    = warning,success,danger,info        
	        | 
	        */
	        $this->alert = array();
	                

	        
	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add more button to header button 
	        | ----------------------------------------------------------------------     
	        | @label = Name of button 
	        | @url   = URL Target
	        | @icon  = Icon from Awesome.
	        | 
	        */
	        $this->index_button = array();
			if(CRUDBooster::getCurrentMethod() == 'getIndex') {
                if(CRUDBooster::isSuperadmin()){
                    $this->index_button[] = [
                        "title"=>"Upload Inventory",
                        "label"=>"Upload Inventory",
                        "icon"=>"fa fa-upload",
                        "color"=>"info",
                        "url"=>route('item-inventory.view')];
                }
				if(CRUDBooster::isSuperadmin()){
                    $this->index_button[] = [
                        "title"=>"Upload Items",
                        "label"=>"Upload Items",
                        "icon"=>"fa fa-upload",
                        "color"=>"info",
                        "url"=>route('item.view')];
                }
				$this->index_button[] = ['label'=>'Export Items','url'=>"javascript:showItemExport()",'icon'=>'fa fa-download'];
				if(CRUDBooster::isSuperadmin()){
                    $this->index_button[] = [
                        "title"=>"Clear Items",
                        "label"=>"Clear Items",
                        "icon"=>"fa fa-times",
                        "color"=>"danger",
                        "url"=>"javascript:showDeleteItemsConfirmation()"];
                }
            }


	        /* 
	        | ---------------------------------------------------------------------- 
	        | Customize Table Row Color
	        | ----------------------------------------------------------------------     
	        | @condition = If condition. You may use field alias. E.g : [id] == 1
	        | @color = Default is none. You can use bootstrap success,info,warning,danger,primary.        
	        | 
	        */
	        $this->table_row_color = array();     	          

	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | You may use this bellow array to add statistic at dashboard 
	        | ---------------------------------------------------------------------- 
	        | @label, @count, @icon, @color 
	        |
	        */
	        $this->index_statistic = array();



	        /*
	        | ---------------------------------------------------------------------- 
	        | Add javascript at body 
	        | ---------------------------------------------------------------------- 
	        | javascript code in the variable 
	        | $this->script_js = "function() { ... }";
	        |
	        */
			$this->script_js = "
				function showItemExport() {
					$('#modal-item-export').modal('show');
				}

				$(function(){
					$('body').addClass('sidebar-collapse');
				});

				function showDeleteItemsConfirmation() {
					swal({   
						title: 'All items deletion?',
						text: 'Are you sure to delete all items?',
						type: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#DD6B55',
						confirmButtonText: 'Yes',
						cancelButtonText: 'Cancel',
						closeOnConfirm: true, 
					}, 
						function(){  
							location.href='".route("item.delete-all")."'
					});
				}";

            /*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code before index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it before index table
	        | $this->pre_index_html = "<p>test</p>";
	        |
	        */
	        $this->pre_index_html = null;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code after index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it after index table
	        | $this->post_index_html = "<p>test</p>";
	        |
	        */
	        // $this->post_index_html = null;
	        $this->post_index_html = "
			<div class='modal fade' tabindex='-1' role='dialog' id='modal-item-export'>
				<div class='modal-dialog'>
					<div class='modal-content'>
						<div class='modal-header'>
							<button class='close' aria-label='Close' type='button' data-dismiss='modal'>
								<span aria-hidden='true'>×</span></button>
							<h4 class='modal-title'><i class='fa fa-download'></i> Export Items</h4>
						</div>

						<form method='post' target='_blank' action=".CRUDBooster::mainpath("export").">
                        <input type='hidden' name='_token' value=".csrf_token().">
                        ".CRUDBooster::getUrlParameters()."
                        <div class='modal-body'>
                            <div class='form-group'>
                                <label>File Name</label>
                                <input type='text' name='filename' class='form-control' required value='Export ".CRUDBooster::getCurrentModule()->name ." - ".date('Y-m-d H:i:s')."'/>
                            </div>
						</div>
						<div class='modal-footer' align='right'>
                            <button class='btn btn-default' type='button' data-dismiss='modal'>Close</button>
                            <button class='btn btn-primary btn-submit' type='submit'>Submit</button>
                        </div>
                    </form>
					</div>
				</div>
			</div>
			";
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include Javascript File 
	        | ---------------------------------------------------------------------- 
	        | URL of your javascript each array 
	        | $this->load_js[] = asset("myfile.js");
	        |
	        */
	        $this->load_js = array();
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Add css style at body 
	        | ---------------------------------------------------------------------- 
	        | css code in the variable 
	        | $this->style_css = ".style{....}";
	        |
	        */
	        $this->style_css = NULL;

	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include css File 
	        | ---------------------------------------------------------------------- 
	        | URL of your css each array 
	        | $this->load_css[] = asset("myfile.css");
	        |
	        */
	        $this->load_css = array();
	        
	        
	    }


	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for button selected
	    | ---------------------------------------------------------------------- 
	    | @id_selected = the id selected
	    | @button_name = the name of button
	    |
	    */
	    public function actionButtonSelected($id_selected,$button_name) {
	        //Your code here
	            
	    }


	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate query of index result 
	    | ---------------------------------------------------------------------- 
	    | @query = current sql query 
	    |
	    */
	    public function hook_query_index(&$query) {
	        //Your code here
	            
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
	    	//Your code here
			if($column_index == 11){
				$freebie_sets = explode(",",$column_value);
				$sets = FreebiesCategory::whereIn('id',$freebie_sets)->get();
				$column_value='';
				foreach ($sets as $key => $value) {
					$column_value.='<span class="label label-info">'.$value->category_name.'</span><br>';
				}

			}
			if($column_index == 12){
				if($column_value == 1){
					$column_value='<span class="label label-success">YES</span>';
				}
				else{
					$column_value='<span class="label label-danger">NO</span>';
				}
			}
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before add data is execute
	    | ---------------------------------------------------------------------- 
	    | @arr
	    |
	    */
	    public function hook_before_add(&$postdata) {        
	        //Your code here
			$postdata['created_by']=CRUDBooster::myId();
			$postdata['dtc_reserved_qty']=$postdata['dtc_wh'];
			if(is_array($postdata['included_freebies'])){
				$postdata['included_freebies'] = implode(",", $postdata['included_freebies']);
			}
	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after add public static function called 
	    | ---------------------------------------------------------------------- 
	    | @id = last insert id
	    | 
	    */
	    public function hook_after_add($id) {        
	        //Your code here

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before update data is execute
	    | ---------------------------------------------------------------------- 
	    | @postdata = input post data 
	    | @id       = current id 
	    | 
	    */
	    public function hook_before_edit(&$postdata,$id) {        
	        //Your code here
			$postdata['updated_by']=CRUDBooster::myId();
			$postdata['dtc_reserved_qty']=$postdata['dtc_wh'];
			if(is_array($postdata['included_freebies'])){
				$postdata['included_freebies'] = implode(",", $postdata['included_freebies']);
			}
	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after edit public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_after_edit($id) {
	        //Your code here 

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command before delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_before_delete($id) {
	        //Your code here

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_after_delete($id) {
	        //Your code here

	    }

		public function getAdd()
		{
			if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE || $this->button_add==FALSE) {
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

            $data = [];
            $data['page_title'] = 'Item Create';
			$data['brands'] = Brand::where('status','ACTIVE')->get();
			$data['categories'] = ItemCategory::where('status','ACTIVE')->get();
			$data['campaigns'] = Campaign::where('status','ACTIVE')->get();
			$data['colors'] = Color::where('status','ACTIVE')->get();
			$data['sizes'] = Size::where('status','ACTIVE')->get();
			$data['models'] = ItemModel::where('status','ACTIVE')->get();
			$data['freebies'] = FreebiesCategory::where('status','ACTIVE')->get();
			$data['freebies_set'] = FreebiesCategory::where('status','ACTIVE')->get();
			
            return view('item.add',$data);
		}

		public function getEdit($id)
		{
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE || $this->button_edit==FALSE) {    
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			}

            $data = [];
			$data['row'] = Item::where('id',$id)->first();
            $data['page_title'] = 'Item Update';
			$data['brands'] = Brand::where('status','ACTIVE')->get();
			$data['categories'] = ItemCategory::where('status','ACTIVE')->get();
			$data['campaigns'] = Campaign::where('status','ACTIVE')->get();
			$data['colors'] = Color::where('status','ACTIVE')->get();
			$data['sizes'] = Size::where('status','ACTIVE')->get();
			$data['models'] = ItemModel::where('status','ACTIVE')->get();
			$data['freebies'] = FreebiesCategory::where('status','ACTIVE')->get();
			$data['freebies_set'] = FreebiesCategory::where('status','ACTIVE')->get();
			
            return view('item.edit',$data);
		}

		public function inventoryUpload(Request $request)
		{
			$errors = array();
			$path_excel = $request->file('import_file')->store('temp');
			$path = storage_path('app').'/'.$path_excel;
            HeadingRowFormatter::default('none');
            $headings = (new HeadingRowImport)->toArray($path);
            //check headings
            $header = array("DIGITS CODE","QTY");

			for ($i=0; $i < sizeof($headings[0][0]); $i++) {
				if (!in_array($headings[0][0][$i], $header)) {
					$unMatch[] = $headings[0][0][$i];
				}
			}

			if(!empty($unMatch)) {
                return redirect()->back()->with(['message_type' => 'danger', 'message' => 'Failed ! Please check template headers, mismatched detected.']);
			}
            HeadingRowFormatter::default('slug');
            $excelData = Excel::toArray(new ItemInventoryImport, $path);
			$uploaded_items = array_column($excelData[0], "digits_code");
            $items = array_unique(array_column($excelData[0], "digits_code"));

			if(count((array)$uploaded_items) != count((array)$items)){
				array_push($errors, 'duplicate item found!');
			}

            //data checking
            foreach ($items as $item) {
                $itemDetails = Item::where('digits_code',$item)->first();
                if(empty($itemDetails)){
                    array_push($errors, 'item '.$item.' not found!');
                }
            }

            if(!empty($errors)){
                return redirect()->back()->with(['message_type' => 'danger', 'message' => 'Failed ! Please check '.implode(", ",$errors)]);
            }

            Excel::import(new ItemInventoryImport, $path);

            return redirect()->back()->with(['message_type' => 'success', 'message' => 'Upload complete!']);
		}

		public function inventoryTemplate()
		{
			$header = array("DIGITS CODE","QTY");
            $export = new ExcelTemplateExport([$header]);
            return Excel::download($export, 'inventory-'.date("Ymd").'-'.date("h.i.sa").'.csv');
		}

		public function inventoryView()
		{
			if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE || $this->button_add==FALSE) {
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

            $data = [];
            $data['page_title'] = 'Upload Inventory';
            $data['uploadRoute'] = route('item-inventory.upload');
            $data['uploadTemplate'] = route('item-inventory.template');
            return view('upload.upload',$data);
		}

		public function itemView()
		{
			if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE || $this->button_add==FALSE) {
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

            $data = [];
            $data['page_title'] = 'Upload Items';
            $data['uploadRoute'] = route('item.upload');
            $data['uploadTemplate'] = route('item.template');
            return view('upload.upload',$data);
		}

		public function itemTemplate()
		{
			$header = array("DIGITS CODE","UPC CODE","ITEM DESCRIPTION",
				"BRAND","CATEGORY","MODEL","ACTUAL COLOR","SIZE",
				"CURRENT SRP","CAMPAIGN","IS FREEBIE","INCLUDED FREEBIE",
				"FREEBIE CATEGORY","WH QTY");
            $export = new ExcelTemplateExport([$header]);
            return Excel::download($export, 'item-'.date("Ymd").'-'.date("h.i.sa").'.csv');
		}

		public function itemUpload(Request $request)
		{
			$errors = array();
			$path_excel = $request->file('import_file')->store('temp');
			$path = storage_path('app').'/'.$path_excel;
            HeadingRowFormatter::default('none');
            $headings = (new HeadingRowImport)->toArray($path);
            //check headings
            $header = array("DIGITS CODE","UPC CODE","ITEM DESCRIPTION",
			"BRAND","CATEGORY","MODEL","ACTUAL COLOR","SIZE",
			"CURRENT SRP","CAMPAIGN","IS FREEBIE","INCLUDED FREEBIE",
			"FREEBIE CATEGORY","WH QTY");

			for ($i=0; $i < sizeof($headings[0][0]); $i++) {
				if (!in_array($headings[0][0][$i], $header)) {
					$unMatch[] = $headings[0][0][$i];
				}
			}

			if(!empty($unMatch)) {
                return redirect(route('item.view'))->with(['message_type' => 'danger', 'message' => 'Failed ! Please check template headers, mismatched detected.']);
			}
            HeadingRowFormatter::default('slug');
            $excelData = Excel::toArray(new ItemInventoryImport, $path);

            $brands = array_unique(array_column($excelData[0], "brand"));
			$categories = array_unique(array_column($excelData[0], "category"));
			$colors = array_unique(array_column($excelData[0], "actual_color"));
			$sizes = array_unique(array_column($excelData[0], "size"));
			$models = array_unique(array_column($excelData[0], "model"));
			$campaigns = array_unique(array_column($excelData[0], "campaign"));
			$isFreebies = array_unique(array_column($excelData[0], "is_freebie"));
			$freebie_categories = array_unique(array_column($excelData[0], "freebie_category"));
			$included_freebies = array_unique(array_column($excelData[0], "included_freebie"));
			$uploaded_items = array_column($excelData[0], "digits_code");
            $items = array_unique(array_column($excelData[0], "digits_code"));

			if(count((array)$uploaded_items) != count((array)$items)){
				array_push($errors, 'duplicate item found!');
			}

            //data checking
            foreach ($brands as $brand) {
                $brandDetails = Brand::where('brand_name',$brand)
					->where('status','ACTIVE')->first();
                if(empty($brandDetails)){
                    array_push($errors, 'brand '.$brand.' not found!');
                }
            }

			foreach ($categories as $category) {
				$categoryDetails = ItemCategory::where('category_name',$category)
					->where('status','ACTIVE')->first();
                if(empty($categoryDetails)){
                    array_push($errors, 'category '.$category.' not found!');
                }
			}

			foreach ($colors as $color) {
                $colorDetails = Color::where('color_name',$color)
					->where('status','ACTIVE')->first();
                if(empty($colorDetails)){
                    array_push($errors, 'color '.$color.' not found!');
					Color::firstOrCreate([
						'color_name' => $color,
						'status' => 'ACTIVE'
					]);
                }
            }

			foreach ($sizes as $size) {
                $sizeDetails = Size::where('size',$size)
					->where('status','ACTIVE')->first();
                if(empty($sizeDetails)){
                    array_push($errors, 'size '.$size.' not found!');
					Size::firstOrCreate([
						'size' => $size,
						'status' => 'ACTIVE'
					]);
                }
            }

			foreach ($models as $model) {
                $modelDetails = ItemModel::where('model_name',$model)
					->where('status','ACTIVE')->first();
                if(empty($modelDetails)){
                    array_push($errors, 'model '.$model.' not found!');
					ItemModel::firstOrCreate([
						'model_name' => $model,
						'status' => 'ACTIVE'
					]);
                }
            }

			foreach ($campaigns as $campaign) {
				// if($campaign == ''){
				// 	continue;
				// }
                $campaignDetails = Campaign::where('campaigns_name',$campaign)
					->where('status','ACTIVE')->first();
                if(empty($campaignDetails)){
                    array_push($errors, 'campaign '.$campaign.' not found!');
					// Campaign::firstOrCreate([
					// 	'campaigns_name' => $campaign,
					// 	'status' => 'ACTIVE'
					// ]);
                }
            }

			foreach ($freebie_categories as $category) {
				if($category == ''){
					continue;
				}
                $categoryDetails = FreebiesCategory::where('category_name',$category)
					->where('status','ACTIVE')->first();
                if(empty($categoryDetails)){
                    array_push($errors, 'freebie category '.$category.' not found!');
					FreebiesCategory::firstOrCreate([
						'category_name' => $category,
						'status' => 'ACTIVE'
					]);
                }
            }

			foreach ($included_freebies as $included_freebie) {
				if($included_freebie == ''){
					continue;
				}
				$freebieCategories = explode(",",$included_freebie);
				foreach ($freebieCategories as $freebieCategory) {
					$freebieCategoryDetails = FreebiesCategory::where('category_name',$freebieCategory)->where('status','ACTIVE')->first();
					if(empty($freebieCategoryDetails)){
						array_push($errors, 'included freebie '.$freebieCategory.' not found!');
					}
				}
                
			}

			foreach ($isFreebies as $isFreebie) {
				if(!in_array($isFreebie,["YES","NO"])){
					array_push($errors, 'is freebie should be YES/NO!');
				}
			}

            if(!empty($errors)){
                return redirect(route('item.view'))->with(['message_type' => 'danger', 'message' => 'Failed ! Please check '.implode(", ",$errors)]);
            }

            Excel::import(new ItemImport, $path);

            return redirect(CRUDBooster::mainpath())->with(['message_type' => 'success', 'message' => 'Upload complete!']);
		}

		public function itemSearch(Request $request)
		{
			$data = array();
            $data['status_no'] = 0;
            $data['message']   ='No Item Found!';
			$data['items'] = array();

			$itemDetails = Item::where('item_models_id',$request->model)
				->where('colors_id',$request->color)
				->where('sizes_id',$request->size)
				->where('campaigns_id',$request->campaign)
				->skip(0)
				->take(10)
				->get();

			if($itemDetails){

				$data['status_no'] = 1;
				$data['message']   ='Item Found!';
				$i = 0;

				foreach ($itemDetails as $key => $value) {
					$return_arr[$i]['id'] = $value->id;
					$return_arr[$i]['included_freebie'] = $value->included_freebies;
                    $return_arr[$i]['digits_code'] = $value->digits_code;
					$return_arr[$i]['item_description'] = $value->item_description;
					$return_arr[$i]['current_srp'] = $value->current_srp;
					$return_arr[$i]['wh_reserved_qty'] = $value->dtc_reserved_qty;
					$i++;
				}
				$data['items'] = $return_arr;
			}

			return json_encode($data);
		}

		public function freebiesSearch(Request $request)
		{
			$data = array();
			$freebiesDetails = Item::whereIn('freebies_categories_id',explode(",",$request->search))
				->where('is_freebies',1)
				->skip(0)
				->take(10)
				->orderBy('freebies_categories_id','ASC')
				->get();

			if($freebiesDetails){

				$i = 0;

				foreach ($freebiesDetails as $key => $value) {
					$return_arr[$i]['id'] = $value->id;
					$return_arr[$i]['background_color'] = FreebiesCategory::where('id',$value->freebies_categories_id)->value('color_style');
					$return_arr[$i]['category'] = $value->freebies_categories_id;
					$return_arr[$i]['digits_code'] = $value->digits_code;
					$return_arr[$i]['item_description'] = $value->item_description;
					$return_arr[$i]['current_srp'] = $value->current_srp;
					$return_arr[$i]['wh_reserved_qty'] = $value->dtc_reserved_qty;
					$i++;
				}
				$data['freebies'] = $return_arr;
			}

			return json_encode($data);
		}

		public function itemReservable(Request $request)
		{
			return json_encode(Item::where('digits_code',$request->item_code)->value('dtc_reserved_qty'));
		}

		public function getItemColors(Request $request)
		{
			$colors = Item::where('item_models_id',$request->model_id)
				->select('colors_id')
				->distinct()
				->get();

			return json_encode(Color::whereIn('id',$colors)
				->where('status','ACTIVE')
				->select('id','color_name')->get());
		}

		public function getItemSizes(Request $request)
		{
			$sizes = Item::where('item_models_id',$request->model_id)
				->where('colors_id',$request->color_id)
				->select('sizes_id')
				->distinct()
				->get();

			return json_encode(Size::whereIn('id',$sizes)
				->where('status','ACTIVE')
				->select('id','size')->get());
		}

		public function getItemModels(Request $request)
		{
			$models = Item::where('campaigns_id',$request->campaign_id)
				->where('is_freebies',0)
				->select('item_models_id')
				->distinct()
				->get();

			return json_encode(ItemModel::whereIn('id',$models)
				->where('status','ACTIVE')
				->select('id','model_name')->get());
		}
		
		public function itemDelete()
		{
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE || $this->button_edit==FALSE) {    
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			}

			Item::query()->delete();

			return redirect(CRUDBooster::mainpath())->with(['message_type' => 'success', 'message' => 'All items have been deleted!']);
		}

		public function itemExport(Request $request)
		{
			$filename = $request->input('filename');
			return Excel::download(new ItemExport, $filename.'.xlsx');
		}

	}