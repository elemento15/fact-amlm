<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include('BaseController.php');

class Facturas extends BaseController {

	protected $modelName = 'Factura';

	public function load_xml() {
		$success = 0;
		$fail = 0;

		if (!count($_FILES)) {
			$resp = array('success' => false, 'msg' => 'No se pudo cargar el archivo');
		} else {
			//var_dump($_FILES['files']['tmp_name']);
			foreach ($_FILES['files']['tmp_name'] as $file) {
				$xml = simplexml_load_file($file, "SimpleXMLElement");
				if ($this->model->loadXML($xml)) {
					$success++;
				} else {
					$fail++;
				}
			}

			$resp = array(
				'success' => true, 
				'msg' => 'Proceso terminado', 
				'files' => [
					'success' => $success,
					'fail' => $fail,
				]
			);
		}

		echo json_encode($resp);
	}

	public function print_report() {
		$this->load->library('Rpt_Facturas');

		$params = array(
			'type_rpt'      => substr($_REQUEST['rpt'], 0, 1),
			'type_facturas' => substr($_REQUEST['type'], 0, 1),
			'fecha_inicial' => substr($_REQUEST['fec_ini'], 0, 10),
			'fecha_final'   => substr($_REQUEST['fec_fin'], 0, 10)
		);
		
		$this->rpt_facturas->setParams($params);
		$this->rpt_facturas->AddPage();
		$this->rpt_facturas->printRpt();
		$this->rpt_facturas->Output('rpt_facturas.pdf', 'I');
	}
	
}

?>