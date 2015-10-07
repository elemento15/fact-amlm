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
			},
			error: function (resp) {
				notify(resp.responseJSON.msg, 'E');
				unblock();
			}
		});
	},
	this.save = function (rec) {
		var me = this;
		
		// validar formato de rfc
		var regexrfc1 = /^[A-Z]{4}\d{6}[a-zA-Z0-9]{3}$/;
		var regexrfc2 = /^[A-Z]{3}\d{6}[a-zA-Z0-9]{3}$/;
		if(!regexrfc1.test(rec.rfc) && !regexrfc2.test(rec.rfc)) {
			notify('El RFC es invalido', 'W');
			return false;
		}

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
