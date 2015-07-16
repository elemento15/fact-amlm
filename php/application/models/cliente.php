<?php

include('base_model.php');

class Cliente extends BaseModel {
	
	protected $table_name    = 'clientes';
	protected $list_fields   = array('id','rfc','nombre','comercial');
	protected $search_fields = array('rfc','nombre','comercial');
	protected $save_fields   = array('rfc','nombre','comercial','calle','numero','colonia','cp');
	// protected $edit_fields   = array('rfc','nombre','comercial','calle','numero','colonia','cp');
	protected $new_defaults  = array('nombre' => '');
	protected $avoid_delete  = false;
}

?>