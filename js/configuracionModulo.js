var configuracionModulo = function () {

	this.init = function () {
		$('.form-seccion').show();
		this.load();
		this.configToolBarEvents();
	},
	this.load = function () {
		var me = this;

		block();
		$.ajax({
			url:  'app.php/configuraciones/read_one',
			type: 'POST',
			dataType: 'JSON',
			// data: { id: id },
			success: function (response) {
				if (response.success) {
					if (response.data) {
						var data = response.data;
						$('#formConfiguraciones [name="id"]').val(data.id);
						$('#formConfiguraciones [name="rfc"]').val(data.rfc);
						$('#formConfiguraciones [name="nombre"]').val(data.nombre);
					}
				} else {
					notify(response.msg, 'E');
				}
				unblock();
			}
		});
	},
	this.save = function (rec) {
		var me = this;
		
		block();
		$.ajax({
			url:  'app.php/configuraciones/save',
			type: 'POST',
			dataType: 'JSON',
			data: { data: rec },
			success: function (response) {
				if (response.success) {
					$('input[name="id"]').val(response.id);
					unblock();
					notify('El registro se guardo exitosamente. Id: ' + response.id, 'S');
				} else {
					unblock();
					notify(response.msg, 'E');
				}
			},
			error: function (response) {
				var resp = JSON.parse(response.responseText);
				unblock();
				notify(resp.msg, 'E');
			}
		});
	},
	this.configToolBarEvents = function () {
		var me = this;

		$('.cls-toolbar .botonGuardar').click(function () {
			var data = $('#formConfiguraciones').serializeObject();
			me.save(data);
		});
	}

}
