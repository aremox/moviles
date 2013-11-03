<?php
$equi = array();
$campos = array();
if (isset( $_GET['id'])){
	$equi = array('_id' => new MongoId($_GET['id']));
	if(isset( $_GET['numero'])){
		$equi = array('jugadores.numero' => $_GET['numero'],'_id' => new MongoId($_GET['id']));
		$campos = array('equipo','jugadores.$.numero','jugadores.nombre','jugadores.foto','jugadores.posicion','jugadores.nacionalidad','jugadores.licencia','jugadores.altura','jugadores.edad','jugadores.temporada');	
	}
}

else if (isset($_GET['equipo'])){
$vari = $_GET['equipo'];
 $equi = array('equipo' => $vari) ; //html_entity_decode($_GET['equipo'])
 if(isset( $_GET['numero'])){
		$equi = array('jugadores.numero' => $_GET['numero'],'equipo' => $vari);
		$campos = array('equipo','jugadores.$.numero','jugadores.nombre','jugadores.foto','jugadores.posicion','jugadores.nacionalidad','jugadores.licencia','jugadores.altura','jugadores.edad','jugadores.temporada');	
	}
 }else if(isset($_GET['nombre'])){
 	$vari = htmlentities($_GET['nombre']);
	print($vari);
 	$equi = array('jugadores.nombre' => $vari) ; //html_entity_decode($_GET['equipo'])
	$campos = array('equipo','jugadores.$.numero','jugadores.nombre','jugadores.foto','jugadores.posicion','jugadores.nacionalidad','jugadores.licencia','jugadores.altura','jugadores.edad','jugadores.temporada');
 }

try {
    $server = "mongodb://moviles:moviles@localhost:27017/moviles";
	$connection = new MongoClient($server);
    $database = $connection->selectDB('moviles');
    $collection = $database->selectCollection('equipos');
} catch (MongoConnectionException $e) {
    die("Fallo en la conexión a la base de datos " . $e->getMessage());
}
$cursor = $collection->find($equi, $campos);
$cursor->sort(array('equipo' => 1));

    
    foreach($cursor as $k => $row){
    $array[]=$row;
}
	//print_r($array);
	echo json_encode($array, JSON_PRETTY_PRINT);
