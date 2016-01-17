<?php

include('base_model.php');

class Producto extends BaseModel {
	
	protected $table_name    = 'productos';
	protected $list_fields   = array('id','nombre','clave','costo','precio','familia_id','grupo_id','activo',
		                             'familias.nombre AS nombre_familia', 'grupos.nombre AS nombre_grupo');
	
	protected $search_fields = array('nombre','clave','familias.nombre','grupos.nombre');
	protected $new_defaults  = array('nombre' => '', 'activo' => 1);
	protected $save_fields   = array('nombre','clave','costo','precio','codigo_barras','tiene_iva','familia_id','grupo_id','activo','comentarios');
	// protected $edit_fields   = array('nombre','descripcion','activo');
	// protected $avoid_delete  = true;

	protected $join_tables = array (
	                            array ('table' => 'familias', 'fk' => 'familia_id', 'type' => 'left'),
	                            array ('table' => 'grupos',   'fk' => 'grupo_id',   'type' => 'left'),
	                         );
}

?>