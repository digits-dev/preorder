<?php namespace App\Http\Controllers;

	use Session;
	use Illuminate\Http\Request;
	use DB;
	use CRUDBooster;
	use App\Exports\OrderExport;
	use App\Models\Campaign;
	use App\Models\Channel;
	use App\Models\Color;
	use App\Models\Customer;
	use App\Models\Item;
	use App\Models\ItemModel;
	use App\Models\Order;
	use App\Models\OrderFreebiesSetup;
	use App\Models\OrderLine;
	use App\Models\PaymentMethod;
	use App\Models\Size;
	use App\Models\Store;
	use Illuminate\Support\Facades\Validator;
	use Maatwebsite\Excel\Facades\Excel;

	class AdminOrdersController extends \crocodicstudio\crudbooster\controllers\CBController {

		private const ORDER_RESERVED = 1;
		private const ORDER_CANCELLED = 2;
		private const ORDER_PAID = 3;
		private const ORDER_CLAIMED = 2;

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "reference";
			$this->limit = "20";
			$this->orderby = "reference,asc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = true;
			$this->button_action_style = "button_icon";
			$this->button_add = true;
			$this->button_edit = false;
			$this->button_delete = false;
			$this->button_detail = true;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = false;
			$this->table = "orders";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Order Date","name"=>"order_date"];
			$this->col[] = ["label"=>"Reference #","name"=>"reference"];
			$this->col[] = ["label"=>"Campaign","name"=>"campaigns_id","join"=>"campaigns,campaigns_name"];
			$this->col[] = ["label"=>"Customer","name"=>"customers_id","join"=>"customers,customer_name"];
			$this->col[] = ["label"=>"Channel","name"=>"channels_id","join"=>"channels,channel_name"];
			$this->col[] = ["label"=>"Pickup Location","name"=>"stores_id","join"=>"stores,store_name"];
			$this->col[] = ["label"=>"Total Qty","name"=>"total_qty"];
			$this->col[] = ["label"=>"Total Amount","name"=>"total_amount"];
			$this->col[] = ["label"=>"Payment Methods","name"=>"payment_methods_id","join"=>"payment_methods,payment_method"];
			$this->col[] = ["label"=>"Pre-order Invoice #","name"=>"invoice_number"];
			// $this->col[] = ["label"=>"Order Status","name"=>"order_statuses_id","join"=>"order_statuses,status_style"];
			$this->col[] = ["label"=>"Payment Status","name"=>"payment_statuses_id","join"=>"payment_statuses,status_style"];
			$this->col[] = ["label"=>"Claim Status","name"=>"claim_statuses_id","join"=>"claim_statuses,status_style"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Order Date','name'=>'order_date','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Reference','name'=>'reference','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Campaign','name'=>'campaigns_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'campaigns,campaign_name'];
			$this->form[] = ['label'=>'Channel','name'=>'channels_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'channels,channel_name'];
			$this->form[] = ['label'=>'Store','name'=>'stores_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'stores,store_name'];
			$this->form[] = ['label'=>'Total Amount','name'=>'total_amount','type'=>'number','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Order Status','name'=>'order_statuses_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'order_statuses,status_name'];
			$this->form[] = ['label'=>'Payment Method','name'=>'payment_methods_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'payment_methods,payment_method'];
			$this->form[] = ['label'=>'Pre-order Invoice','name'=>'invoice_number','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			if(CRUDBooster::getCurrentMethod() == 'getDetail'){
				$this->form[] = ["label"=>"Created By","name"=>"created_by",'type'=>'select',"datatable"=>"cms_users,name"];
				$this->form[] = ['label'=>'Created Date','name'=>'created_at', 'type'=>'datetime'];
				$this->form[] = ["label"=>"Updated By","name"=>"updated_by",'type'=>'select',"datatable"=>"cms_users,name"];
				$this->form[] = ['label'=>'Updated Date','name'=>'updated_at', 'type'=>'datetime'];
			}# END FORM DO NOT REMOVE THIS LINE

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
			if(CRUDBooster::isUpdate()){
				$this->addaction[] = [
					"title" => "Cancel Order",
					"icon" => "fa fa-times",
					"color" => "danger",
					"confirmation" => true,
					"confirmation_title" => "Order Cancellation!",
					'showIf' => '[payment_statuses_id] == '.self::ORDER_RESERVED,
					"url" => CRUDBooster::mainpath('preorder-cancel/[id]')];

				$this->addaction[] = [
						"title" => "Update Order",
						"icon" => "fa fa-pencil",
						"color" => "warning",
						'showIf' => '[payment_statuses_id] != '.self::ORDER_CANCELLED,
						"url" => CRUDBooster::mainpath('edit/[id]')];
			}
			

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
			$this->index_button[] = ['label'=>'Export Orders','url'=>"javascript:showOrderExport()",'icon'=>'fa fa-download'];


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
	        $this->script_js = NULL;
			$this->script_js = "
				function showOrderExport() {
					$('#modal-order-export').modal('show');
				}
			";

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
	        $this->post_index_html = null;
	        $this->post_index_html = "
			<div class='modal fade' tabindex='-1' role='dialog' id='modal-order-export'>
				<div class='modal-dialog'>
					<div class='modal-content'>
						<div class='modal-header'>
							<button class='close' aria-label='Close' type='button' data-dismiss='modal'>
								<span aria-hidden='true'>×</span></button>
							<h4 class='modal-title'><i class='fa fa-download'></i> Export Orders</h4>
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
	        $this->style_css = "
			@media only screen and (max-width: 600px) { 
				a {
					padding: 2px;
				}

				h1 {
					font-size: 18px !important;
				}

				.btn {
					padding: 5px;
				}

				input[name='q'] {
					padding: 5px !important;
					width: 50% !important;
				}

				input[name='limit'] {
					
				}
			}";
	        
	        
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
	        if(!CRUDBooster::isSuperAdmin() && !in_array(CRUDBooster::myPrivilegeName(),["Ops","Brands"])){
				$query->where('orders.stores_id',CRUDBooster::myStore());
			}    
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
	    	//Your code here
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
            $data['page_title'] = 'Pre-Order Creation';
			$data['campaigns'] = Campaign::where('status','ACTIVE')->get();
			$data['channels'] = Channel::where('status','ACTIVE')->get();
			$data['stores'] = Store::where('status','ACTIVE')->get();
			if(!CRUDBooster::isSuperAdmin()){
				$data['channels'] = Channel::where('status','ACTIVE')
					->where('id',CRUDBooster::myChannel())
					->get();
				$data['stores'] = Store::where('status','ACTIVE')
					->where('id',CRUDBooster::myStore())
					->get();
			}
			$data['paymentMethods'] = PaymentMethod::where('status','ACTIVE')->get();
			$data['orderSetup'] = OrderFreebiesSetup::where('status','ACTIVE')->first();
            return view('order.add',$data);
		}

		public function getDetail($id)
		{
			if(!CRUDBooster::isRead() && $this->global_privilege==FALSE || $this->button_detail==FALSE) {
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

            $data = [];
            $data['page_title'] = 'Pre-Order Details';
			$data['order_details'] = Order::withDetails($id);
			$data['order_items'] = OrderLine::withDetails($id);
            return view('order.detail',$data);
		}

		public function getEdit($id)
		{
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

            $data = [];
            $data['page_title'] = 'Pre-Order Edit';
			$data['order_details'] = Order::withDetails($id);
			$data['order_items'] = OrderLine::withDetails($id);
            return view('order.edit',$data);
		}
		
		public function preOrderSave(Request $request)
		{
			// dd($request->all());
			if (!CRUDBooster::myId()) {
				return view('crudbooster::login');
			}

			if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE) {
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$validator = \Validator::make($request->all(), [
                'customer_name' => 'required|alpha_spaces',
				'email_address' => 'required|email',
				'contact_number' => 'required|numeric|digits:11',
				'digits_code' => 'required',
				'qty' => 'required',
            ]);
			
			if ($validator->fails()) {
				return redirect(CRUDBooster::mainpath('add'))->withErrors($validator)->withInput();
			}

			if ($request->over_qty == 1) {
				return redirect(CRUDBooster::mainpath('add'))->withErrors(["Please check over qty detected!"])->withInput();
			}

			$customer = Customer::updateOrCreate(['email_address' => $request->email_address],[
				'customer_name' => $request->customer_name,
				'email_address' => $request->email_address,
				'contact_number' => $request->contact_number,
				'payment_methods_id' => $request->payment_methods_id,
				'status' => 'ACTIVE'
			]);

			$orderLimit = Campaign::withOrderLimit($request->campaigns_id);
			$customerOrderCount = Order::withCustomerOrder($customer->id,$request->campaigns_id) + 1;
			if($orderLimit < $customerOrderCount){
				return redirect(CRUDBooster::mainpath('add'))->withErrors(["Order limit reached for this customer!"])->withInput();
			}

			$order = Order::firstOrCreate([
				'order_date' => date('Y-m-d H:i:s'),
				'campaigns_id' => $request->campaigns_id,
				'channels_id' => $request->channels_id,
				'customers_id' => $customer->id,
				'stores_id' => $request->stores_id,
				'total_qty' => $request->total_quantity,
				'total_amount' => floatval($request->total_amount),
				'payment_methods_id' => $request->payment_methods_id,
				'order_statuses_id' => self::ORDER_RESERVED,
				'payment_statuses_id' => self::ORDER_RESERVED,
			]);

			foreach ($request->digits_code as $key => $digits_code) {
				OrderLine::firstOrCreate([
					'orders_id' => $order->id,
					'digits_code' => $digits_code,
					'qty' => $request->qty[$key],
					'amount' => $request->amount[$key],
					'available_qty' => $request->reservable_qty[$key]
				]);

				Item::where('digits_code',$digits_code)
					->decrement('dtc_reserved_qty',$request->qty[$key]);
			}

			if($request->with_freebies == 1){
				foreach ($request->f_digits_code as $key => $freebies) {
					OrderLine::firstOrCreate([
						'orders_id' => $order->id,
						'digits_code' => $freebies,
						'qty' => $request->f_qty[$key],
						'amount' => 0,
						'available_qty' => $request->f_amount[$key]
					]);

					Item::where('digits_code',$freebies)
						->decrement('dtc_reserved_qty',$request->f_qty[$key]);
				}
			}

			return redirect(CRUDBooster::mainpath())->with(['message_type' => 'success', 'message' => 'Order reserved!']);
			
		}

		public function preOrderCancel($id)
		{
			if (!CRUDBooster::myId()) {
				return view('crudbooster::login');
			}

			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$order = Order::where('id',$id)->first();
			$items = OrderLine::where('orders_id',$id)->get();

			Order::where('id',$id)->update([
				'order_statuses_id' => self::ORDER_CANCELLED,
				'payment_statuses_id' => self::ORDER_CANCELLED,
				'updated_by' => CRUDBooster::myId()
			]);

			foreach ($items as $item) {
				Item::where('digits_code', $item->digits_code)
					->increment('dtc_reserved_qty', $item->qty);
			}

			return redirect(CRUDBooster::mainpath())->with(['message_type' => 'warning', 'message' =>"Order ".$order->reference." has been cancelled!"]);
			
		}

		public function preOrderEditSave(Request $request)
		{
			if (!CRUDBooster::myId()) {
				return view('crudbooster::login');
			}

			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$validator = Validator::make($request->all(), [
                'order_id' => 'required|numeric',
				'invoice_number' => 'required|alpha_num'
            ]);

			if ($validator->fails()) {
				return redirect(CRUDBooster::mainpath('edit/'.$request->order_id))->withErrors($validator)->withInput();
			}
			$order = Order::find($request->order_id);
			if($request->claimed_date){
				
				$order->claim_statuses_id = self::ORDER_CLAIMED;
				$order->claimed_date = $request->claimed_date;
				$order->claiming_invoice_number = $request->claiming_invoice_number;
				$order->save();
			}
			if($request->invoice_number){
				
				$order->payment_statuses_id = self::ORDER_PAID;
				$order->invoice_number = $request->invoice_number;
				$order->save();
			}

			return redirect(CRUDBooster::mainpath())->with(['message_type' => 'success', 'message' => 'Order has been updated!']);
		}

		public function preOrderExport(Request $request)
		{
			$filename = $request->input('filename');
			return Excel::download(new OrderExport, $filename.'.xlsx');
		}

		public function getCustomerOrderCount(Request $request)
		{
			$customer = Customer::where('email_address',$request->email_address)->first();
			return json_encode(Order::where('customers_id',$customer->id)
				->where('campaigns_id',$request->campaign)
				->where('payment_statuses_id','!=',self::ORDER_CANCELLED)
				->select('id')->get()->count());
		}
	}