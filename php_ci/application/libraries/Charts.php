<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Charts {

	public function __construct() {
		//
	}

	public function rpt12Months() {
		return [
			'emitidas' => $this->getData12Months('E'), 
			'recibidas' => $this->getData12Months('R'),
			'pagos'  => $this->getData12MonthsPayments(),
		];
	}


	private function getData12Months($type) {
		$CI =& get_instance();

		$CI->db->select("date_format(fecha, '%Y-%m') AS anio_mes, SUM(total) AS total");
		$CI->db->from('facturas');
		$CI->db->where("fecha >= CONCAT(SUBDATE(date_format(NOW(), '%Y-%m-01'), INTERVAL 11 MONTH), ' 00:00:00') ");
		$CI->db->where('fecha <= NOW()');
		$CI->db->where('activo', 1);
		$CI->db->where('tipo', $type);
		$CI->db->order_by('anio_mes', 'ASC');
		$CI->db->group_by('anio_mes');

		$query = $CI->db->get();
		return $this->mapData12Months($query->result());
	}

	private function getData12MonthsPayments() {
		$CI =& get_instance();

		$CI->db->select("CONCAT(anio,'-',IF(mes < 10, CONCAT('0',mes),mes)) AS anio_mes, SUM(total) AS total");
		$CI->db->from('pagos');
		$CI->db->having("anio_mes >= DATE_FORMAT(SUBDATE(DATE_FORMAT(NOW(), '%Y-%m-01'), INTERVAL 11 MONTH), '%Y-%m')");
		$CI->db->having("anio_mes <= DATE_FORMAT(NOW(), '%Y-%m')");
		$CI->db->order_by('anio_mes', 'ASC');
		$CI->db->group_by('anio_mes');

		$query = $CI->db->get();
		return $this->mapData12Months($query->result());
	}

	private function mapData12Months($data) {
		$parsed = [];
		$months = [];
		$date = date_create(date('Y-m-').'01');

		// create last 12 months list
		$months[] = date_format($date, 'Y-m');
		for ($i=1; $i<=11 ; $i++) { 
			date_sub($date, date_interval_create_from_date_string('1 months'));
			$months[] = date_format($date, 'Y-m');
		}
		sort($months);
		
		// parse data to each month
		foreach ($months as $month) {
			$flag = false;
			foreach ($data as $item) {
				if ($item->anio_mes == $month) {
					$flag = true;
					$parsed[] = [
						'date'  => $item->anio_mes,
						'label' => $this->getLabelDate($item->anio_mes),
						'total' => $item->total
					];
				}
			}
			if (!$flag) {
				$parsed[] = [
					'date'  => $month,
					'label' => $this->getLabelDate($month),
					'total' => 0
				];
			}
		}

		return $parsed;
	}

	private function getLabelDate($date) {
		$arr = explode('-', $date);

		switch ($arr[1]) {
			case '01' : $text = 'Ene'; break;
			case '02' : $text = 'Feb'; break;
			case '03' : $text = 'Mar'; break;
			case '04' : $text = 'Abr'; break;
			case '05' : $text = 'May'; break;
			case '06' : $text = 'Jun'; break;
			case '07' : $text = 'Jul'; break;
			case '08' : $text = 'Ago'; break;
			case '09' : $text = 'Sep'; break;
			case '10' : $text = 'Oct'; break;
			case '11' : $text = 'Nov'; break;
			case '12' : $text = 'Dic'; break;
		}

		return $text . ' '. substr($arr[0], 2, 2);
	}
}

?>