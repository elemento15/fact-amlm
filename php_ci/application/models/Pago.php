<?php

require_once('BaseModel.php');

class Pago extends BaseModel {
	
	protected $table_name    = 'pagos';
	protected $list_fields   = array('id','anio','mes','total','concepto');
	protected $search_fields = array('anio','mes');
	protected $save_fields   = array('anio','mes','total','concepto');
	protected $new_defaults  = array('concepto' => '');
	protected $avoid_delete  = false;


	public function setOrder($order) {
		$this->db->order_by($this->table_name.'.anio', $order['direction']);
		$this->db->order_by($this->table_name.'.mes', $order['direction']);
	}
}

?>