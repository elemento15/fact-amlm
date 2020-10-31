<?php

require_once('BaseModel.php');
require_once('Configuracion.php');
require_once('Cliente.php');

class Factura extends BaseModel {
	
	protected $table_name    = 'facturas';
	protected $list_fields   = array('id','tipo','cliente_id','rfc','fecha','total','activo','clientes.nombre AS nombre_cliente',
		                             'serie','folio','creado');
	protected $search_fields = array('rfc','clientes.nombre','serie','folio','fecha');
	protected $save_fields   = array('tipo','cliente_id','rfc','fecha','descuento','subtotal','iva','total','activo','creado',
		                             'sello_cfd','certificado_sat','sello_sat');
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
			'descuento'  => $xmlData['descuento'],
			'iva'        => $xmlData['impuestos_trasladados'],
			'total'      => $xmlData['total'],
			'activo'     => 1,
			'serie'      => $xmlData['serie'],
			'folio'      => $xmlData['folio'],
			'creado'     => date('Y-m-d H:i:s'),

			'sello_cfd'       => $xmlData['sello_cfd'],
			'certificado_sat' => $xmlData['certificado_sat'],
			'sello_sat'       => $xmlData['sello_sat']
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
			$data['decuento'] = 0; // avoid error if do not exist
			$data['total']    = (float)$item['total'];

			$data['sello_cfd'] = '';
			$data['certificado_sat'] = '';
			$data['sello_sat'] = '';
		}

		foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Impuestos') as $item) {
			$data['impuestos_trasladados'] = (float)$item['totalImpuestosTrasladados'];
		}

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
			$data['serie']     = (string)$item['Serie'];
			$data['folio']     = (string)$item['Folio'];
			$data['fecha']     = (string)$item['Fecha'];
			$data['subtotal']  = (float)$item['SubTotal'];
			$data['descuento'] = (isset($item['Descuento'])) ? (float)$item['Descuento'] : 0;
			$data['total']     = (float)$item['Total'];
		}

		foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Impuestos') as $item) {
			if (isset($item['TotalImpuestosTrasladados'])) {
				$data['impuestos_trasladados'] = (float)$item['TotalImpuestosTrasladados'];
			} else {
				$data['impuestos_trasladados'] = 0;
			}
		}

		if (! isset($data['impuestos_trasladados'])) {
			$data['impuestos_trasladados'] = 0;
		}


		$cfdi = $xml->xpath('//cfdi:Comprobante//cfdi:Complemento');
		foreach ($cfdi as $c) {
			$c->registerXPathNamespace('tfd', 'http://www.sat.gob.mx/TimbreFiscalDigital');
			$tfd = $c->xpath('//tfd:TimbreFiscalDigital');
			foreach ($tfd as $t) {
				$data['sello_cfd'] = (string)$t['SelloCFD'];
				$data['certificado_sat'] = (string)$t['NoCertificadoSAT'];
				$data['sello_sat'] = (string)$t['SelloSAT'];
			}
		}

		return $data;
	}
}

?>