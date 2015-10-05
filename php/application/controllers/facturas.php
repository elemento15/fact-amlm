<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include('base_controller.php');

class Facturas extends BaseController {

	protected $modelName = 'Factura';

	function load_xml() {
		if (!count($_FILES)) {
			$resp = array('success' => false, 'msg' => 'No se pudo cargar el archivo');
		} else {
			$xml = simplexml_load_file($_FILES['file']['tmp_name'], "SimpleXMLElement");
			if ($this->model->loadXML($xml)) {
				$resp = array('success' => true, 'msg' => 'XML cargado exitosamente');
			} else {
				$resp = array('success' => false, 'msg' => $this->model->getError());
			}
		}

		echo json_encode($resp);
	}
	
}

?>