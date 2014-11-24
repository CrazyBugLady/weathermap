<?php
	session_start();
	include "DAL/CSVReader.class.php";
	include "BO/Kartentypen.enum.php";
	include "BO/Karte.class.php";
	include "BO/Region.class.php";
	include "BO/Kartengenerator.class.php";
	include "BO/Filter.class.php";
	
/* Configabteil*/
	// Für die einzelnen Regionen werden Objekte erstellt
	$Genf = new Region(1, "Genf", 50, 674);
	$Wallis = new Region(2, "Wallis", 472, 706);
	$Tessin = new Region(3, "Tessin", 861, 639);
	$Graubuenden = new Region(4, "Graubünden", 1044, 450);
	$Zuerich = new Region(5, "Zürich", 766, 207);
	$Bern = new Region(6, "Bern", 453, 398);
	$Basel = new Region(7, "Basel", 480, 155);
		
	// Die Objekte werden in ein Array gespeichert
	$Regionen = array($Genf, $Wallis, $Tessin, $Graubuenden, $Zuerich, $Bern, $Basel);
	
	// In einer Session werden die aktuellen Daten für diese Woche gespeichert, diese müssen dann nicht mehr eingelesen werden
	if(!array_key_exists("Wetterdaten", $_SESSION))
	{
		// Der CSV-Reader verarbeitet die Daten, die benotwendigt werden in einem mehrdimensionalen Array ein
		$Reader = new CSVReader("http://localhost/Wetterkarte/Sources/wetter.php");
		$_SESSION["Wetterdaten"] = $Reader->getData();
		// Löschen aller erstellten Files, um neue Daten zuzulassen nach Vernichtung der Session
		array_map('unlink', glob("Sources/Bilder/Karten/*.png"));
	}

	// Der Kartengenerator wird instanziiert
	$MapGenerator = new Kartengenerator("Sources/Bilder/plan.png", "Schweiz", $Regionen, $_SESSION["Wetterdaten"]);
/* Configabteil */

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Wetterkarten-Generator</title>

	<link href="Sources/css/bootstrap.css" rel="stylesheet">
    <link href="Sources/css/bootstrap.min.css" rel="stylesheet">

  </head>
  <body>
  
  <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php">Wetterkarten-Generator</a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <?php 
				// Der Mapgenerator erstellt das Menü
				$MapGenerator->buildMenu();
		  ?>
          </ul>
        </div>
      </div>
    </div>
  
<div class="container">
    <div class="page-header" style="padding-top:50px;">
        <h1>Wetterkarten-Generator</h1>
    </div>
<div class="jumbotron">
	<?php 
	// Der Filter wird gesetzt
		$Datum = "";
		$Kartentyp = "";
	
		// Gibt es Übergabewerte ? Sonst Standardwerte
		if(array_key_exists("date", $_GET))
		{
			$Datum = $_GET["date"];
		}
		else
		{
			$Datum = date('Y-m-d', mktime(0,0,0,date('n'), date('d'), date('Y')));
		}
		
		// Gibt es Übergabewerte? Sonst Standardwerte
		if(array_key_exists("Kartentyp", $_GET))
		{
			// Für den Fall, dass ein Wert grösser 4 oder kleiner 0 eingegeben wurde, soll der Filterwert auf 1 zurückgesetzt werden, damit keine Exceptions auftreten
			$Kartentyp = ($_GET["Kartentyp"] < 5 and $_GET["Kartentyp"] > 0) ? $_GET["Kartentyp"] : Kartentypen::Wetterlage;
		}
		else
		{
			$Kartentyp = Kartentypen::Wetterlage;
		}

		
		// Setzen des Filters
		$MapGenerator->setFilter($Datum, $Kartentyp);
		// Erstellen der Kartenübersicht (unter anderem auch Legende und dergleichen)
		$MapGenerator->buildMap();
?>
</form>	
</div>
</div>
    <!-- jQuery (wird für Bootstrap JavaScript-Plugins benötigt) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Binde alle kompilierten Plugins zusammen ein (wie hier unten) oder such dir einzelne Dateien nach Bedarf aus -->
    <script src="Sources/js/bootstrap.min.js"></script>
  </body>
</html>