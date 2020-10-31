<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('tcpdf/tcpdf.php');

class RptFacturas extends TCPDF {

	public function __construct() {
		parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT);

		$this->setHeaderMargin(10);
		$this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP + 2, PDF_MARGIN_RIGHT);
		$this->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM - 8);
		$this->SetFillColor(243,243,243);
	}

	public function header() {
		$border = false;
		$rpt = ($this->type_rpt == 'C') ? 'Concentrado' : 'Detallado';

		$this->SetFont('helvetica', 'B', 9);
		$this->Cell(40,  7, 'Fecha: '.date('Y-m-d'), $border, 0);
		$this->SetFont('helvetica', 'B', 14);
		$this->Cell(100, 7, "Reporte $rpt de Facturas", $border, 0, 'C');
		$this->SetFont('helvetica', 'B', 9);
		$this->Cell(0,   7, '', $border, 1);

		$this->Cell(40, 6, '', $border, 0);
		$this->SetFont('helvetica', 'BI', 10);
		$this->Cell(100, 6, 'RFC: '.$this->rfc, $border, 0, 'C');
		$this->Cell(0,   6, '', $border, 1);
		
		$this->Ln(4);
		$this->Line($this->GetX(), $this->GetY(), 195, $this->GetY());
	}

	public function setParams($params) {
		$this->type_rpt      = $params['type_rpt'];
		$this->type_facturas = $params['type_facturas'];
		$this->fecha_inicial = $params['fecha_inicial'];
		$this->fecha_final   = $params['fecha_final'];

		$modConf = new Configuracion();
		$config = $modConf->getConfiguracion();
		$this->rfc = $config->rfc;
	}

	public function printRpt() {
		if ($this->type_rpt == 'C') {
			$this->printConcentrado();
		} else if ($this->type_rpt == 'D') {
			$this->printDetallado();
		}
	}


	private function printConcentrado() {
		$data = $this->getDataConcentrado();
		$tipo = '@';

		$totales_emitidas = 0;
		$totales_recibidas = 0;

		foreach ($data as $key => $item) {
			if ($tipo != $item->tipo) {
				if ($tipo != '@') $this->Ln(10);

				$tipo = $item->tipo;
				$this->SetFont('helvetica', 'B', 12);

				$txt = ($tipo == 'E') ? 'EMITIDAS' : 'RECIBIDAS';
				$this->Cell(0, 0, 'FACTURAS '. $txt, false, 1, 'C');
				$this->Ln(1);
				$fill = false;
				$border = 'B';

				$this->SetFont('helvetica', 'B', 8);
				$this->Cell(30, 0, 'RFC', $border, 0, 'C', $fill);
				$this->Cell(90, 0, 'Razón Social', $border, 0, '', $fill);
				$this->Cell(20, 0, 'Subtotal', $border, 0, 'C', $fill);
				$this->Cell(20, 0, 'IVA', $border, 0, 'C', $fill);
				$this->Cell(20, 0, 'Total', $border, 1, 'C', $fill);
			}

			$border = false;
			$this->SetFont('helvetica', '', 9);

			$this->Cell(30, 0, $item->rfc, $border, 0, '', $fill);
			$this->Cell(90, 0, substr($item->nombre, 0, 50), $border, 0, '', $fill, '', 1);
			$this->Cell(20, 0, '$ '.number_format($item->subtotal, 2), $border, 0, 'R', $fill);
			$this->Cell(20, 0, '$ '.number_format($item->iva, 2), $border, 0, 'R', $fill);
			$this->Cell(20, 0, '$ '.number_format($item->total, 2), $border, 1, 'R', $fill);

			if ($tipo == 'E') {
				$totales_emitidas += $item->total;
			}
			if ($tipo == 'R') {
				$totales_recibidas += $item->total;
			}

			$fill = !$fill;
		}

		$this->Ln(10);
		$border = false;
		$this->SetFont('helvetica', 'B', 10);

		$this->Cell(38, 0, 'Facturas Emitidas: ', $border, 0);
		$this->Cell(25, 0, '$'.number_format($totales_emitidas, 2), $border, 0, 'R');
		$this->Cell(0,  0, '', $border, 1);

		$this->Cell(38, 0, 'Facturas Recibidas: ', $border, 0);
		$this->Cell(25, 0, '$'.number_format($totales_recibidas, 2), $border, 0, 'R');
		$this->Cell(0,  0, '', $border, 1);
	}

	private function getDataConcentrado() {
		$CI =& get_instance();

		$CI->db->select('facturas.tipo, facturas.rfc, SUM(facturas.total) AS total, SUM(facturas.subtotal) AS subtotal, SUM(facturas.iva) AS iva, 
			             facturas.cliente_id, clientes.nombre');
		$CI->db->from('facturas');
		$CI->db->join('clientes', "clientes.id = facturas.cliente_id", 'LEFT');
		$CI->db->order_by('facturas.tipo', 'ASC');
		$CI->db->order_by('clientes.nombre', 'ASC');
		$CI->db->group_by('facturas.tipo');
		$CI->db->group_by('facturas.cliente_id');
		$CI->db->where('fecha >= ', $this->fecha_inicial . ' 00:00:00');
		$CI->db->where('fecha <= ', $this->fecha_final . ' 23:59:59');
		$CI->db->where('activo', 1);

		if ($this->type_facturas) {
			$CI->db->where('tipo', $this->type_facturas);
		}

		$query = $CI->db->get();

		return $query->result();
	}

	private function printDetallado() {
		$data = $this->getDataDetallado();
		$cliente = '@';
		$tipo = '@';

		$totales_emitidas = 0;
		$totales_recibidas = 0;

		foreach ($data as $key => $item) {
			if ($tipo != $item->tipo) {
				if ($tipo != '@') $this->Ln(10);

				$tipo = $item->tipo;
				$this->SetFont('helvetica', 'B', 12);

				$txt = ($tipo == 'E') ? 'EMITIDAS' : 'RECIBIDAS';
				$this->Cell(0, 0, 'FACTURAS '. $txt, false, 1, 'C');
				$cliente = '@';
			}

			if ($cliente != $item->id_cliente) {
				$this->Ln(3);
				$cliente = $item->id_cliente;
				$this->SetFont('helvetica', '', 10);

				$this->Cell(0, 0, $item->rfc.' - '.$item->nombre, 'B', 1, '', false);

				// headers
				$this->SetFont('helvetica', 'B', 8);
				$this->Cell(10, 0, '', false, 0);
				$this->Cell(20, 0, 'Fecha', false, 0, 'C');
				$this->Cell(35, 0, ' Serie/Folio', false, 0);
				$this->Cell(20, 0, 'Subtotal ', false, 0, 'R');
				$this->Cell(20, 0, 'IVA ', false, 0, 'R');
				$this->Cell(20, 0, 'Total ', false, 1, 'R');

				$fill = false;
			}

			$border = false;
			$this->SetFont('helvetica', '', 9);

			$this->Cell(10, 0, '', $border, 0);
			$this->Cell(20, 0, substr($item->fecha, 0, 10), $border, 0, 'C', $fill);
			$this->Cell(35, 0, $item->serie.' '.$item->folio, $border, 0, '', $fill);
			$this->Cell(20, 0, '$ '.number_format($item->subtotal, 2), $border, 0, 'R', $fill);
			$this->Cell(20, 0, '$ '.number_format($item->iva, 2), $border, 0, 'R', $fill);
			$this->Cell(20, 0, '$ '.number_format($item->total, 2), $border, 1, 'R', $fill);

			if ($tipo == 'E') {
				$totales_emitidas += $item->total;
			}
			if ($tipo == 'R') {
				$totales_recibidas += $item->total;
			}

			$fill = !$fill;
		}

		$this->Ln(10);
		$border = false;
		$this->SetFont('helvetica', 'B', 10);

		$this->Cell(38, 0, 'Facturas Emitidas: ', $border, 0);
		$this->Cell(25, 0, '$'.number_format($totales_emitidas, 2), $border, 0, 'R');
		$this->Cell(0,  0, '', $border, 1);

		$this->Cell(38, 0, 'Facturas Recibidas: ', $border, 0);
		$this->Cell(25, 0, '$'.number_format($totales_recibidas, 2), $border, 0, 'R');
		$this->Cell(0,  0, '', $border, 1);
	}

	private function getDataDetallado() {
		$CI =& get_instance();

		$CI->db->select('facturas.tipo, facturas.rfc, facturas.fecha, facturas.subtotal, facturas.iva, facturas.total, 
			             facturas.serie, facturas.folio, clientes.nombre, clientes.id AS id_cliente');
		$CI->db->from('facturas');
		$CI->db->join('clientes', "clientes.id = facturas.cliente_id", 'LEFT');
		$CI->db->order_by('facturas.tipo', 'ASC');
		$CI->db->order_by('clientes.nombre', 'ASC');
		$CI->db->order_by('facturas.fecha', 'ASC');
		$CI->db->where('fecha >= ', $this->fecha_inicial . ' 00:00:00');
		$CI->db->where('fecha <= ', $this->fecha_final . ' 23:59:59');
		$CI->db->where('activo', 1);

		if ($this->type_facturas) {
			$CI->db->where('tipo', $this->type_facturas);
		}

		$query = $CI->db->get();

		return $query->result();
	}
	
}

?>