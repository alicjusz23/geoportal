<?php
	/*
	* bezpieczenstwo.php 
	*
	*/
	
	//połączenie z bd
	$link = mysql_connect('sql5.progreso.pl', 'szymonp9_alicja', '!@WSc$#1123wdfeW');
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	$db_selected = mysql_select_db('szymonp9_alicja', $link);
	
?>

<html>
  <head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Serwis informacyjny w zakresie porządku publicznego miasta Chełm</title>
		<link rel="shortcut icon" href="img/favicon.png" />
		<!-- popup -->
		<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		<!-- openlayers -->
		<link rel="stylesheet" href="http://openlayers.org/en/v3.9.0/css/ol.css" type="text/css">
		<script src="http://openlayers.org/en/v3.9.0/build/ol.js"></script>
		<!-- layerswitcher -->
		<script src="/ol3-layerswitcher-master/src/ol3-layerswitcher.js"></script>
		<link rel="stylesheet" href="/ol3-layerswitcher-master/src/ol3-layerswitcher.css" />
		<!-- bootstrap do alertu -->
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
			<!-- css do całości -->
		<link rel="stylesheet" type="text/css" href="styl.css">
	
  </head>

  <body>  
	  <header>
		<a href="/">
			<h1></h1>
		</a>
	  </header>
	  
	    <div id="menu">
		<div id="menu2">
			<p><br><br><br>Witam na stronie serwisu informacyjnego w zakresie porządku publicznego miasta Chełm.
			<br>Zapraszam do korzystania!</p>
			
			<!-- pokazywanie współrzędnych -->
			<div id="mouse-position">
				<br><img src="herb2.png" alt="Herb">
				<br><br>Twoja pozycja:&nbsp;
			</div>
			
			<footer>
				<button id="zgloszenie" onclick="window.location.href='/'">Mapa zgłoszeń</button>
				<br>
				<br>
				<!-- autor.html = index34c.html -->
				<a id='oautorze' href="autor.html" >O autorze</a>
			</footer>
		</div>
		</div>
		
		<div id="map" class="map">
			<div id="popup">
			</div>
		</div>

	
	
	<script type="text/javascript">

		//Pokazywanie współrzędnych
		var mousePositionControl = new ol.control.MousePosition({
		  coordinateFormat: ol.coordinate.createStringXY(4),
		  projection: 'EPSG:4326',
		  target: document.getElementById('mouse-position')
		});
		
		//Podziałka skala
		var scaleLineControl = new ol.control.ScaleLine();
		
				  
		//punkt - tu centrujemy
		var myLocation = new ol.geom.Point([23.4738, 51.1325]);
		myLocation.transform('EPSG:4326', 'EPSG:3857');
		
		//Legenda i przełącznik warstw
		var layerSwitcher = new ol.control.LayerSwitcher({
			tipLabel: 'Legenda'
		});
		
		
		
		//style kolorów
		styl4=new ol.style.Style({
								fill: new ol.style.Fill({
									color: 'rgba(255, 0, 0, 0.8)'
								}),
								stroke: new ol.style.Stroke({
									color: 'rgba(174, 0, 0, 1)',
									width: 3
								})
		});
		styl3=new ol.style.Style({
								fill: new ol.style.Fill({
									color: 'rgba(255, 60, 60, 0.8)'
								}),
								stroke: new ol.style.Stroke({
									color: 'rgba(174, 0, 0, 1)',
									width: 3
								})
		});
		styl2=new ol.style.Style({
								fill: new ol.style.Fill({
									color: 'rgba(255, 120, 120, 0.8)'
								}),
								stroke: new ol.style.Stroke({
									color: 'rgba(174, 0, 0, 1)',
									width: 3
								})
		});
		styl1=new ol.style.Style({
								fill: new ol.style.Fill({
									color: 'rgba(255, 180, 180, 0.8)'
								}),
								stroke: new ol.style.Stroke({
									color: 'rgba(174, 0, 0, 1)',
									width: 3
								})
		});
		stylBrak=new ol.style.Style({
								fill: new ol.style.Fill({
									color: 'rgba(255, 180, 180, 0)'
								}),
								stroke: new ol.style.Stroke({
									color: 'transparent',
									width: 0
								})
		});
		
		//Dodanie shp podobreby
		idPodobreby= new Array();
		asTextPodobreby= new Array();
		i_policzO_2 = new Array();
		<?php
			$query6 = mysql_query('SELECT `Id`, Astext(`SPATIAL`) FROM `PODOBREBY`');
			while ($row6 = mysql_fetch_array($query6, MYSQL_BOTH)){
				echo 'idPodobreby.push("' . $row6[0] . '");';
				echo 'asTextPodobreby.push("' . $row6[1] . '");';
			}
			
			$ilePodobreby = mysql_fetch_row(mysql_query('select count(`Id`) from `PODOBREBY`'))[0];
			$zdarzenia = array("WLAMANIE","WANDALIZM","DROGI_D","PIES","INNE","BOJKA");
			$i_policzO =array();
			//po podobrebach
			for ($k=1; $k<=$ilePodobreby; $k++){
				//po rodzaju zdarzenia
				array_push($i_policzO, 0);
				for ($j=0; $j<sizeof($zdarzenia);$j++){
					$queryDlugoscZdarzenia = mysql_fetch_row(mysql_query('select count(`Id`) from `' . $zdarzenia[$j] . '`'))[0];
					//po kazdym zdarzeniu w rodzaju w danym podobrebie
					for ($i=1; $i<=$queryDlugoscZdarzenia; $i++){
						$zapytanie = 'select ST_Contains((select `SPATIAL` from `PODOBREBY` where `Id`=' . $k . '), (SELECT `SPATIAL` from `' . $zdarzenia[$j] . '` where Id=' . $i . '))';
						$queryPolicz = mysql_query($zapytanie);
						$i_policzO[$k]=$i_policzO[$k]+mysql_fetch_row($queryPolicz)[0];
					}
				}
				echo 'i_policzO_2.push("' . $i_policzO[$k] . '");';
			}
		?>
		var podzial = new Array();
		podzial = podziel(i_policzO_2);
		var podobrebySource = new ol.source.Vector();
		var podobrebySource1 = new ol.source.Vector();
		var podobrebySource2 = new ol.source.Vector();
		var podobrebySource3 = new ol.source.Vector();
		var podobrebySource4 = new ol.source.Vector();
		var ind6;
		for(ind6 = 0; ind6 < asTextPodobreby.length; ind6++){
			var format6 = new ol.format.WKT();
			var podobreb = format6.readFeature(asTextPodobreby[ind6]);
			podobreb.setId(idPodobreby[ind6]);
			podobreb.setProperties({
				name: "podobreb"
			});
			var nr = i_policzO_2[idPodobreby[ind6]-1];
			var podobrebClone = podobreb.clone();
			podobrebClone.setStyle(stylBrak);
			if (nr<=podzial[4] && nr>=podzial[3]){
				podobrebySource4.addFeature(podobrebClone);
				podobreb.setStyle(styl4);
			}else if(nr<podzial[3] && nr>=podzial[2]){
				podobrebySource3.addFeature(podobrebClone);
				podobreb.setStyle(styl3);
			}else if(nr<podzial[2] && nr>=podzial[1]){
				podobrebySource2.addFeature(podobrebClone);
				podobreb.setStyle(styl2);
			}else if(nr<podzial[1] && nr>=podzial[0]){
				podobrebySource1.addFeature(podobrebClone);
				podobreb.setStyle(styl1);
			}
			podobrebySource.addFeature(podobreb);
		}
		
		view1=new ol.View({
				center: myLocation.getCoordinates(),
				maxZoom: 18,
				minZoom: 12,
				zoom: 14
			});
		////	
		//MAPA
		var map = new ol.Map({
			view: view1,
			layers: [
			new ol.layer.Group({
                title: 'Mapy bazowe',
                layers: [
					new ol.layer.Tile({
                        title: 'Stamen watercolor',
                        type: 'base',
                        visible: false,
                        source: new ol.source.OSM({
						url: 'http://c.tile.stamen.com/watercolor/{z}/{x}/{y}.jpg'
					  })
                    }),
					 new ol.layer.Image({
						title: 'WMS Ortofotoafmapa Polski',
						type: 'base',
						visible: false,
						extent: [2600072, 6637775, 2626816, 6652068],
						source: new ol.source.ImageWMS({
						  url: 'http://mapy.geoportal.gov.pl/wss/service/img/guest/ORTO/MapServer/WMSServer',
						  params: {'LAYERS': 'Raster'}
						})
					}),
					new ol.layer.Tile({
						title: 'OSM',
						type: 'base',
						visible: true,
					    source: new ol.source.OSM()
					 })
				]
				}),
				new ol.layer.Group({
					title: 'Zagrożenia',
					layers: [
						new ol.layer.Vector({
							title: podzial[3].toString().concat(" - ", podzial[4].toString()),
							source: podobrebySource4
						}),
						new ol.layer.Vector({
							title: podzial[2].toString().concat(" - ", podzial[3].toString()),
							source: podobrebySource3
						}),
						new ol.layer.Vector({
							title: podzial[1].toString().concat(" - ", podzial[2].toString()),
							source: podobrebySource2
						}),
						new ol.layer.Vector({
							title: podzial[0].toString().concat(" - ", podzial[1].toString()),
							source: podobrebySource1
						}),
						new ol.layer.Vector({
							title: "Zagrożenia",
							source: podobrebySource
						}),
					]
				})
			],
			controls: ol.control.defaults().extend([
				scaleLineControl,
				mousePositionControl
			]),
			target:document.getElementById('map')
		});
		
		
		//dodaj layerswitcher
		map.addControl(layerSwitcher);

		
		
		//dzieli tablice wynikow na 4 czesci
		function podziel(arr){
			for(i=0; i<arr.length; i++){
				arr[i]=parseInt(arr[i]);
			}
			var max=arr[0];
			var min=arr[0];
			var i;
			for (i=1; i<arr.length; i++){
				if (max<arr[i]){
					max=arr[i];
				}
				if (min>arr[i]){
					min=arr[i];
				}
			}
			var dist = max-min;
			var d= dist/4;
			var min2= min+d;
			var sr=min+2*d;
			var max2=max-d;
			var arr2=[min, min2, sr, max2, max];
			return arr2;
		}
		
		console.log(view1.getResolution());
		console.log(map.getSize());
		//console.log(view1.calculateExtent(map.getSize()));
		
		
	</script>
	
  </body>

</html>
