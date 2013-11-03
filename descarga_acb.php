<?php
try {
	$server = "mongodb://moviles:moviles@localhost:27017/moviles";
	$connection = new MongoClient($server);
	$database = $connection -> selectDB('moviles');
	$collection = $database -> selectCollection('equipos');
	$collection -> remove();
	$url = "http://www.acb.com/";
	$content = file_get_contents($url . "menuplantillas.php");
	$content = html_entity_decode($content);
	$txt = null;
	preg_match("/<table align=\"center\" class=\"menuclubs\"(.*)<\/table>/siU", $content, $matches);
	$conte_equipo_foto = file_get_contents($url . "left.html?1382955000");
	$conte_equipo_foto = html_entity_decode($conte_equipo_foto);
	//print_r($matches);
	if ($matches) {
		//Get categories links
		preg_match_all("/a>(.*)<a href=\"(.*)\">(.*)<\/a>/siU", $matches[1], $links, PREG_SET_ORDER);
		foreach ($links as $link) {
			$typeArray[$link[3]] = $link[2];
		}
		//Get foto equipo
		//print($conte_equipo_foto);
		preg_match_all("/<li>(.*)href=\"http:\/\/www.acb.com\/club.php\?id\=(.*)\"(.*)src=\"(.*)\"(.*) \/>/siU", $conte_equipo_foto, $foto_equipo, PREG_SET_ORDER);
		
		foreach ($foto_equipo as $link_foto) {
			$typeArrayFoto[$link_foto[2]] = $link_foto[4];
		}
		//print_r($typeArrayFoto);
	}
	foreach ($typeArray as $key => $value) {
		$txt .= "<h1>" . $key . "</h1>";
		$content = file_get_contents($url . $value);
		$content2 = html_entity_decode($content);
		preg_match("/www.acb.com\/plantilla.php\?cod_equipo\=(.*)&cod_competicion=LACB&cod_edicion\=58/siU",$url . $value,$cod);
		$codi=$cod[1];
		preg_match_all("/<tr onmouseover\=\"document\[\'foto\'\]\.src\=\'(.*)\'\"(.*)naranja\">(.*)<\/td>(.*)href=\"(.*)\">(.*)<\/a>(.*)blanco\">(.*)<(.*)gris\">(.*)<(.*)blanco\">(.*)<(.*)gris\">(.*)<(.*)blanco\">(.*)<(.*)gris\">(.*)<\/td>/siU", $content, $links2, PREG_SET_ORDER);
		unset($equipo);
		$equipo['equipo'] = htmlentities($key);
		$equipo['escudo'] = $typeArrayFoto[$codi];
		$jugadores = array();
		foreach ($links2 as $link2) {
			unset($jugador);

			$jugador['numero'] = $link2[3];
			$jugador['nombre'] = $link2[6];
			$jugador['foto'] = $link2[1];
			$jugador['posicion'] = $link2[8];
			$jugador['nacionalidad'] = $link2[10];
			$jugador['licencia'] = $link2[12];
			$jugador['altura'] = $link2[14];
			$jugador['edad'] = $link2[16];
			$jugador['temporada'] = $link2[18];

			array_push($jugadores, $jugador);

		}
		preg_match_all("/<tr onmouseover\=\"document\[\'foto\'\]\.src\=\'(.*)entrenadores(.*)\'\"(.*)\.src\=\'(.*)\'\"(.*)href=\"(.*)\">(.*)<\/a><\/td>(.*)<td class=\"blanco\" width=\"30\">(.*)<(.*)gris\" width=\"45\">(.*)<(.*)blanco\" width=\"30\">(.*)<(.*)gris\" width=\"30\">(.*)<\/td>/siU", $content, $links3, PREG_SET_ORDER);
		$entrenadores = array();
		foreach ($links3 as $link3) {
			unset($entrenador);

			$entrenador['numero'] = $link3[9];
			$entrenador['nombre'] = $link3[7];
			$entrenador['foto'] = $link3[4];

			array_push($entrenadores, $entrenador);

		}
		//print_r($entrenadores);
		//////////////////
		$equipo['jugadores'] = $jugadores;
		$equipo['entrenadores'] = $entrenadores;
		$collection -> insert($equipo);

	}
} catch (MongoConnectionException $e) {
	die("No se ha podido conectar a la base de datos " . $e -> getMessage());
} catch (MongoException $e) {
	die('No se han podido insertar los datos ' . $e -> getMessage());
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="style.css"/>
<title>Creador de Posts</title>
</head>
<body>
<?php print($txt); ?>

</body>
</html> 