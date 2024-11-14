<?php

    namespace App\Http\Controllers;

	use Illuminate\Http\Request;
	use App\Exports\OrderExport;
    use App\Http\Helpers\Helper;
    use App\Models\Campaign;
	use App\Models\Channel;
	use App\Models\Customer;
	use App\Models\Item;
	use App\Models\Order;
	use App\Models\OrderFreebiesSetup;
	use App\Models\OrderLine;
use App\Models\OrderSchedule;
use App\Models\PaymentMethod;
	use App\Models\Store;
use Carbon\Carbon;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
    use Illuminate\Support\Facades\Validator;
	use Maatwebsite\Excel\Facades\Excel;

	class AdminOrdersController extends \crocodicstudio\crudbooster\controllers\CBController {

		private const ORDER_RESERVED = 1;
		private const ORDER_CANCELLED = 2;
		private const ORDER_PAID = 3;
		private const ORDER_CLAIMED = 2;
        private const ORDER_PARTIAL_CLAIMED = 3;
        private const CLAIMED_CANCELLED = 3;

        public function __construct(){
            $this->middleware('check.order.schedule')->only('getAdd');
        }

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "reference";
			$this->limit = "20";
			$this->orderby = "reference,desc";
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
            $this->col[] = ["label"=>"Payment Date","name"=>"paid_at"];
			$this->col[] = ["label"=>"Payment Status","name"=>"payment_statuses_id","join"=>"payment_statuses,status_style"];
			$this->col[] = ["label"=>"Claim Status","name"=>"claim_statuses_id","join"=>"claim_statuses,status_style"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Order Date','name'=>'order_date','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Reference','name'=>'reference','type'=>'text','validation'=>'required|min:1|max:10','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Campaign','name'=>'campaigns_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'campaigns,campaign_name'];
			$this->form[] = ['label'=>'Channel','name'=>'channels_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'channels,channel_name'];
			$this->form[] = ['label'=>'Store','name'=>'stores_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'stores,store_name'];
			$this->form[] = ['label'=>'Total Amount','name'=>'total_amount','type'=>'number','validation'=>'required','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Order Status','name'=>'order_statuses_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'order_statuses,status_name'];
			$this->form[] = ['label'=>'Payment Method','name'=>'payment_methods_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'payment_methods,payment_method'];
			$this->form[] = ['label'=>'Pre-order Invoice','name'=>'invoice_number','type'=>'text','validation'=>'required|min:1|max:50','width'=>'col-sm-10'];
			if(CRUDBooster::getCurrentMethod() == 'getDetail'){
				$this->form[] = ["label"=>"Created By","name"=>"created_by",'type'=>'select',"datatable"=>"cms_users,name"];
				$this->form[] = ['label'=>'Created Date','name'=>'created_at', 'type'=>'datetime'];
				$this->form[] = ["label"=>"Updated By","name"=>"updated_by",'type'=>'select',"datatable"=>"cms_users,name"];
				$this->form[] = ['label'=>'Updated Date','name'=>'updated_at', 'type'=>'datetime'];
			}

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

	        $this->index_button = array();
			$this->index_button[] = ['label'=>'Export Orders','url'=>"javascript:showOrderExport()",'icon'=>'fa fa-download'];

	        $this->table_row_color = array();

	        $this->script_js = null;
			$this->script_js = "
				function showOrderExport() {
					$('#modal-order-export').modal('show');
				}
			";

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

	        $this->style_css = null;
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


	    }

	    public function hook_query_index(&$query) {
	        //Your code here
	        if(!CRUDBooster::isSuperAdmin() && !in_array(CRUDBooster::myPrivilegeName(),["Ops","Brands","Accounting"])){
				$query->where('orders.stores_id', Helper::myStore());
			}
	    }

		public function getAdd()
		{
			if(!CRUDBooster::isCreate() && !$this->global_privilege || !$this->button_add) {
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

            $data = [];
            $data['page_title'] = 'Pre-Order Creation';

            $currentTime = Carbon::now();
            $activeSchedule = OrderSchedule::active()
                ->where('activity','create-order')
                ->get();

            $activeCampaigns = [];
            foreach($activeSchedule as $schedule){
                if ($currentTime->gte(Carbon::parse($schedule->start_date))) {
                    $currentCampaign = Campaign::active()->where('id', $schedule->campaigns_id)->get();
                    $activeCampaigns = array_merge($activeCampaigns, $currentCampaign->toArray());
                }
            }

            $data['campaigns'] = $activeCampaigns;
			$data['channels'] = Channel::where('status','ACTIVE')->get();
			$data['stores'] = Store::where('status','ACTIVE')->get();
			if(!CRUDBooster::isSuperAdmin()){
				$data['channels'] = Channel::where('status','ACTIVE')
					->where('id', Helper::myChannel())
					->get();
				$data['stores'] = Store::where('status','ACTIVE')
					->where('id', Helper::myStore())
					->get();
			}
			$data['paymentMethods'] = PaymentMethod::where('status','ACTIVE')->get();
			$data['orderSetup'] = OrderFreebiesSetup::where('status','ACTIVE')->first();
            return view('order.add',$data);
		}

		public function getDetail($id)
		{
			if(!CRUDBooster::isRead() && !$this->global_privilege || !$this->button_detail) {
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

            $data = [];
            $data['page_title'] = 'Pre-Order Details';
			$data['order_details'] = Order::withDetails($id);
			$data['order_items'] = OrderLine::withDetails($id);
            return view('order.detail',$data);
		}

        public function getPrint($id)
		{
			if(!CRUDBooster::isRead() && !$this->global_privilege || !$this->button_detail) {
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

            $data = [];
            $data['page_title'] = 'Pre-Order Details';
			$data['order_details'] = Order::withDetails($id);
			$data['order_items'] = OrderLine::withDetails($id);
            return view('order.print',$data);
		}

		public function getEdit($id)
		{
			if(!CRUDBooster::isUpdate() && !$this->global_privilege) {
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
			if (!CRUDBooster::myId()) {
                Session::flush();
				return view('crudbooster::login');
			}

			if(!CRUDBooster::isCreate() && !$this->global_privilege) {
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

            // $withFreebies = $request->with_freebies;

			$validator = Validator::make($request->all(), [
                'with_freebies' => 'required',
                'customer_name' => 'required',
				'email_address' => 'required|email',
				'contact_number' => 'required|numeric|digits:11',
				'order_items' => 'required|array|min:1',
				// 'order_items.*.digits_code' => 'required|string|digits:8|exists:items,digits_code',
                // 'order_items.*.qty' => 'required|integer|min:1',
                // 'order_items.*.amount' => 'required|numeric|min:0',
                // 'order_items.*.reservable_qty' => 'required|integer|min:1',
                // 'order_items.*.f_digits_code' => [
                // 'sometimes',
                // 'required_if:with_freebies,1',
                // 'string',
                // 'digits:8',
                // 'nullable',
                // 'exists:items,digits_code'
                // ],
                // 'order_items.*.f_qty' => [
                //     'sometimes',
                //     'required_if:with_freebies,1',
                //     'nullable',
                //     'integer',
                //     'min:1'
                // ],
                // 'order_items.*.f_amount' => [
                //     'sometimes',
                //     'required_if:with_freebies,1',
                //     'nullable',
                //     'numeric',
                //     'min:0'
                // ],
                // 'order_items.*.f_reservable_qty' => [
                //     'sometimes',
                //     'required_if:with_freebies,1',
                //     'nullable',
                //     'integer',
                //     'min:1'
                // ],
            ]);

			if ($validator->fails()) {
				return redirect(CRUDBooster::mainpath('add'))->with([
                    'message_type'=>'danger',
                    'message'=> implode(",", $validator->errors()->all())
                ])->withInput()->send();
			}

			if ($request->over_qty == 1) {
				return redirect(CRUDBooster::mainpath('add'))->with([
                    "message_type" => "danger",
                    "message" => "Please check over qty detected!"
                ])->withInput();
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
				return redirect(CRUDBooster::mainpath('add'))->with(["message"=>"Order limit reached for this customer!","message_type"=>"danger"])->withInput();
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

			foreach ($request->order_items as $items) {
                $dataItems = [
                    'orders_id' => $order->id,
					'digits_code' => $items['digits_code'],
					'qty' => $items['qty'],
					'amount' => $items['amount'],
					'available_qty' => $items['reservable_qty']
                ];

                if($request->with_freebies == 1 && !empty($items['f_digits_code'])){
                    $dataItems = [
                        'orders_id' => $order->id,
                        'digits_code' => $items['f_digits_code'],
                        'qty' => $items['f_qty'],
                        'amount' => 0,
                        'available_qty' => $items['f_reservable_qty']
                    ];
                }

				OrderLine::firstOrCreate($dataItems);

                DB::transaction(function () use ($items) {
                    $itemCode = $items['digits_code'] ?? $items['f_digits_code'];
                    $itemQty = $items['qty'] ?? $items['f_qty'];
                    $item = Item::where('digits_code', $itemCode)->lockForUpdate()->first();

                    // Check if the quantity is sufficient
                    if ($item->dtc_reserved_qty < $itemQty) {
                        return back()->with([
                            "message_type" => "danger",
                            "message" => "Not enough inventory available!"
                        ]);
                    }

                    $item->dtc_reserved_qty -= $itemQty;
                    $item->save();
                });

                DB::commit();
			}

			CRUDBooster::insertLog(cbLang("log_add", ['name' => $order->reference, 'module' => CRUDBooster::getCurrentModule()->name]));

            return self::getPrint($order->id);

		}

		public function preOrderCancel($id)
		{
			if (!CRUDBooster::myId()) {
				return view('crudbooster::login');
			}

			if(!CRUDBooster::isUpdate() && !$this->global_privilege) {
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$order = Order::where('id',$id)->first();
            //restrict if already cancelled
            if($order->order_statuses_id == self::ORDER_CANCELLED){
                return redirect(CRUDBooster::mainpath())->with(['message_type' => 'danger', 'message' =>"Order ".$order->reference." has already been cancelled!"])->send();
            }
			$items = OrderLine::where('orders_id',$id)->get();

			Order::where('id',$id)->update([
				'order_statuses_id' => self::ORDER_CANCELLED,
				'payment_statuses_id' => self::ORDER_CANCELLED,
                'cancelled_at' => date('Y-m-d H:i:s'),
                'cancelled_by' => CRUDBooster::myId(),
                'claim_statuses_id' => self::CLAIMED_CANCELLED,
				'updated_by' => CRUDBooster::myId()
			]);

			foreach ($items as $item) {
                DB::transaction(function () use ($item) {
                    $cancelledItem = Item::where('digits_code', $item->digits_code)->lockForUpdate()->first();

                    $cancelledItem->dtc_reserved_qty += $item->qty;
                    $cancelledItem->save();
                });

                DB::commit();
				// Item::where('digits_code', $item->digits_code)
				// 	->increment('dtc_reserved_qty', $item->qty);
			}

			CRUDBooster::insertLog(cbLang("log_update", ['name' => $order->reference.' cancelled ', 'module' => CRUDBooster::getCurrentModule()->name]));

			return redirect(CRUDBooster::mainpath())->with(['message_type' => 'warning', 'message' =>"Order ".$order->reference." has been cancelled!"])->send();

		}

		public function preOrderEditSave(Request $request)
		{
			if (!CRUDBooster::myId()) {
				return view('crudbooster::login');
			}

			if(!CRUDBooster::isUpdate() && !$this->global_privilege) {
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$validator = Validator::make($request->all(), [
                'order_id' => 'required|numeric',
				'invoice_number' => 'required|alpha_num'
            ]);

			if ($validator->fails()) {
				return redirect(CRUDBooster::mainpath('edit/'.$request->order_id))->with([
                    'message_type'=>'danger',
                    'message'=> implode(",", $validator->errors()->all())
                ])->withInput();
			}
			$order = Order::find($request->order_id);

            if(!empty($request->claimed)){
				$order->claim_statuses_id = (count($request->claimed) == count($request->claimed_date)) ? self::ORDER_CLAIMED : self::ORDER_PARTIAL_CLAIMED;
                $order->save();
                foreach ($request->claimed as $keyItem => $valueItem) {
                    $orderLines = OrderLine::where('orders_id',$request->order_id)
                        ->where('digits_code',$keyItem)->first();

                    $orderLines->claimed_date = $request->claimed_date[$keyItem];
                    $orderLines->claiming_invoice_number = $request->claiming_invoice_number[$keyItem];
                    $orderLines->save();
                }
            }
			if($request->invoice_number){

				$order->payment_statuses_id = self::ORDER_PAID;
                $order->paid_at = date('Y-m-d H:i:s');
				$order->invoice_number = $request->invoice_number;
				$order->save();
			}

			CRUDBooster::insertLog(cbLang("log_update", ['name' => $order->reference, 'module' => CRUDBooster::getCurrentModule()->name]));

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

        public function orderRestricted(){
            $data['page_title'] = "Order Restricted";
			return view('order.restricted', $data);
        }
	}
