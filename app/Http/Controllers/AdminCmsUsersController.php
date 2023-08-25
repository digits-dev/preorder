<?php namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use DB;
use CRUDBooster;
use App\Models\User;
use App\Imports\UserImport;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use App\Exports\ExcelTemplateExport;
use App\Models\Store;
use Maatwebsite\Excel\Facades\Excel;
use crocodicstudio\crudbooster\controllers\CBController;

class AdminCmsUsersController extends CBController {


	public function cbInit() {
		# START CONFIGURATION DO NOT REMOVE THIS LINE
		$this->table               = 'cms_users';
		$this->primary_key         = 'id';
		$this->title_field         = "name";
		$this->button_action_style = 'button_icon';
		$this->button_import 	   = false;
		$this->button_export 	   = true;
		# END CONFIGURATION DO NOT REMOVE THIS LINE

		# START COLUMNS DO NOT REMOVE THIS LINE
		$this->col = array();
		$this->col[] = array("label"=>"Name","name"=>"name");
		$this->col[] = array("label"=>"Email","name"=>"email");
		$this->col[] = array("label"=>"Privilege","name"=>"id_cms_privileges","join"=>"cms_privileges,name");
		$this->col[] = array("label"=>"Channel","name"=>"channels_id","join"=>"channels,channel_name");
		$this->col[] = array("label"=>"Store","name"=>"stores_id","join"=>"stores,store_name");
		$this->col[] = array("label"=>"Photo","name"=>"photo","image"=>1);
		$this->col[] = array("label"=>"Status","name"=>"status");
		# END COLUMNS DO NOT REMOVE THIS LINE

		# START FORM DO NOT REMOVE THIS LINE
		$this->form = array();
		$this->form[] = array("label"=>"Name","name"=>"name",'width'=>'col-sm-6','validation'=>'required|min:3','readonly'=>(CRUDBooster::isSuperAdmin()) ? false : true );
		$this->form[] = array("label"=>"Email","name"=>"email",'width'=>'col-sm-6','type'=>'email','validation'=>'required|email|unique:cms_users,email,'.CRUDBooster::getCurrentId(),'readonly'=>(CRUDBooster::isSuperAdmin()) ? false : true);
		$this->form[] = array("label"=>"Photo","name"=>"photo",'width'=>'col-sm-6',"type"=>"upload","help"=>"Recommended resolution is 200x200px",'validation'=>'image|max:1000','resize_width'=>90,'resize_height'=>90);
		$this->form[] = array("label"=>"Privilege","name"=>"id_cms_privileges","type"=>"select","datatable"=>"cms_privileges,name",'width'=>'col-sm-6','validation'=>'required');
		$this->form[] = array("label"=>"Channel","name"=>"channels_id",'width'=>'col-sm-6',"type"=>"select","datatable"=>"channels,channel_name");
		$this->form[] = array("label"=>"Store","name"=>"stores_id",'width'=>'col-sm-6',"type"=>"select","datatable"=>"stores,store_name","parent_select"=>"channels_id");
		$this->form[] = array("label"=>"Password","name"=>"password","type"=>"password",'width'=>'col-sm-6',"help"=>"Please leave empty if not changed");
		if(in_array(CRUDBooster::getCurrentMethod(),['getEdit','postEditSave','getDetail'])) {
			$this->form[] = ['label'=>'Status','name'=>'status','type'=>'select','validation'=>'required','width'=>'col-sm-6','dataenum'=>'ACTIVE;INACTIVE'];
		}
		// $this->form[] = array("label"=>"Password Confirmation","name"=>"password_confirmation","type"=>"password","help"=>"Please leave empty if not changed");
		# END FORM DO NOT REMOVE THIS LINE


		$this->index_button = array();
		if(CRUDBooster::getCurrentMethod() == 'getIndex') {
			if(CRUDBooster::isSuperadmin()){
				$this->index_button[] = [
					"title"=>"Upload Users",
					"label"=>"Upload Users",
					"icon"=>"fa fa-upload",
					"color"=>"primary",
					"url"=>route('users.view')
				];
			}
		}

		$this->button_selected = array();
		if(CRUDBooster::isSuperadmin()){
			$this->button_selected[] = ["label"=>"Set Status ACTIVE ","icon"=>"fa fa-check-circle","name"=>"set_status_ACTIVE"];
			$this->button_selected[] = ["label"=>"Set Status INACTIVE","icon"=>"fa fa-times-circle","name"=>"set_status_INACTIVE"];
			$this->button_selected[] = ["label"=>"Reset Password","icon"=>"fa fa-refresh","name"=>"reset_password"];
		}
	}

	public function hook_query_index(&$query) {
		if(!CRUDBooster::isSuperAdmin()){
			$query->where('id',CRUDBooster::myId());
		}

	}

	public function actionButtonSelected($id_selected,$button_name) {
		//Your code here
		switch ($button_name) {
			case 'set_status_ACTIVE':
				DB::table('cms_users')->whereIn('id',$id_selected)->update([
					'status'=>'ACTIVE',
					'updated_at' => date('Y-m-d H:i:s')
				]);
				break;
			case 'set_status_INACTIVE':
				DB::table('cms_users')->whereIn('id',$id_selected)->update([
					'status'=>'INACTIVE',
					'updated_at' => date('Y-m-d H:i:s')
				]);
				break;
			case 'reset_password':
				DB::table('cms_users')->whereIn('id',$id_selected)->update([
					'password'=>bcrypt('qwerty2023'),
					'updated_at' => date('Y-m-d H:i:s')
				]);
				break;
			default:
				# code...
				break;
		}
	}

	public function getProfile() {

		$this->button_addmore = FALSE;
		$this->button_cancel  = FALSE;
		$this->button_show    = FALSE;
		$this->button_add     = FALSE;
		$this->button_delete  = FALSE;

		if(!CRUDBooster::isSuperAdmin()){
			$this->hide_form = [
				'id_cms_privileges',
				'photo',
				'channels_id',
				'stores_id',
				'password_confirmation'
			];
		}

		$data['page_title'] = cbLang("label_button_profile");
		$data['row'] = CRUDBooster::first('cms_users',CRUDBooster::myId());

        return $this->view('crudbooster::default.form',$data);
	}
	public function hook_before_edit(&$postdata,$id) {
		$postdata['name'] = strtoupper($postdata['name']);
		// unset($postdata['password_confirmation']);
	}
	public function hook_before_add(&$postdata) {
		$postdata['name'] = strtoupper($postdata['name']);
		$postdata['status'] = 'ACTIVE';
	    // unset($postdata['password_confirmation']);
	}

	public function usersUpload(Request $request)
	{
		$errors = array();
		$path_excel = $request->file('import_file')->store('temp');
		$path = storage_path('app').'/'.$path_excel;
		HeadingRowFormatter::default('none');
		$headings = (new HeadingRowImport)->toArray($path);
		//check headings
		$header = array("NAME","EMAIL","PRIVILEGE","CHANNEL","STORE");

		for ($i=0; $i < sizeof($headings[0][0]); $i++) {
			if (!in_array($headings[0][0][$i], $header)) {
				$unMatch[] = $headings[0][0][$i];
			}
		}

		if(!empty($unMatch)) {
			return redirect()->back()->with(['message_type' => 'danger', 'message' => 'Failed ! Please check template headers, mismatched detected.']);
		}
		HeadingRowFormatter::default('slug');
		$array = Excel::toArray(new UserImport, $path);
		$emails = array_unique(array_column($array[0], "email"));
		$stores = array_unique(array_column($array[0], "store"));
		$privilege = array_unique(array_column($array[0], "privilege"));

		//data checking
		foreach ($emails as $email) {
			$userDetails = User::where('email',$email)->first();
			if(!empty($userDetails)){
				array_push($errors, 'email '.$email.' already exists!');
			}
		}
		if($privilege[0] == "Requestor" || $privilege[0] == "Cashier"){
			foreach ($stores as $store) {
				$storeDetails = Store::where('store_name',$store)->first();
				if(empty($storeDetails)){
					array_push($errors, 'store '.$store.' not found!');
				}
			}
		}

		if(!empty($errors)){
			return redirect()->back()->with(['message_type' => 'danger', 'message' => 'Failed ! Please check '.implode(", ",$errors)]);
		}

		Excel::import(new UserImport, $path);

		return redirect()->back()->with(['message_type' => 'success', 'message' => 'Upload complete!']);
	}

	public function usersTemplate()
	{
		$header = array("NAME","EMAIL","PRIVILEGE","CHANNEL","STORE");
		$export = new ExcelTemplateExport([$header]);
		return Excel::download($export, 'users-'.date("Ymd").'-'.date("h.i.sa").'.csv');
	}

	public function usersView()
	{
		if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE || $this->button_add==FALSE) {
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
		}

		$data = [];
		$data['page_title'] = 'Upload Users';
		$data['uploadRoute'] = route('users.upload');
		$data['uploadTemplate'] = route('users.template');
		return view('upload.upload',$data);
	}

}
