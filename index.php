<?php
	/*
	* Mapa zgłoszeń.
	* index.php
	* autor: Alicja Puacz
	*/
	
	
	//Połączenie z bazą danych
	$link = mysql_connect('sql5.progreso.pl', 'szymonp9_alicja', '!@WSc$#1123wdfeW');
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	$db_selected = mysql_select_db('szymonp9_alicja', $link);

	//mysql_close($link);
?>

<html>
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Serwis informacyjny w zakresie porządku publicznego miasta Chełm</title>
	<link rel="shortcut icon" type="image/png" href="img/favicon.png" />
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
			<p>Aby dodać zgłoszenie, kliknij przycisk "Dodaj Zgłoszenie".</p>
			<button id="zgloszenie" onclick='dodaj()'>Dodaj zgłoszenie</button>
		
			
			<!-- pokazywanie współrzędnych -->
			<div id="mouse-position">
				<br style="line-height: 8px;">
				<img src="herb2.png" alt="Herb">
				<br><br>Twoja pozycja:&nbsp;
			</div>
			
			<footer>
				<!-- bezpieczenstwo.php = inedx34d.php -->
				<button id="zgloszenie" onclick="window.location.href='bezpieczenstwo.php'">Mapa bezpieczenstwa</button>
				<br>
				<br style="line-height: 7px;">
				<!-- autor.html == index34c.html-->
				<a id='oautorze' href="autor.html" >O autorze</a>
			</footer>
		</div>
	</div>
			
	<div id="map" class="map">
		<div id="popup">
		</div>
	</div>
		
	<div id="tip">
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
		
		//zmienne
		var przycik;
		var x1, y1;
		var xmlhttp, myWindow;
				  
		//punkt - tu centrujemy
		var myLocation = new ol.geom.Point([23.4738, 51.1325]);
		myLocation.transform('EPSG:4326', 'EPSG:3857');
		
		//Legenda i przełącznik warstw
		var layerSwitcher = new ol.control.LayerSwitcher({
			tipLabel: 'Legenda'
		});
		
			
			
		/*Markery TABLE 1 Kradzież - punkty ze zgłoszeniami
		*/
		//styl ikony
		var iconStyleKradziez = new ol.style.Style({
		  image: new ol.style.Icon(({ 
			src: 'img/kradziez.png'
		  }))
		});
		//source wektorowe
		var vectorSourceKradziez = new ol.source.Vector();
		//tablica na wartości współrzędnych
		var phpVarsXKradziez = new Array();
		var phpVarsYKradziez = new Array();
		//tablica na wartości atrybutów
		var opisKradziez = new Array();
		var dataKradziez = new Array();
		//zapytanie do bazy danych
		<?php
			$queryKradziez = mysql_query('SELECT X(`SPATIAL`), Y(`SPATIAL`), Opis, Data FROM `KRADZIEZ`');
			while ($row = mysql_fetch_array($queryKradziez, MYSQL_BOTH)){
				echo 'phpVarsXKradziez.push("' . $row[0] . '");';
				echo 'phpVarsYKradziez.push("' . $row[1] . '");';
				echo 'opisKradziez.push("' . $row[2] . '");';
				echo 'dataKradziez.push("' . $row[3] . '");';
			}
		?>
		//licznik
		var iKradziez;
		for(iKradziez = 0; iKradziez < phpVarsXKradziez.length; iKradziez++){
			//pojedynczy obiekt wektorowy
			var iconFeatureKradziez = new ol.Feature({
			  geometry: new ol.geom.Point([phpVarsXKradziez[iKradziez], phpVarsYKradziez[iKradziez]]),
			  name: "Kradzież",
			  opis: opisKradziez[iKradziez],
			  data: dataKradziez[iKradziez]
			});
			//przypisz styl do obiektu
			iconFeatureKradziez.setId(iKradziez);
			iconFeatureKradziez.setStyle(iconStyleKradziez);
			//dodaj obiekt do source
			vectorSourceKradziez.addFeature(iconFeatureKradziez);	
		}	
		
		
		/*Markery TABLE 2 Ubytki nawierzchni - punkty ze zgłoszeniami
		*/ 
		var iconStyleDroga = new ol.style.Style({
		  image: new ol.style.Icon( ({
			src: 'img/droga.png'
		  }))
		});
		var vectorSourceDroga = new ol.source.Vector();
		var phpVarsXDroga = new Array();
		var phpVarsYDroga= new Array();
		var opisDroga = new Array();
		var dataDroga = new Array();
		<?php
			$queryDroga = mysql_query('SELECT X(`SPATIAL`), Y(`SPATIAL`), Opis, Data FROM `DROGI_D`');
			while ($row = mysql_fetch_array($queryDroga, MYSQL_BOTH)){
				echo 'phpVarsXDroga.push("' . $row[0] . '");';
				echo 'phpVarsYDroga.push("' . $row[1] . '");';
				echo 'opisDroga.push("' . $row[2] . '");';
				echo 'dataDroga.push("' . $row[3] . '");';
			}
		?>
		var iDroga;
		for(iDroga = 0; iDroga < phpVarsXDroga.length; iDroga++){
			var iconFeatureDroga = new ol.Feature({
			  geometry: new ol.geom.Point([phpVarsXDroga[iDroga], phpVarsYDroga[iDroga]]),
			  name: "Ubytki nawierzchni",
			  opis: opisDroga[iDroga],
			  data: dataDroga[iDroga]
			});
			iconFeatureDroga.setId(iDroga);
			iconFeatureDroga.setStyle(iconStyleDroga);
			vectorSourceDroga.addFeature(iconFeatureDroga);
		}		
		
		
		/*Markery TABLE 3 Zdewastowane mienie - punkty ze zgłoszeniami
		*/ 
		var iconStyleWandalizm = new ol.style.Style({
		  image: new ol.style.Icon( ({
			src: 'img/wandalizm.png'
		  }))
		});
		var vectorSourceWandalizm = new ol.source.Vector();
		phpVarsXWandalizm = new Array();
		phpVarsYWandalizm= new Array();
		var opisWandalizm = new Array();
		var dataWandalizm = new Array();
		<?php
			$queryWandalizm = mysql_query('SELECT X(`SPATIAL`), Y(`SPATIAL`), Opis, Data FROM `WANDALIZM`');
			while ($row = mysql_fetch_array($queryWandalizm, MYSQL_BOTH)){
				echo 'phpVarsXWandalizm.push("' . $row[0] . '");';
				echo 'phpVarsYWandalizm.push("' . $row[1] . '");';
				echo 'opisWandalizm.push("' . $row[2] . '");';
				echo 'dataWandalizm.push("' . $row[3] . '");';
			}
		?>
		var iWandalizm;
		for(iWandalizm = 0; iWandalizm < phpVarsXWandalizm.length; iWandalizm++){
			var iconFeatureWandalizm = new ol.Feature({
			  geometry: new ol.geom.Point([phpVarsXWandalizm[iWandalizm], phpVarsYWandalizm[iWandalizm]]),
			  name: "Zdewastowane mienie",
			  opis: opisWandalizm[iWandalizm],
			  data: dataWandalizm[iWandalizm]
			});
			iconFeatureWandalizm.setId(iWandalizm);
			iconFeatureWandalizm.setStyle(iconStyleWandalizm);
			vectorSourceWandalizm.addFeature(iconFeatureWandalizm);
		}
		
		
		/*Markery TABLE 5 Włamanie - punkty ze zgłoszeniami
		*/ 
		var iconStyleWlamanie = new ol.style.Style({
		  image: new ol.style.Icon( ({
			src: 'img/kradziez.png'
		  }))
		});
		var vectorSourceWlamanie = new ol.source.Vector();
		var phpVarsXWlamanie = new Array();
		var phpVarsYWlamanie = new Array();
		var opisWlamanie = new Array();
		var dataWlamanie = new Array();
		<?php
			$queryWlamanie = mysql_query('SELECT X(`SPATIAL`), Y(`SPATIAL`), Opis, Data FROM `WLAMANIE`');
			while ($row = mysql_fetch_array($queryWlamanie, MYSQL_BOTH)){
				echo 'phpVarsXWlamanie.push("' . $row[0] . '");';
				echo 'phpVarsYWlamanie.push("' . $row[1] . '");';
				echo 'opisWlamanie.push("' . $row[2] . '");';
				echo 'dataWlamanie.push("' . $row[3] . '");';
			}
		?>
		var iWlamanie;
		for(iWlamanie = 0; iWlamanie < phpVarsXWlamanie.length; iWlamanie++){
			var iconFeatureWlamanie = new ol.Feature({
			  geometry: new ol.geom.Point([phpVarsXWlamanie[iWlamanie], phpVarsYWlamanie[iWlamanie]]),
			  name: "Włamanie",
			  opis: opisWlamanie[iWlamanie],
			  data: dataWlamanie[iWlamanie]
			});
			iconFeatureWlamanie.setId(iWlamanie);
			iconFeatureWlamanie.setStyle(iconStyleWlamanie);
			vectorSourceWlamanie.addFeature(iconFeatureWlamanie);
		}
		
		
		/*Punkty z tabeli PIES - zdarzenie: Agresywny pies
		*/ 
		var iconStylePies = new ol.style.Style({
		  image: new ol.style.Icon( ({
			src: 'img/psy.png'
		  }))
		});
		var vectorSourcePies = new ol.source.Vector();
		var phpVarsXPies = new Array();
		var phpVarsYPies = new Array();
		var opisPies = new Array();
		var dataPies = new Array();
		<?php
			$queryPies = mysql_query('SELECT X(`SPATIAL`), Y(`SPATIAL`), Opis, Data FROM `PIES`');
			while ($row = mysql_fetch_array($queryPies, MYSQL_BOTH)){
				echo 'phpVarsXPies.push("' . $row[0] . '");';
				echo 'phpVarsYPies.push("' . $row[1] . '");';
				echo 'opisPies.push("' . $row[2] . '");';
				echo 'dataPies.push("' . $row[3] . '");';
			}
		?>
		var iPies;
		for(iPies = 0; iPies < phpVarsXPies.length; iPies++){
			var iconFeaturePies = new ol.Feature({
			  geometry: new ol.geom.Point([phpVarsXPies[iPies], phpVarsYPies[iPies]]),
			  name: "Agresywny pies",
			  opis: opisPies[iPies],
			  data: dataPies[iPies]
			});
			iconFeaturePies.setId(iPies);
			iconFeaturePies.setStyle(iconStylePies);
			vectorSourcePies.addFeature(iconFeaturePies);
		}
				
				
		/*Punkty z tabeli BOJKA - zdarzenie: Bójka
		*/ 
		var iconStyleBojka = new ol.style.Style({
		  image: new ol.style.Icon( ({
			src: 'img/bojka.png'
		  }))
		});
		var vectorSourceBojka = new ol.source.Vector();
		var phpVarsXBojka = new Array();
		var phpVarsYBojka = new Array();
		var opisBojka = new Array();
		var dataBojka = new Array();
		<?php
			$queryBojka = mysql_query('SELECT X(`SPATIAL`), Y(`SPATIAL`), Opis, Data FROM `BOJKA`');
			while ($row = mysql_fetch_array($queryBojka, MYSQL_BOTH)){
				echo 'phpVarsXBojka.push("' . $row[0] . '");';
				echo 'phpVarsYBojka.push("' . $row[1] . '");';
				echo 'opisBojka.push("' . $row[2] . '");';
				echo 'dataBojka.push("' . $row[3] . '");';
			}
		?>
		var iBojka;
		for(iBojka = 0; iBojka < phpVarsXBojka.length; iBojka++){
			var iconFeatureBojka = new ol.Feature({
			  geometry: new ol.geom.Point([phpVarsXBojka[iBojka], phpVarsYBojka[iBojka]]),
			  name: "Bójka",
			  opis: opisBojka[iBojka],
			  data: dataBojka[iBojka]
			});
			iconFeatureBojka.setId(iBojka);
			iconFeatureBojka.setStyle(iconStyleBojka);
			vectorSourceBojka.addFeature(iconFeatureBojka);
		}
		
		
		
		/*Markery TABELA INNE Inne - punkty ze zgłoszeniami
		*/ 
		var iconStyleInne = new ol.style.Style({
		  image: new ol.style.Icon( ({
			src: 'img/inne.png'
		  }))
		});
		var vectorSourceInne = new ol.source.Vector();
		var phpVarsXInne = new Array();
		var phpVarsYInne= new Array();
		var opisInne = new Array();
		var dataInne = new Array();
		<?php
			$queryInne = mysql_query('SELECT X(`SPATIAL`), Y(`SPATIAL`), Opis, Data FROM `INNE`');
			while ($row = mysql_fetch_array($queryInne, MYSQL_BOTH)){
				echo 'phpVarsXInne.push("' . $row[0] . '");';
				echo 'phpVarsYInne.push("' . $row[1] . '");';
				echo 'opisInne.push("' . $row[2] . '");';
				echo 'dataInne.push("' . $row[3] . '");';
			}
		?>
		var iInne;
		for(iInne = 0; iInne < phpVarsXInne.length; iInne++){
			var iconFeatureInne = new ol.Feature({
			  geometry: new ol.geom.Point([phpVarsXInne[iInne], phpVarsYInne[iInne]]),
			  name: "Inne",
			  opis: opisInne[iInne],
			  data: dataInne[iInne]
			});
			iconFeatureInne.setId(iInne);
			iconFeatureInne.setStyle(iconStyleInne);
			vectorSourceInne.addFeature(iconFeatureInne);
		}
		
		
		//Dodanie shp budynki
		asTextBudynki2= new Array();
		funkcjaBudynki2 = new Array();
		<?php
			$query1b = mysql_query('SELECT Astext(`SPATIAL`), Funkcja FROM `BUDYNKI`');
			while ($row1 = mysql_fetch_array($query1b, MYSQL_BOTH)){
				echo 'asTextBudynki2.push("' . $row1[0] . '");';
				echo 'funkcjaBudynki2.push("' . $row1[1] . '");';
			}
		?>
		var budynki2Source = new ol.source.Vector();
		var ind2;
		for(ind2 = 0; ind2 < asTextBudynki2.length; ind2++){
			var format2 = new ol.format.WKT();
			var budynek2 = format2.readFeature(asTextBudynki2[ind2]);
			budynek2.setId(String(ind2));
			budynek2.setProperties({
				name: "budynki",
				opis: funkcjaBudynki2[ind2]
			});
			if(budynek2.get("opis")=="kulturalne"){
				budynek2.setStyle(new ol.style.Style({
								fill: new ol.style.Fill({
									color: 'rgba(153, 204, 255, 0.9)',
								stroke: new ol.style.Stroke({
									color: 'rgba(160, 160, 160, 0.9)',
									width: 1
								})
								})
							}));
			}else if(budynek2.get("opis")=="przemyslowe"){
				budynek2.setStyle(new ol.style.Style({
								fill: new ol.style.Fill({
									color: 'rgba(255, 153, 153, 0.9)'
								}),
								stroke: new ol.style.Stroke({
									color: 'rgba(160, 160, 160, 0.9)',
									width: 1
								})
							}));
			} else if(budynek2.get("opis")=="uslugowe"){
				budynek2.setStyle(new ol.style.Style({
								fill: new ol.style.Fill({
									color: 'rgba(204, 255, 153, 0.9)'
								}),
								stroke: new ol.style.Stroke({
									color: 'rgba(160, 160, 160, 0.9)',
									width: 1
								})
							}));
			} else if(budynek2.get("opis")=="mieszkalne"){
				budynek2.setStyle(new ol.style.Style({
								fill: new ol.style.Fill({
									color: 'rgba(255, 255, 153, 0.9)'
								}),
								stroke: new ol.style.Stroke({
									color: 'rgba(160, 160, 160, 0.9)',
									width: 1
								})
							}));
			}else if(budynek2.get("opis")=="reszta" || budynek2.get("opis")=="inne"){
				budynek2.setStyle(new ol.style.Style({
								fill: new ol.style.Fill({
									color: 'rgba(224, 224, 224, 0.9)'
								}),
								stroke: new ol.style.Stroke({
									color: 'rgba(160, 160, 160, 0.9)',
									width: 1
								})
							}));
			} 
			budynki2Source.addFeature(budynek2);
		}
		
		  
		
		//Dodanie shp obreby
		asTextObreby= new Array();
		<?php
			$query6 = mysql_query('SELECT Astext(`SPATIAL`) FROM `OBREBY`');
			while ($row6 = mysql_fetch_array($query6, MYSQL_BOTH)){
				echo 'asTextObreby.push("' . $row6[0] . '");';
			}
		?>
		var obrebySource = new ol.source.Vector();
		var ind6;
		for(ind6 = 0; ind6 < asTextObreby.length; ind6++){
			var format6 = new ol.format.WKT();
			var obreb = format6.readFeature(asTextObreby[ind6]);
			obreb.setId(ind6);
			obreb.setProperties({
				name: "obreb"
			});
			obreb.setStyle(new ol.style.Style({
								fill: new ol.style.Fill({
									color: 'rgba(160, 70, 160, 0.1)'
								}),
								stroke: new ol.style.Stroke({
									color: 'rgba(76,131,101, 1)',
									width: 4
								})
							}));
			//obreb.getGeometry().transform('EPSG:4326', 'EPSG:3857');
			obrebySource.addFeature(obreb);
		}
		
		
		//Dodanie shp drogi
		asTextDrogi= new Array();
		<?php
			$queryDrogi = mysql_query('SELECT Astext(`SPATIAL`) FROM `DROGI`');
			while ($rowDrogi = mysql_fetch_array($queryDrogi, MYSQL_BOTH)){
				echo 'asTextDrogi.push("' . $rowDrogi[0] . '");';
			}
		?>
		var drogiSource = new ol.source.Vector();
		var iDrogi;
		for(iDrogi = 0; iDrogi < asTextDrogi.length; iDrogi++){
			var formatDrogi = new ol.format.WKT();
			var droga = formatDrogi.readFeature(asTextDrogi[iDrogi]);
			droga.setId(iDrogi);
			droga.setProperties({
				name: "droga"
			});
			droga.setStyle(new ol.style.Style({
								stroke: new ol.style.Stroke({
									color: 'rgba(30, 30, 30, 0.9)'
								})
							}));
			//droga.getGeometry().transform('EPSG:4326', 'EPSG:3857');
			drogiSource.addFeature(droga);
		}
						
		
		//Dodanie shp rzeki
		asTextRzeki= new Array();
		<?php
			$queryRzeki = mysql_query('SELECT Astext(`SPATIAL`) FROM `RZEKI`');
			while ($rowRzeki = mysql_fetch_array($queryRzeki, MYSQL_BOTH)){
				echo 'asTextRzeki.push("' . $rowRzeki[0] . '");';
			}
		?>
		var rzekiSource = new ol.source.Vector();
		var iRzeki;
		for(iRzeki = 0; iRzeki < asTextRzeki.length; iRzeki++){
			var formatRzeki = new ol.format.WKT();
			var rzeka = formatRzeki.readFeature(asTextRzeki[iRzeki]);
			rzeka.setId(iRzeki);
			rzeka.setProperties({
				name: "rzeka"
			});
			rzeka.setStyle(new ol.style.Style({
								stroke: new ol.style.Stroke({
									color: 'rgba(99, 99, 99, 0.7)'
								}),
								fill: new ol.style.Fill({
									color: 'rgba(0, 128, 255, 0.8)'
								})
							}));
			//droga.getGeometry().transform('EPSG:4326', 'EPSG:3857');
			rzekiSource.addFeature(rzeka);
		}
	
		
		//Dodanie shp zieleń
		asTextZielen= new Array();
		<?php
			$queryZielen = mysql_query('SELECT Astext(`SPATIAL`) FROM `ZIELEN`');
			while ($rowZielen = mysql_fetch_array($queryZielen, MYSQL_BOTH)){
				echo 'asTextZielen.push("' . $rowZielen[0] . '");';
			}
		?>
		var zielenSource = new ol.source.Vector();
		var iZielen;
		for(iZielen = 0; iZielen < asTextZielen.length; iZielen++){
			var formatZielen = new ol.format.WKT();
			var zielen = formatZielen.readFeature(asTextZielen[iZielen]);
			zielen.setId(iZielen);
			zielen.setProperties({
				name: "zielen"
			});
			zielen.setStyle(new ol.style.Style({
								stroke: new ol.style.Stroke({
									color: 'rgba(99, 99, 99, 0.7)'
								}),
								fill: new ol.style.Fill({
									color: 'rgba(86, 186, 86, 1)'
								})
							}));
			//droga.getGeometry().transform('EPSG:4326', 'EPSG:3857');
			zielenSource.addFeature(zielen);
		}
	
		
			
		////	
		//MAPA
		var map = new ol.Map({
			view: new ol.View({
				center: myLocation.getCoordinates(),
				maxZoom: 18,
				minZoom: 12,
				zoom: 14
			}),
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
						visible: false,
					    source: new ol.source.OSM()
					 })
				]
				}),
				new ol.layer.Group({
					title: 'Mapa zasadnicza',
					layers: [
						new ol.layer.Vector({
							title: 'Obręby',
							type: 'base',
							visible: true,
							source: obrebySource
						}),
						new ol.layer.Vector({
							title: 'Zieleń',
							source: zielenSource
						}),
						new ol.layer.Vector({
							title: 'Rzeki',
							source: rzekiSource
						}),
						new ol.layer.Vector({
							title: 'Drogi',
							source: drogiSource
						}),
						new ol.layer.Vector({
							title: 'Budynki',
							source: budynki2Source
						})
					]
				}),
				new ol.layer.Group({
					title: 'Zgłoszenia',
					layers: [
						new ol.layer.Vector({
							title: 'Inne',
							source: vectorSourceInne
						}),
						new ol.layer.Vector({
							title: 'Bójka',
							source: vectorSourceBojka
						}),
						new ol.layer.Vector({
							title: 'Agresywny pies',
							source: vectorSourcePies
						}),
						new ol.layer.Vector({
							title: 'Zdewastowane mienie',
							source: vectorSourceWandalizm
						}),
						new ol.layer.Vector({
							title: 'Ubytki nawierzchni',
							source: vectorSourceDroga
						}),
						new ol.layer.Vector({
							title: 'Włamanie',
							source: vectorSourceWlamanie
						})
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
				
		//dodaj zgłoszenie - wyświetl formularz
		function dodaj(){
			document.getElementById("tip").innerHTML = '<div class="alert alert-info" id="alert1" ><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Uwaga!</strong> Aby dodać zgłoszenie, kliknij w miejsce na mapie, gdzie wydarzył się wypadek.  </div>';
			map.once('singleclick', function(evt){
				var lonlat = evt.coordinate;
				x1 = lonlat[0];
				y1 = lonlat[1];
				//formularz.php = index34b.php
				myWindow = window.open("http://alicja.puacz.net/formularz.php", "MsgWindow", "width=250, height=430, resizable=no");
			});
		}	
		
		
		
		
		function ala(evtt) {
		  var feature = map.forEachFeatureAtPixel(evtt.pixel,
			  function(feature, layer) {
				if(feature.get("name")!="budynki" && feature.get("name")!="obreb" && feature.get("name")!="rzeka" && feature.get("name")!="zielen" && feature.get("name")!="droga"){	  
					return feature;
				}
			  });	  
		  if (feature) {
				popup.setPosition(evtt.coordinate);
				$(element).popover({
				  'placement': 'top',
				  'html': true,
				  title: "Właściwości obiektu" + '<button type="button" id="close" class="close" onclick="$(&quot;#example&quot;).popover(&quot;hide&quot;);">&times;</button>',
				  //do poprawy
				  'content': "<u>Zdarzenie:</u> " + '<u>' + feature.get('name') + "</u><br>" + "Opis: " +feature.get("opis") + "<br>" + "Data: " + feature.get("data") 
				});
				$(element).popover('show');
		  } else {
			$(element).popover('destroy');
		  }
		}
		
		
		

		//pop-up
		var element = document.getElementById('popup');
		var popup = new ol.Overlay({
		  element: element,
		  positioning: 'bottom-center',
		  stopEvent: false
		});
		map.addOverlay(popup);

		// wyświetla popup po kliknięciu
		map.on('click', function(evtt) {
		  var feature = map.forEachFeatureAtPixel(evtt.pixel,
			  function(feature, layer) {
				if(feature.get("name")!="budynki" && feature.get("name")!="obreb" && feature.get("name")!="rzeka" && feature.get("name")!="zielen" && feature.get("name")!="droga"){	  
					return feature;
				}
			  });	  
		  if (feature) {
				popup.setPosition(evtt.coordinate);
				$(element).popover({
				  'placement': 'top',
				  'html': true,
				  title: "Właściwości obiektu" + '<button type="button" id="close" class="close" onclick="$(&quot;#example&quot;).popover(&quot;hide&quot;);">&times;</button>',
				  //do poprawy
				  'content': "<u>Zdarzenie: " + feature.get('name') + "</u><br>" + "Opis: " +feature.get("opis") + "<br>" + "Data: " + feature.get("data") 
				});
				$(element).popover('show');
		  } else {
			$(element).popover('destroy');
		  }
		});
		
		// zmień styl kursora nad ikoną
		map.on('pointermove', function(e) {
		  if (e.dragging) {
			$(element).popover('destroy');
			return;
		  }
		  var pixel = map.getEventPixel(e.originalEvent);
		  var hit = map.hasFeatureAtPixel(pixel, function(layer){
									//tylko nad ikonami
									if(layer.getSource()!=budynki2Source && layer.getSource()!=obrebySource && layer.getSource()!=drogiSource && layer.getSource()!=rzekiSource && layer.getSource()!=zielenSource) 
										return layer;
									}	
							);
			map.getTarget().style.cursor = hit ? 'pointer' : '';
		});
		
	</script>
	
  </body>

</html>
