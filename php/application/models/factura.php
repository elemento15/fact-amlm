<?php

include('base_model.php');

class Factura extends BaseModel {
	
	protected $table_name    = 'facturas';
	protected $list_fields   = array('id','tipo','cliente_id','rfc','fecha','total','activo');
	protected $search_fields = array('rfc');
	protected $save_fields   = array('tipo','cliente_id','rfc','fecha','subtotal','iva','total','activo');
	// protected $edit_fields   = array('tipo','cliente_id','rfc','fecha','subtotal','iva','total','activo');
	protected $new_defaults  = array('tipo' => 'R', 'activo' => 1);
	protected $avoid_delete  = false;
}

?>