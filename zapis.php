<?php
	/*
	* Połącznie z bd, rysowanie mapy
	* Okno dialogowe
	* AJAX!
	* Teraz jest dobrze, punkt zapisuje się raz.
	* Zapisywanie punktu dopiero po kliknięciu "zgłoś".
	* Wszystkie dane daje się zapisać.
	* Problem z ukł współrzędnych rozwiązany.
	* OL3
	* Warstwa wektora z shp działa.
	* Ikony zdarzeń - podział na warstwy.
	*/
	
	/*
	* zapis.php
	* @author Alicja Puacz
	* Połącznie z bd i zapytanie do bd.
	*/
	
	

	$wspX=$_GET["x"];
	$wspY=$_GET["y"];
	$kategory2=$_GET["kategory1"];
	$opis2=$_GET["opis1"];
	$nazwa2=$_GET["nazwa1"];
	$mail2=$_GET["mail1"];

	mysql_connect('sql5.progreso.pl', 'szymonp9_alicja', '!@WSc$#1123wdfeW');

	$db_selected = mysql_select_db('szymonp9_alicja');

	echo 'Connected successfully';
	
		if ($kategory2 == "Włamanie"){
			mysql_query("INSERT INTO `WLAMANIE` (`SPATIAL`, `Opis`, `Data`, `ImieNazwisko`, `Email`) VALUES (GeomFromText('POINT($wspX $wspY)'), '$opis2', NOW(), '$nazwa2', '$mail2');");
		}elseif ($kategory2 == "Ubytki nawierzchni"){
			mysql_query("INSERT INTO `DROGI_D` (`SPATIAL`, `Opis`, `Data`, `ImieNazwisko`, `Email`) VALUES (GeomFromText('POINT($wspX $wspY)'), '$opis2', NOW(), '$nazwa2', '$mail2');");
		}elseif ($kategory2 == "Zdewastowane mienie"){
			mysql_query("INSERT INTO `WANDALIZM` (`SPATIAL`, `Opis`, `Data`, `ImieNazwisko`, `Email`) VALUES (GeomFromText('POINT($wspX $wspY)'), '$opis2', NOW(), '$nazwa2', '$mail2');");
		}elseif ($kategory2 == "Agresywny pies"){
			mysql_query("INSERT INTO `PIES` (`SPATIAL`, `Opis`, `Data`, `ImieNazwisko`, `Email`) VALUES (GeomFromText('POINT($wspX $wspY)'), '$opis2', NOW(), '$nazwa2', '$mail2');");
		}elseif ($kategory2 == "Bójka"){
			mysql_query("INSERT INTO `BOJKA` (`SPATIAL`, `Opis`, `Data`, `ImieNazwisko`, `Email`) VALUES (GeomFromText('POINT($wspX $wspY)'), '$opis2', NOW(), '$nazwa2', '$mail2');");
		}elseif ($kategory2 == "Inne..."){
			mysql_query("INSERT INTO `INNE` (`SPATIAL`, `Opis`, `Data`, `ImieNazwisko`, `Email`) VALUES (GeomFromText('POINT($wspX $wspY)'), '$opis2', NOW(), '$nazwa2', '$mail2');");
		}
	
	//mysql_close($link);
?>