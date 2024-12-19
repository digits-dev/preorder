<?php namespace App\Http\Controllers;

use App\Models\OrderSchedule;
use Carbon\Carbon;
use crocodicstudio\crudbooster\helpers\CRUDBooster;

	class AdminOrderSchedulesController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "id";
			$this->limit = "20";
			$this->orderby = "id,desc";
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
			$this->button_export = true;
			$this->table = "order_schedules";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Campaign","name"=>"campaigns_id","join"=>"campaigns,campaigns_name"];
			$this->col[] = ["label"=>"Activity","name"=>"activity"];
			$this->col[] = ["label"=>"Start Date","name"=>"start_date"];
			$this->col[] = ["label"=>"End Date","name"=>"end_date"];
			$this->col[] = ["label"=>"Status","name"=>"status"];
			$this->col[] = ["label"=>"Created By","name"=>"created_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Created Date","name"=>"created_at"];
			$this->col[] = ["label"=>"Updated By","name"=>"updated_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Updated Date","name"=>"updated_at"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Campaign','name'=>'campaigns_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-6',
                'datatable'=>'campaigns,campaigns_name','datatable_where'=>"status='ACTIVE'"];
			$this->form[] = ['label'=>'Activity','name'=>'activity','type'=>'select','validation'=>'required','width'=>'col-sm-6',
                'dataenum'=>'import-items;create-orders'];
			$this->form[] = ['label'=>'Start Date','name'=>'start_date','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-6'];
			$this->form[] = ['label'=>'End Date','name'=>'end_date','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-6'];
			if(in_array(CRUDBooster::getCurrentMethod(),['getEdit','postEditSave','getDetail'])) {
				$this->form[] = ['label'=>'Status','name'=>'status','type'=>'select','validation'=>'required','width'=>'col-sm-6','dataenum'=>'ACTIVE;INACTIVE'];
			}
			if(CRUDBooster::getCurrentMethod() == 'getDetail'){
				$this->form[] = ["label"=>"Created By","name"=>"created_by",'type'=>'select',"datatable"=>"cms_users,name"];
				$this->form[] = ['label'=>'Created Date','name'=>'created_at', 'type'=>'datetime'];
				$this->form[] = ["label"=>"Updated By","name"=>"updated_by",'type'=>'select',"datatable"=>"cms_users,name"];
				$this->form[] = ['label'=>'Updated Date','name'=>'updated_at', 'type'=>'datetime'];
			}

	        $this->table_row_color[] = ["color"=>"danger","condition"=>"[status]=='INACTIVE'"];

            $this->button_selected = [];
            if(CRUDBooster::isSuperadmin()){
                $this->button_selected[] = ["label"=>"Set Status ACTIVE ","icon"=>"fa fa-check-circle","name"=>"set_status_ACTIVE"];
                $this->button_selected[] = ["label"=>"Set Status INACTIVE","icon"=>"fa fa-times-circle","name"=>"set_status_INACTIVE"];
            }

	    }

	    public function actionButtonSelected($id_selected,$button_name) {
            $data = [
                'updated_at' => now(),
                'updated_by' => CRUDBooster::myId()
            ];

            if($button_name == 'set_status_ACTIVE'){
                $data['status'] = 'ACTIVE';
            }
            elseif($button_name == 'set_status_INACTIVE'){
                $data['status'] = 'INACTIVE';
            }

            OrderSchedule::whereIn('id', $id_selected)
                ->update($data);
	    }

	    public function hook_before_add(&$postdata) {
            $postdata['created_by']=CRUDBooster::myId();
	    }

	    public function hook_before_edit(&$postdata,$id) {
            $postdata['updated_by']=CRUDBooster::myId();
	    }

        public function deactivateSchedule() {
            $activeSchedule = OrderSchedule::where('status','ACTIVE')
                ->orderBy('start_date','asc')
                ->first();

            $timeNow = Carbon::now();
            if($timeNow->gt(Carbon::parse($activeSchedule->end_date))){
                $activeSchedule->status = 'INACTIVE';
                $activeSchedule->save();
            }

        }

	}
