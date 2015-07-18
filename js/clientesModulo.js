var clientesModulo = new moduleGeneral ({
  gridId: 'gridClientes',
  formId: 'formClientes',
  url: {
    read:   'app.php/clientes/read',
    create: 'app.php/clientes/create',
    find:   'app.php/clientes/find',
    save:   'app.php/clientes/save',
    remove: 'app.php/clientes/remove'
  },
  columns: [
    { data: 'rfc' },
    { data: 'nombre' },
    { data: 'comercial' }
  ],
  order: {
    column: 'rfc',
    direction: 'ASC'
  },
  beforeSave: function () {
    var form = this.dataForm;

    if (!form.nombre || !form.rfc) {
      notify('Especifique RFC y Razon Social', 'W');

      return false;
    }

    // validar formato de rfc
    var regexrfc1 = /^[A-Z]{4}\d{6}[a-zA-Z0-9]{3}$/;
    var regexrfc2 = /^[A-Z]{3}\d{6}[a-zA-Z0-9]{3}$/;
    if(!regexrfc1.test(form.rfc) && !regexrfc2.test(form.rfc)) {
      notify('El RFC es invalido', 'W');
      return false;
    }

    // validar formato de email
    var regexemail = /\S+@\S+\.\S+/;
    if(form.email && !regexemail.test(form.email)) {
      notify('El email es invalido', 'W');
      return false;
    }

    return true;
  }
  // addingRow: function (row, data, index) {
  //   if (!parseInt(data.activo)) {
  //     $(row).addClass('clsCancelado');
  //   }
  // }
});
