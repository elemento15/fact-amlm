<?php

require_once('base_model.php');
require_once('configuracion.php');
require_once('cliente.php');

class Factura extends BaseModel {
	
	protected $table_name    = 'facturas';
	protected $list_fields   = array('id','tipo','cliente_id','rfc','fecha','total','activo','clientes.nombre AS nombre_cliente');
	protected $search_fields = array('rfc','clientes.nombre');
	protected $save_fields   = array('tipo','cliente_id','rfc','fecha','subtotal','iva','total','activo');
	// protected $edit_fields   = array('tipo','cliente_id','rfc','fecha','subtotal','iva','total','activo');
	protected $new_defaults  = array('tipo' => 'R', 'activo' => 1);
	protected $avoid_delete  = false;

	protected $join_tables = array(
		                         array('table' => 'clientes', 'fk' => 'cliente_id')
		                     );


	public function loadXML($xml) {
		// read rfc from configuration
		$mod_conf = new Configuracion();
		$mod_cli  = new Cliente();
		$config = $mod_conf->getConfiguracion();

		if (!$config) {
			$this->setError('Falta configurar el RFC');
			return false;
		}

		// read emisro/receptor RFC from XML
		foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Emisor') as $item) {
			$emisor_rfc    = (string)$item['rfc'];
			$emisor_nombre = (string)$item['nombre'];
		}

		foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Receptor') as $item) {
			$receptor_rfc    = (string)$item['rfc'];
			$receptor_nombre = (string)$item['nombre'];
		}

		// check configuration RFC is equals to Emisor/Receptor
		if ($config->rfc != $emisor_rfc && $config->rfc != $receptor_rfc) {
			$this->setError('El RFC del Emisor/Receptor del XML no coincide con el RFC de la configuracion');
			return false;
		}

		// Factura is emited o received
		if ($config->rfc == $emisor_rfc) {
			$tipo   = 'E';
			$rfc    = $receptor_rfc;
			$nombre = $receptor_nombre;
		} else {
			$tipo   = 'R';
			$rfc    = $emisor_rfc;
			$nombre = $emisor_nombre;
		}

		// check if Cliente exists, or create it
		$params = array(
			'order'  => null,
			'start'  => 0,
			'length' => 1,
			'search' => null,
			'filter' => array(array('field' => 'rfc', 'value' => $rfc))
		);
		$recs = $mod_cli->findAll($params);

		if ($recs['filtered'] == 0) {
			// create new Cliente
			$data = array(
				'id' => 0,
				'rfc' => $rfc,
				'nombre' => $nombre
			);
			if (!($cliente_id = $mod_cli->save($data, true))) {
				$this->setError($mod_cli->getError());
				return false;
			}
		} else {
			$cliente_id = $recs['data'][0]->id;
		}

		// read more data from XML
		foreach ($xml->xpath('//cfdi:Comprobante') as $item) {
			$fecha    = (string)$item['fecha'];
			$subtotal = (float)$item['subTotal'];
			$total    = (float)$item['total'];
		}

		foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Impuestos') as $item) {
			$impuestos_trasladados = (float)$item['totalImpuestosTrasladados'];
		}


		// foreach ($xml->xpath('//cfdi:Comprobante') as $item) {
		// 	var_dump((string)$item);
		// 	// $xml2 = new SimpleXmlIterator($item->asXML());
		// 	// $xml2->registerXPathNamespace("cfdi", "http://www.w3.org/2005/Atom");
		// 	// var_dump($xml2);
		// }

		// foreach ($xml->children('cfdi',true) as $item) {
		// 	if ($item->getName() == 'Complemento') {
		// 		$item->registerXPathNamespace('tfd',true);
		// 		var_dump($item->attributes);
		// 	}
		// }


		// save Factura
		$data = array(
			'id'         => 0,
			'tipo'       => $tipo,
			'cliente_id' => $cliente_id,
			'rfc'        => $rfc,
			'fecha'      => str_replace('T', ' ', $fecha),
			'subtotal'   => $subtotal,
			'iva'        => $impuestos_trasladados,
			'total'      => $total,
			'activo'     => 1
		);

		if (!$this->save($data, true)) {
			return false;
		}

		return true;
	}
}

?>