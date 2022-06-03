var pagosModulo = new moduleGeneral ({
  gridId: 'gridPagos',
  formId: 'formPagos',
  url: {
    read:   'app.php/pagos/read',
    create: 'app.php/pagos/create',
    find:   'app.php/pagos/find',
    save:   'app.php/pagos/save',
    remove: 'app.php/pagos/remove'
  },
  columns: [
    { data: 'anio' },
    { data: 'mes' },
    { data: 'total' },
    { data: 'concepto' },
  ],
  columnDefs: [
    {
      "render": function(data, type, row) {
        return data;
      }, "targets": 0
    },{
      "render": function(data, type, row) {
        var meses = ['Enero','Febrero','Marzo','Abril','Mayo',
                     'Junio','Julio','Agosto','Septiembre',
                     'Octubre','Noviembre','Diciembre'];
        return meses[data - 1];
      }, "targets": 1
    },{
      "render": function(data, type, row) {
        return '<div class="pull-right">$'+ formatNumber(parseFloat(data).toFixed(2)) +'</div>';
      }, "targets": 2
    },{
      "render": function(data, type, row) {
        return data;
      }, "targets": 3
    }
  ],
  order: {
    column: 'anio',
    direction: 'DESC'
  },
  beforeSave: function () {
    var form = this.dataForm;

    if (!form.total || isNaN(form.total) || form.total <= 0 || form.total > 100000) {
      notify('Total inválido', 'W');
      return false;
    }

    if (!form.anio) {
      notify('Seleccione al año', 'W');
      return false;
    } else if (form.anio < 2000 || form.anio > 2050) {
      notify('Año inválido', 'W');
      return false;
    }

    if (!form.mes || form.mes < 1 || form.mes > 12) {
      notify('Seleccione al mes', 'W');
      return false;
    }

    return true;
  }
});
