<?php

require_once('base_model.php');
require_once('configuracion.php');
require_once('cliente.php');

class Factura extends BaseModel {
	
	protected $table_name    = 'facturas';
	protected $list_fields   = array('id','tipo','cliente_id','rfc','fecha','total','activo','clientes.nombre AS nombre_cliente','serie','folio');
	protected $search_fields = array('rfc','clientes.nombre','serie','folio');
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

		// get the XML version
		foreach ($xml->xpath('//cfdi:Comprobante') as $item) {
			if ( ! empty((string)$item['version']) ) {
				$version = (string)$item['version'];
			} else if ( ! empty((string)$item['Version']) ) {
				$version = (string)$item['Version'];
			}
		}


		if ($version == '3.2') {
			$xmlData = $this->getDataXML32($xml);
		}

		if ($version == '3.3') {
			$xmlData = $this->getDataXML33($xml);
		}

		// check configuration RFC is equals to Emisor/Receptor
		if ($config->rfc != $xmlData['emisor_rfc'] && $config->rfc != $xmlData['receptor_rfc']) {
			$this->setError('El RFC del Emisor/Receptor del XML no coincide con el RFC de la configuracion');
			return false;
		}

		// Factura is emited o received
		if ($config->rfc == $xmlData['emisor_rfc']) {
			$tipo   = 'E';
			$rfc    = $xmlData['receptor_rfc'];
			$nombre = $xmlData['receptor_nombre'];
		} else {
			$tipo   = 'R';
			$rfc    = $xmlData['emisor_rfc'];
			$nombre = $xmlData['emisor_nombre'];
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

		// save Factura
		$data = array(
			'id'         => 0,
			'tipo'       => $tipo,
			'cliente_id' => $cliente_id,
			'rfc'        => $rfc,
			'fecha'      => str_replace('T', ' ', $xmlData['fecha']),
			'subtotal'   => $xmlData['subtotal'],
			'iva'        => $xmlData['impuestos_trasladados'],
			'total'      => $xmlData['total'],
			'activo'     => 1,
			'serie'      => $xmlData['serie'],
			'folio'      => $xmlData['folio']
		);

		if (!$this->save($data, true)) {
			return false;
		}

		return true;
	}

	private function getDataXML32($xml) {
		$data = array();

		// read emisor/receptor RFC from XML
		foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Emisor') as $item) {
			$data['emisor_rfc']    = (string)$item['rfc'];
			$data['emisor_nombre'] = (string)$item['nombre'];
		}

		foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Receptor') as $item) {
			$data['receptor_rfc']    = (string)$item['rfc'];
			$data['receptor_nombre'] = (string)$item['nombre'];
		}

		// read more data from XML
		foreach ($xml->xpath('//cfdi:Comprobante') as $item) {
			$data['serie']    = (string)$item['serie'];
			$data['folio']    = (string)$item['folio'];
			$data['fecha']    = (string)$item['fecha'];
			$data['subtotal'] = (float)$item['subTotal'];
			$data['total']    = (float)$item['total'];
		}

		foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Impuestos') as $item) {
			$data['impuestos_trasladados'] = (float)$item['totalImpuestosTrasladados'];
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

		return $data;
	}

	private function getDataXML33($xml) {
		$data = array();

		// read emisor/receptor RFC from XML
		foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Emisor') as $item) {
			$data['emisor_rfc']    = (string)$item['Rfc'];
			$data['emisor_nombre'] = (string)$item['Nombre'];
		}

		foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Receptor') as $item) {
			$data['receptor_rfc']    = (string)$item['Rfc'];
			$data['receptor_nombre'] = (string)$item['Nombre'];
		}

		// read more data from XML
		foreach ($xml->xpath('//cfdi:Comprobante') as $item) {
			$data['serie']    = (string)$item['Serie'];
			$data['folio']    = (string)$item['Folio'];
			$data['fecha']    = (string)$item['Fecha'];
			$data['subtotal'] = (float)$item['SubTotal'];
			$data['total']    = (float)$item['Total'];
		}

		foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Impuestos') as $item) {
			$data['impuestos_trasladados'] = (float)$item['TotalImpuestosTrasladados'];
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

		return $data;
	}
}

?>