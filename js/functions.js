function block(mensaje) {
	var msg = mensaje || 'Espere un momento';
	$.blockUI({ message: '<div class="clsMsgBlock">'+ msg +'</div>' });
}

function unblock() {
	$.unblockUI();
}

function notify(msg, type) {
	// level
	switch (type || 'N') {
		case 'S': type = 'success'; break;
		case 'N': type = 'notice';  break;
		case 'W': type = 'warning'; break;
		case 'E': type = 'error';   break;
	}

	$().toastmessage('showToast', {
	    text     : msg || 'Notificación',
	    sticky   : false,
	    type     : type,
	    position : 'bottom-right'
	});
}

function fillSelectFromAjax(opts) {
  var element = $('select[name="'+ opts.elName +'"]');
  var val = opts.fieldValue || 'id';
  var name = opts.fieldName;
  var data = opts.data || null;
  var defOption = opts.defOption || '';

  $.ajax({
    url: opts.url,
    type: 'POST',
    dataType: 'JSON',
    data: {
      sort: {
        column: name,
        direction: 'ASC'
      }
    },
    success: function (response) {
      var data = response.data;

      $(element).html('<option value="">'+ defOption +'</option>');

      _.each(data, function (rec) {
        $(element).append('<option value="'+ rec[val] +'">'+ rec[name] +'</option>');
      })
    }
  });
}