<?php
try {
	$server = "mongodb://moviles:moviles@localhost:27017/moviles";
	$connection = new MongoClient($server);
	$database = $connection -> selectDB('moviles');
	$collection = $database -> selectCollection('partidos');
	$partido = array();
	$evento = array();
	$eventos = array();
	$criterio = array();
	$campos = array();
	$array = array();
	if (isset($_POST['partido'])) {
		$partido = json_decode($_POST['partido'], true);
		$hora = new MongoDate();
		$partido['date'] = $hora -> sec;
		$partido['eventos'] = $eventos;
		$collection -> insert($partido, array('safe' => True));
		$cursor = $collection -> find(array('_id' => new MongoId($partido['_id'])));
		$array = array();
		foreach ($cursor as $k => $row) {
			$array[] = $row;
		}
		//print_r($array);
		echo json_encode($array, JSON_PRETTY_PRINT);
	} else if (isset($_POST['evento'])) {
		//52724d78a21a9d6412000029 //$_POST['partidoId']
		$criterio = new MongoId($_POST['partidoId']);
		$criterio = array("_id" => $criterio);
		$evento = json_decode($_POST['evento'], true);
		$hora = new MongoDate();
		$evento['date'] = $hora -> sec;
		$eventos['eventos'] = $evento;
		
		$collection -> update($criterio, array('$push' => $eventos) );
		unset($criterio);
		$criterio = array('eventos.date' => $evento['date']);
		$campos = array('_id', 'local', 'visitante','eventos.equi', 'eventos.$.date','eventos.faltaPersonal');
		$campos = array('_id', 'local', 'visitante','eventos.$.*');
		$cursor = $collection -> find($criterio, $campos);

		foreach ($cursor as $k => $row) {
			$array[] = $row;
		}
		echo json_encode($array, JSON_PRETTY_PRINT);
		
	} else if (isset($_POST['qpartido'])) {
		
		$criterio = new MongoId($_POST['qpartido']);
		$criterio = array("_id" => $criterio);
		$cursor = $collection -> find($criterio);
		$array = array();
		foreach ($cursor as $k => $row) {
			$array[] = $row;
		}
		//print_r($array);
		echo json_encode($array, JSON_PRETTY_PRINT);
	}
	
	
	else {

		//$campos = array('_id', 'local', 'visitante', 'date');
		$cursor = $collection -> find($criterio, $campos);

		foreach ($cursor as $k => $row) {
			$array[] = $row;
		}
		//print_r($array);
		echo json_encode($array, JSON_PRETTY_PRINT);
	}
} catch (MongoConnectionException $e) {
	die("No se ha podido conectar a la base de datos " . $e -> getMessage());
} catch (MongoException $e) {
	die('No se han podido insertar los datos ' . $e -> getMessage());
}
?>