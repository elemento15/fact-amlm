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
  }
  // addingRow: function (row, data, index) {
  //   if (!parseInt(data.activo)) {
  //     $(row).addClass('clsCancelado');
  //   }
  // }
});
