var moduleGeneral = function (opts) {
  this.order = opts.order;

  this.init = function () {
    var me = this;
    block();

    this.grid = $('#'+ opts.gridId).DataTable({
      serverSide: true,
      ajax: {
        url: opts.url.read,
        type: 'POST',
        data: {
          sort: me.order
        },
        error: function (resp) {
          notify(resp.responseJSON.msg, 'E');
          unblock();
        }
      },
      createdRow: opts.addingRow,
      columns: opts.columns,
      processing: false,
      lengthChange: false,
      pageLength: opts.pageLen || 10,
      searching: true,
      ordering: false,
      language: {
        processing: 'Procesando...',
        info: '_START_ al _END_ de _TOTAL_ registros',
        infoFiltered: '',
        infoEmpty: 'No hay registros para mostrar',
        zeroRecords: 'No hay registros para mostrar',
        loadingRecords: 'Cargando...',
        sSearch: 'Buscar:',
        paginate: {
          next: 'Siguiente',
          previous: 'Anterior'
        }
      }
    });

    this.grid.on('preXhr.dt', function ( e, settings, data ) {
        block();
        data.sort = me.order
    });

    this.grid.on('xhr.dt', function () {
      unblock();
    });

    this.configTableEvents();
    this.configToolBarEvents();

    opts.onInit && opts.onInit.call(me);
  },
  this.refresh = function () {
    // draw table
    this.grid.draw();
  },
  this.add = function () {
    this.load(0, opts.url.create);
  },
  this.edit = function (rec) {
    this.load(rec.id, opts.url.find);
  },
  this.load = function (id, url) {
    var me = this;
    
    $('#' + opts.formId)[0].reset();

    if (opts.beforeLoad && !opts.beforeLoad.call(me)) return;

    block();
    $.ajax({
      url:  url,
      type: 'POST',
      dataType: 'JSON',
      data: { id: id },
      success: function (response) {
        if (response.success) {
          me.loadForm(response.data);
          opts.afterLoad && opts.afterLoad.call(me);
          me.showForm();
        } else {
          notify(response.msg, 'E');
        }
        unblock();
      },
      error: function (response) {
        var resp = JSON.parse(response.responseText);
        unblock();
        notify(resp.msg, 'E');
      }
    });
  },
  this.delete = function (rec) {
    var me = this;

    if (!confirm('¿Deseas eliminar el registro seleccionado?')) {
      return false;
    }

    block();
    $.ajax({
      url:  opts.url.remove,
      type: 'POST',
      dataType: 'JSON',
      data: { id: rec.id },
      success: function (response) {
        if (response.success) {
          notify('El registro fue eliminado exitosamente.', 'S');
          unblock();
          me.refresh();
        } else {
          notify(response.msg, 'E');
          unblock();
        }
      },
      error: function (response) {
        var resp = JSON.parse(response.responseText);
        unblock();
        notify(resp.msg, 'E');
      }
    });
  },
  this.save = function (rec) {
    var me = this;
    this.dataForm = rec;

    if (opts.beforeSave && !opts.beforeSave.call(me)) return;

    block();
    $.ajax({
      url:  opts.url.save,
      type: 'POST',
      dataType: 'JSON',
      data: { data: rec },
      success: function (response) {
        if (response.success) {
          $('input[name="id"]').val(response.id);
          me.refresh();
          unblock();
          notify('El registro se guardo exitosamente. Id: ' + response.id, 'S');
          opts.afterSave && opts.afterSave.call(me);
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
  this.close = function () {
    // TODO: Si cambiaron los datos, confirmar cerrar
    this.showGrid();
  },
  this.sort = function (colName, direction) {
    this.order = {
      column: colName,
      direction: direction
    }
    this.refresh();
  },
  this.configTableEvents = function () {
    var me = this;

    // clicking on a table's record
    $('#'+ opts.gridId +' tbody').on('click', 'tr', function () {
      $('#'+ opts.gridId +' tbody tr').removeClass('selected');
      $(this).addClass('selected');
    });

    // double-clicking on a table's record
    $('#'+ opts.gridId +' tbody').on('dblclick', 'tr', function (evt) {
      $('#'+ opts.gridId +' tbody tr').removeClass('selected');
      $(this).addClass('selected');
      var record = me.getSelectedRecord();
      me.edit(record);
    });

    // searching when enter in textbox
    $('.grid-seccion').on('keyup', '.textoBuscar', function (evt) {
      var texto = $(this).val();
      if (evt.keyCode == 13) {
        me.grid.search(texto).draw();
      }
    });

    // limpiar la busqueda
    $('span.clear-search').on('click', function (evt) {
      $(this).parent().parent().find('input.textoBuscar').val('').focus();
      me.grid.search('').draw();
    });

    // icons for ordering column headers
    $('#'+ opts.gridId +' th').on('click', '.cls-orderable', function (evt) {
      var jqObj = $(this).find('span.glyphicon');
      var column;
      var direction;

      if ( !jqObj.length ) {
        $('#'+ opts.gridId +' th span.glyphicon').remove();
        $(this).prepend('<span class="glyphicon glyphicon-menu-down" sortDirection="ASC"></span>');
        direction = 'ASC';
      } else {
        var sort = jqObj.attr('sortDirection');
        if (sort == 'ASC') {
          jqObj.attr('sortDirection', 'DESC');
          jqObj.removeClass('glyphicon-menu-down');
          jqObj.addClass('glyphicon-menu-up');
          direction = 'DESC';
        } else {
          jqObj.attr('sortDirection', 'ASC');
          jqObj.removeClass('glyphicon-menu-up');
          jqObj.addClass('glyphicon-menu-down');
          direction = 'ASC';
        }
      }
      column = $(this).attr('nameColumn');
      me.sort(column, direction);
    });
  },
  this.configToolBarEvents = function () {
    var me = this;
    
    $('.cls-toolbar .botonNuevo').click(function () {
      me.add();
    });

    $('.cls-toolbar .botonEditar').click(function () {
      var record = me.getSelectedRecord();
      if (record) {
        me.edit(record);
      }
    });

    $('.cls-toolbar .botonEliminar').click(function () {
      var record = me.getSelectedRecord();
      if (record) {
        me.delete(record);
      }
    });

    $('.cls-toolbar .botonGuardar').click(function () {
      var data = $('#'+ opts.formId).serializeObject();
      me.save(data);
    });

    $('.cls-toolbar .botonCerrar').click(function () {
      me.close();
    });
  },
  this.showForm = function () {
    $('.grid-seccion').hide();
    $('.form-seccion').show();
  },
  this.showGrid = function () {
    $('.form-seccion').hide();
    $('.grid-seccion').show();
  },
  this.getSelectedRecord = function () {
    var sel = $('#'+ opts.gridId +' tbody tr.selected');
    return this.grid.row(sel).data();
  },
  this.loadForm = function (data) {
    _.each(data, function (value, key) {
      var item = $('#'+ opts.formId +' [name="' + key + '"]');
      if (!item.length) return;
      
      var tag  = item[0].tagName;
      var type = item[0].type;

      if (tag == 'INPUT') {
        if (type == 'text' || type == 'hidden') {
          item.val(value);
        }

        if (type == 'checkbox' ) {
          if (parseInt(value)) {
            item.prop('checked', true);
          } else {
            item.prop('checked', false);
          }
        }
      }

      if (tag == 'SELECT') {
        item.val(value);
      }

      if (tag == 'TEXTAREA') {
        item.text(value);
      }
    });
  }
}