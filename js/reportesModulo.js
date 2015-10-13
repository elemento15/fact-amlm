var reportesModulo = function () {

	this.init = function () {
		$('.form-seccion').show();
		this.configToolBarEvents();
		this.configComponents();
	},
	
	this.configToolBarEvents = function () {
		var me = this;

		$('.cls-toolbar .btnConcentrado').click(function () {
			me.printRpt('C');
		});

		$('.cls-toolbar .btnDetallado').click(function () {
			me.printRpt('D');
		});
	}

	this.configComponents = function () {
		var me = this;
		
		$('#dateFecIni').datetimepicker({
			format: 'DD/MMM/YYYY'
		});
		
		$('#dateFecFin').datetimepicker({
			format: 'DD/MMM/YYYY'
		});
	}

	this.printRpt = function (opt) {
		var params = '';
		var fec_ini = moment($('#dateFecIni').data('date'), 'DD/MMM/YYYY').format('YYYY-MM-DD');
		var fec_fin = moment($('#dateFecFin').data('date'), 'DD/MMM/YYYY').format('YYYY-MM-DD');
		var type = $('[name="tipo_factura"]').val();
		
		console.log(fec_ini);
		console.log(fec_fin);

		if (fec_ini == 'Invalid date') {
			notify('Fecha inicial invalida', 'W');
			return false;
		}

		if (fec_fin == 'Invalid date') {
			notify('Fecha final invalida', 'W');
			return false;
		}

		params += '?rpt=' + opt +'&type=' + type + '&fec_ini=' + fec_ini + '&fec_fin=' + fec_fin;
		window.open('app.php/facturas/print_report' + params, '_blank');
	}

}
