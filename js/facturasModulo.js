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
    { data: 'folio' },
    { data: 'rfc'   },
    { data: 'total' },
    { data: 'tipo'  }
  ],
  columnDefs: [
    {
      "render": function (data, type, row) {
        return data.substr(0, 10);
      }, "targets": 0
    },{
      "render": function (data, type, row) {
        var html = '<div class="cls-texto-secundario">'+ (row.serie || '') +' '+ (row.folio || '') +'</div>';
        return html;
      }, "targets": 1
    },{
      "render": function (data, type, row) {
        var html = '<div class="cls-rfc-cliente">'+ data +'</div>';
        html    += '<div class="cls-texto-secundario">'+ row.nombre_cliente +'</div>';
        return html;
      }, "targets": 2
    },{
      "render": function (data, type, row) {
        return '<div class="pull-right">$'+ formatNumber(parseFloat(data).toFixed(2)) +'</div>';
      }, "targets": 3
    },{
      "render": function (data, type, row) {
        return (data == 'E') ? 'Emitida' : 'Recibida';
      }, "targets": 4
    }
  ],
  order: {
    column: 'fecha',
    direction: 'ASC'
  },
  disableEditOnDblClick: true,
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

    $("#filtroTipoFactura").change(function (evt) {
      var tipo = $(this).val();
      me.filter = [];
      if (tipo) {
        me.filter.push({ field: 'tipo', value: tipo });
      }

      me.refresh();
    });
  }
});
