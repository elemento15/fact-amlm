<?php

require_once('BaseModel.php');

class Configuracion extends BaseModel {
	
	protected $table_name    = 'configuracion';
	protected $list_fields   = array('id','nombre','rfc');
	protected $search_fields = array('nombre','rfc');
	protected $save_fields   = array('nombre','rfc');
	// protected $edit_fields   = array('nombre','rfc');
	protected $new_defaults  = array('nombre' => '', 'rfc' => '');
	protected $avoid_delete  = true;

	public function getConfiguracion() {
		$params = array(
			'order'  => null,
			'start'  => 0,
			'length' => 1,
			'search' => null,
			'filter' => array()
		);
		$recs = $this->findAll($params);

		if ($recs['total'] == 0) {
			return false;
		} else {
			$id = $recs['data'][0]->id;
			$data = $this->find($id);
			return $data;
		}
	}
}

?>