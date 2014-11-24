<?php
	/**
	* Die Klasse Kartengenerator ist für das Gesamtbild zuständig
	*/
	class Kartengenerator
	{
		private $Wetterdaten;
		private $Kartenplan;
		private $Land;
		private $Regionen;
		private $Filter;
		
		/**
		* Standardkonstruktor
		*/
		public function __construct($Kartenplan, $Land, $Regionen, $Data) 
		{
			$this->Wetterdaten = $Data;
			$this->Kartenplan = imagecreatefrompng($Kartenplan); // $Kartenplan 
			$this->Land = $Land;
			$this->Regionen = $Regionen;
		}
		
		/**
		* Generieren des Datummenüs
		*/
		public function buildMenu()
		{
			$Kartentyp = "&Kartentyp=" . (array_key_exists('Kartentyp', $_GET) ? $_GET["Kartentyp"] : 1);
			
				$DaysOfWeek = Array();
				$DaysOfWeekFilter = Array();
				
				for($i=0; $i<=6; $i++){
					// $DaysOfWeek dient für den ausgeschriebenen Namen des Wochentages
					$DaysOfWeek[$i] = date('l', mktime(0,0,0,date('n'), date('d')+$i, date('Y')));	
					// $DaysOfWeekFilter dienen für das Datum, mit dem man später die mehrdimensionalen Arrays filtern kann
					$DaysOfWeekFilter[$i] = date('Y-m-d', mktime(0,0,0,date('n'), date('d')+$i, date('Y')));	
				}
				
				foreach($DaysOfWeek as $key => $value)
				{
					$text = "";
					
					switch($key)
					{
						case 0:
						$text = "Today";
						break;
						case 1:
						$text = "Tomorrow";
						break;
						default:
						$text = $value;
					}
						// Setzen der Links für die aktuelle Woche
						echo "<li><a href='index.php?date=".$DaysOfWeekFilter[$key] ."". $Kartentyp."'>".$text."</a></li>\n\r";
					
				}
				
		}
		
		/**
		* Generieren des Formulars zur Auswahl des Kartentyps
		*/
		public function buildForm()
		{
			echo "<form action='index.php' method='get'>\n";
			echo "<fieldset>\n";
			echo "<legend>Filter:</legend>\n";
			$this->buildDropdown();
			echo "</fieldset>\n";
			echo "<input class='btn btn-default' type='submit' value='Zeigen'>\n";
		}
		
		/**
		* Build des Kartentyp Dropdowns
		*/
		public function buildDropdown()
		{
			$Kartentypen = array('Wetterlage', 'Temperatur', 'Wind', 'Pollenbelastung');
			
			echo "<input type='hidden' name='date' value='". $this->Filter->getDatum() ."'>\n";
			echo "<select class='form-control' name='Kartentyp'>\n";
			for($i = 0; $i < count($Kartentypen); $i++) 
			{
				echo "<option value='". ($i + 1) ."' " . (($this->Filter->getType() == ($i + 1)) ? "selected" : "") . ">" . $Kartentypen[$i] . "</option>\n";
			}
			echo "</select>\n";
		}
		
		/**
		* Setzen des Filters
		*/
		public function setFilter($Kartentyp, $Datum)
		{
			$this->Filter = new Filter($Kartentyp, $Datum);
		}
		
		/**
		* Generieren des Kartennamens
		*/
		public function generateMapName()
		{
			return "Plan-" . $this->Filter->getType() . "_" . $this->Filter->getDatum() . ".png";
		}
		
		/**
		* Erstellen der Übersicht (Instanziieren eines Kartenobjekts, das dann die einzelnen Icons auf der Karte setzt)
		*/
		public function buildMap()
		{		
		
		// Setzen der Werte  für die Regionen
			$i = 0;
			foreach($this->Regionen as $Region)
			{
				// Regionen erhalten nur den bereits vorgefilterten Wert
				$Wetterlage = $this->Wetterdaten[$this->Filter->getDatum()][$i][0];
				$Temperatur = $this->Wetterdaten[$this->Filter->getDatum()][$i][1];
				$Wind = $this->Wetterdaten[$this->Filter->getDatum()][$i][2];
				$Pollenbelastung = $this->Wetterdaten[$this->Filter->getDatum()][$i][3];
				
				$Region->setData($Temperatur, $Wind, $Wetterlage, $Pollenbelastung);
				$i++;
			}
			
			$Karte = new Karte($this->Regionen, $this->Filter->getType(), $this->Kartenplan, $this->generateMapName()); 
		
			echo "<h2>" . $this->Land . "</h2>\n";
			
			// Dropdown für die Kartentypen erstellen
			$this->buildForm();
			// Zeigen der aktuell erstellten Karte
			$Karte->showMap();
			
			// Legende anzeigen für den Kartentyp Pollenflug
			if($Karte->Kartentyp == Kartentypen::Pollenflug)
			{
				echo "<table width='100%' class='table-striped'>\n";
				
				echo "<tr>\n<th colspan='2'>Legende</th>\n</tr>";
				
				echo "<tr>\n<td><img src='./Sources/Bilder/Pollenbelastung_1.gif'></td>\n<td>keine Belastung</td>\n</tr>\n";
				echo "<tr>\n<td><img src='./Sources/Bilder/Pollenbelastung_2.gif'></td>\n<td>schwache Belastung</td>\n</tr>\n";
				echo "<tr>\n<td><img src='./Sources/Bilder/Pollenbelastung_3.gif'></td>\n<td>mässige Belastung</td>\n</tr>\n";
				echo "<tr>\n<td><img src='./Sources/Bilder/Pollenbelastung_4.gif'></td>\n<td>starke Belastung</td>\n</tr>\n";
				
				echo "</table>";
			}
		}
	
	}
?>