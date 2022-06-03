$(document).ready(function () {

  function initModule(modulo) {
    switch (modulo) {
      case 'inicioModulo'        : var obj = new inicioModulo();
                                   obj.init();
                                   break;
      case 'configuracionModulo' : var obj = new configuracionModulo();
                                   obj.init();
                                   break;
      case 'reportesModulo'      : var obj = new reportesModulo();
                                   obj.init();
                                   break;
      case 'clientesModulo'      : clientesModulo.init();
                                   break;
      case 'facturasModulo'      : facturasModulo.init();
                                   break;
      case 'pagosModulo'         : pagosModulo.init();
                                   break;
    }
  }

  function closeSession() {
    if (confirm('Cerrar Sesion')) {
      $.ajax({
        url: 'app.php/acceso/logout',
        type: 'POST',
        dataType: 'JSON',
        success: function (response) {
          window.location.reload();
        },
        error: function () {
          alert('Error en el servidor al cerrar sesión');
        }
      });
    }
  }

  $('#navbar li.opt-navbar').click(function (evt) {
    var tpl = $(this).attr('tpl');
    var modulo = $(this).attr('modulo');

    if ($(this).attr('opt') == 'close-session') {
      closeSession();
      return false;
    }

    // si es la opcion activa, omite
    if ($(this).hasClass('active')) {
      return false;
    }

    $('#navbar li.opt-navbar').removeClass('active');
    $(this).addClass('active');

    if (tpl) {
      $('div.main').load('templates/'+ tpl +'.html', {}, function (response, textStatus) {
        if (textStatus == 'success') {
          $.getScript('js/'+ modulo +'.js', function () {
            initModule(modulo);
          });
        } else {
          $('div.main').html(response);
        }
      });
    }
  });

  // configuraciones globales de DataTable
  $.fn.dataTable.ext.errMode = 'none'; // alert, throw, none

  // serialize forms
  $.fn.serializeObject = function() {
    var o = {};
    // var a = this.serializeArray();
    $(this).find('input[type="hidden"], input[type="text"], input[type="password"], input[type="checkbox"]:checked, input[type="radio"]:checked, select, textarea').each(function() {
      if ($(this).attr('type') == 'hidden') { //if checkbox is checked do not take the hidden field
        var $parent = $(this).parent();
        var $chb = $parent.find('input[type="checkbox"][name="' + this.name.replace(/\[/g, '\[').replace(/\]/g, '\]') + '"]');
        if ($chb != null) {
          if ($chb.prop('checked')) return;
        }
      }
      if (this.name === null || this.name === undefined || this.name === '') return;
      var elemValue = null;
      if ($(this).is('select')) elemValue = $(this).find('option:selected').val();
      else elemValue = this.value;
      if (o[this.name] !== undefined) {
        if (!o[this.name].push) {
          o[this.name] = [o[this.name]];
        }
        o[this.name].push(elemValue || '');
      } else {
        o[this.name] = elemValue || '';
      }
    });
    return o;
  }

  // show INICIO when logged in
  $('#navbar li.opt-navbar').first().trigger('click');

});