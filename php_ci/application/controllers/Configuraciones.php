<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include('BaseController.php');

class Configuraciones extends BaseController {

	protected $modelName = 'Configuracion';

	public function read_one () {
		$data = $this->model->getConfiguracion();
		$response = array('success' => true, 'data' => $data);
		echo json_encode($response);
	}
	
}

?>