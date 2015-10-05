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
  },
  // addingRow: function (row, data, index) {
  //   if (!parseInt(data.activo)) {
  //     $(row).addClass('clsCancelado');
  //   }
  // }
  onInit: function () {
    var me = this;

    $("#cargarXML").on("click", function() {
      var file_data = $("#uploaderXML").prop("files")[0];
      var form_data = new FormData();
      form_data.append("file", file_data);

      block('Cargando XML');
      $.ajax({
        url: "app.php/facturas/load_xml",
        // dataType: 'script',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'POST',
        success: function(response) {
          var resp = JSON.parse(response);
          unblock();
          if (resp.success) {
            notify(resp.msg, 'S');
          } else {
            notify(resp.msg, 'E');
          }
          me.refresh();
        },
        error: function (response) {
          var resp = JSON.parse(response.responseText);
          unblock();
          notify(resp.msg, 'E');
        }
      });
    });

  }
});
