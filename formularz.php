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
	* formularz.php
	* @author Alicja Puacz
	* Okno dialogowe.
	*/
	
	
	
	$link = mysql_connect('sql5.progreso.pl', 'szymonp9_alicja', '!@WSc$#1123wdfeW');
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	$db_selected = mysql_select_db('szymonp9_alicja', $link);
	if (!$db_selected) {
		die ('Nie można ustawić : ' . mysql_error());
	}
?>

<html>
	<head>
    <title>Dodaj zdarzenie</title>
	<link rel="shortcut icon" type="image/png" href="img/favicon.png" />
	<!-- css do całości -->
	<link rel="stylesheet" type="text/css" href="styl.css">
	</head>
	
	<body>
		<div id='formularz'>
			<form>
				Wybierz rodzaj zdarzenia:<br>
				<select id="myList">
					<option>Włamanie</option>
					<option>Ubytki nawierzchni</option>
					<option>Zdewastowane mienie</option>
					<option>Bójka</option>
					<option>Agresywny pies</option>
					<option>Inne...</option>
				</select>
			</form><p>
			Opis: <br>
			<textarea id="opis" rows=3 cols=20 autofocus required>
			</textarea>
			</p>
			<p>
			Imię i nazwisko: <br>
			<textarea id="imieINaz" rows=1 cols=20 required>
			</textarea>
			</p><p>
			E-mail: <br>
			<textarea id="email" rows=1 cols=20 required>
			</textarea>
			</p><p>
			<button id="zgloszenie" onclick="myFunction()">Zgłoś!</button></p>
		</div>
		<p id="demo"></p>

		<script type="text/javascript">
			var x2 = window.opener.x1;
			var y2 = window.opener.y1;
					
			function myFunction() {
				var kategory = document.getElementById("myList").value;
				var opis = document.getElementById("opis").value;
				var nazwa = document.getElementById("imieINaz").value;
				var mail = document.getElementById("email").value;
				var ok = true;
				
				//Poprawność danych - niedozwolone wyrażenia
				var wyrazenia_zle = [';', 'SELECT', 'select', 'Select', 'from', 'FROM', 'where', 'WHERE', 'union', 'UNION', 'import', 'include', "'", '{', '}', '/'];
				for (i=0; i<wyrazenia_zle.length; i++){
					if (mail.includes(wyrazenia_zle[i])==true || opis.includes(wyrazenia_zle[i])==true || nazwa.includes(wyrazenia_zle[i])==true){
						ok=false;
					}
				}
				//Poprawność adresu mail
				if (mail.includes('@') == false){
					ok=false;
				}
				//Poprawność - długość tekstu
				if (mail.length>30 || opis.length>100 || nazwa.length>30){
						ok=false;
				}
				
				if (ok==true){	
					xmlhttp = new XMLHttpRequest();
					//zapis.php = index32a.php
					xmlhttp.open("GET", "zapis.php?x="+x2+"&y="+y2+"&kategory1="+kategory+"&opis1="+opis+"&nazwa1="+nazwa+"&mail1="+mail, true);
					xmlhttp.send(null);
					alert("Dziękujemy za zgłoszenie.");
					close();
				}
				else{
					alert("Niepoprawne dane");
				}
				
			}
		</script>
	</body>

</html>
