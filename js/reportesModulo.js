var reportesModulo = function () {

	this.init = function () {
		$('.form-seccion').show();
		this.configToolBarEvents();
		this.configComponents();
	},
	
	this.configToolBarEvents = function () {
		var me = this;

		$('.cls-toolbar .botonGenerar').click(function () {
			var fec_ini = moment($('#dateFecIni').data('date'), 'DD/MMM/YYYY').format('YYYY-MM-DD');
			var fec_fin = moment($('#dateFecFin').data('date'), 'DD/MMM/YYYY').format('YYYY-MM-DD');
			
			console.log(fec_ini);
			console.log(fec_fin);
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

}
