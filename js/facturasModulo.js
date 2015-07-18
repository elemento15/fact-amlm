var facturasModulo = new moduleGeneral ({
  gridId: 'gridFacturas',
  formId: 'formFacturas',
  url: {
    read:   'app.php/facturas/read',
    create: 'app.php/facturas/create',
    find:   'app.php/facturas/find',
    save:   'app.php/facturas/save',
    remove: 'app.php/facturas/remove'
  },
  columns: [
    { data: 'fecha' },
    { data: 'rfc' },
    { data: 'cliente' },
    { data: 'total' }
  ],
  order: {
    column: 'fecha',
    direction: 'ASC'
  }
  // addingRow: function (row, data, index) {
  //   if (!parseInt(data.activo)) {
  //     $(row).addClass('clsCancelado');
  //   }
  // }
});
