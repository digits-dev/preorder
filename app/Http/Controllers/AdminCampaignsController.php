<?php namespace App\Http\Controllers;

use crocodicstudio\crudbooster\helpers\CRUDBooster;

	class AdminCampaignsController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "campaigns_name";
			$this->limit = "20";
			$this->orderby = "created_at,desc";
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
			$this->table = "campaigns";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Campaigns Name","name"=>"campaigns_name"];
			$this->col[] = ["label"=>"Customer Order Limit","name"=>"max_order_count"];
			$this->col[] = ["label"=>"Status","name"=>"status"];
			$this->col[] = ["label"=>"Created By","name"=>"created_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Created Date","name"=>"created_at"];
			$this->col[] = ["label"=>"Updated By","name"=>"updated_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Updated Date","name"=>"updated_at"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Campaigns Name','name'=>'campaigns_name','type'=>'text','validation'=>'required|min:1|max:100','width'=>'col-sm-6'];
			$this->form[] = ['label'=>'Customer Order Limit','name'=>'max_order_count','type'=>'number','validation'=>'required','width'=>'col-sm-6'];

			if(in_array(CRUDBooster::getCurrentMethod(),['getEdit','postEditSave','getDetail'])) {
				$this->form[] = ['label'=>'Status','name'=>'status','type'=>'select','validation'=>'required','width'=>'col-sm-6','dataenum'=>'ACTIVE;INACTIVE'];
			}
			if(CRUDBooster::getCurrentMethod() == 'getDetail'){
				$this->form[] = ["label"=>"Created By","name"=>"created_by",'type'=>'select',"datatable"=>"cms_users,name"];
				$this->form[] = ['label'=>'Created Date','name'=>'created_at', 'type'=>'datetime'];
				$this->form[] = ["label"=>"Updated By","name"=>"updated_by",'type'=>'select',"datatable"=>"cms_users,name"];
				$this->form[] = ['label'=>'Updated Date','name'=>'updated_at', 'type'=>'datetime'];
			}

	        $this->table_row_color[] = ['color'=>'danger','condition'=>"[status] == 'INACTIVE'"];

	    }

	    public function hook_before_add(&$postdata) {
			$postdata['created_by']=CRUDBooster::myId();
	    }

	    public function hook_before_edit(&$postdata,$id) {
			$postdata['updated_by']=CRUDBooster::myId();
	    }

	}
