<?php
/**
 * Die Klasse Karte ist eine Klasse, zur Erstellung der gesamten Karte, diese kann Unterelemente haben, die sogenannten Regionen,
 * was in diesen abgeht ist abhängig von den Daten, die der Karte übergeben werden.
 */
    class Karte
    {
    	public $Regionen;
		public $Kartentyp;
		public $Hintergrund;
		public $Filename;
		
		/**
		* Standardkonstruktor
		*/
		public function __construct($Regionen, $Kartentyp, $Hintergrund, $Filename)
		{
			$this->Regionen = $Regionen;
			$this->Kartentyp = $Kartentyp;
			$this->Hintergrund = $Hintergrund;
			$this->Filename = $Filename;
		}
		
		/**
		* Zeigen der Karte (wenn recreate, destroyen der alten Karte, zeigen der neuen Karte)
		*/
		public function showMap()
		{
			if(!file_exists("./Sources/Bilder/Karten/" . $this->Filename))
			{
				$this->buildMap();
			}		
		
			echo "<img src='./Sources/Bilder/Karten/".$this->Filename."' class='img-thumbnail' width='100%'>\n";
		}
		
		/**
		* Erstellen der Karte
		*/
		public function buildMap()
		{			
			// Erfragen der Images für den aktuellen Kartentyp
		    $currentImages = $this->getImages();
			foreach($this->Regionen as $Region)
			{
				switch($this->Kartentyp)
				{
					case Kartentypen::Wind:
						// Erhalten der beiden Werte für den Wind, um zum einen ein Pfeilbild zu setzen und zum andern den Text darunter / darüber
						$Windteile = $Region->getWinddata();
						if($Windteile[0] != "NN")
						{
							$this->setIconOnMap($currentImages[$this->getWindWert($Windteile[0])], $Region->getPosition_x(), $Region->getPosition_y());
						}
						$this->setTexTOnMap($Windteile[1] . " km/h", $Region->getPosition_x() + 27, $Region->getPosition_y()+10);
						
					break;
					case Kartentypen::Temperatur:
						// Erhalten der beiden Werte für die Temperatur, um zum einen die Mindesttemperatur und zum andern die Höchsttemperatur zu setzen
						$Temperaturteile = $Region->getTemperatureData();
						$this->setTextOnMap($Temperaturteile[0], $Region->getPosition_x() - 10, $Region->getPosition_y()+10);
						$this->setTextOnMap("/", $Region->getPosition_x() + 17, $Region->getPosition_y()+10);
						$this->setTextOnMap($Temperaturteile[1], $Region->getPosition_x() + 27, $Region->getPosition_y()+10);
					break;
					default:
					// Sowohl bei Pollenbelastung, als auch bei Wetterlagen braucht man keine unterschiedlichen Herangehensweisen, da kann mittels der Region herausgefunden werden,
					// welches Icon gesetzt werden muss und wo
					$this->setIconOnMap($currentImages[$Region->getIndexOfValue($this->Kartentyp)], $Region->getPosition_x(), $Region->getPosition_y());
					break;
				}			
			}
			
			imagepng($this->Hintergrund, "./Sources/Bilder/Karten/". $this->Filename, 0);
		}
		
		/**
		* Setzen eines Bildes auf der Karte
		*/
		public function setIconOnMap($Icon, $Position_x, $Position_y)
		{
			imagecopy($this->Hintergrund, $Icon, $Position_x, $Position_y, 0, 0, imageSX($Icon), imageSY($Icon));
		}
		
		/**
		* Setzen von Text auf der Karte
		*/
		public function setTextOnMap($Text, $Position_x, $Position_y)
		{
			$Schriftart = "Sources/fonts/arial.ttf";
			$color = imagecolorallocate($this->Hintergrund,0,0,0);
			
			if(is_numeric($Text))
			{
				if($Text > 20)// hohe Temperaturen darstellen
				{
					$color = imagecolorallocate($this->Hintergrund,204,0,0);
				}
				else if($Text < 0) // Minustemperaturen darstellen
				{
					$color = imagecolorallocate($this->Hintergrund,0,51,204);
				}
			}
			imagettftext($this->Hintergrund, 15, 0, $Position_x, $Position_y, $color, $Schriftart, $Text); // Setzen des Texts auf der Map
		}
			
		/**
		* Kriegen des Index für den Windwert
		*/
		public function getWindWert($Windrichtung)
		{
			// Aus einem Textwert einen Zahlenwert erhalten
			if($Windrichtung == "N"){ return 0; }
			if($Windrichtung == "NO") { return 1; }
			if($Windrichtung == "NW"){ return 2; }
			if($Windrichtung == "S"){ return 3; }
			if($Windrichtung == "SO"){ return 4; }
			if($Windrichtung == "SW") { return 5; }
			if($Windrichtung == "W"){ return 6; }
			if($Windrichtung == "O") { return 7; }
		}
		
		/**
		* Erhalten der Images für die jeweilige Karte
		*/
		public function getImages()
		{
			$Tageszeit = date("H", time()); 
			$images = array();
			switch($this->Kartentyp)
			{
				case Kartentypen::Pollenflug:
					array_push($images, imagecreatefromgif("./Sources/Bilder/Pollenbelastung_1.gif"));
					array_push($images, imagecreatefromgif("./Sources/Bilder/Pollenbelastung_2.gif"));
					array_push($images, imagecreatefromgif("./Sources/Bilder/Pollenbelastung_3.gif"));
					array_push($images, imagecreatefromgif("./Sources/Bilder/Pollenbelastung_4.gif"));
					break;
				case Kartentypen::Wetterlage:
					$tageszeitImg = ($Tageszeit > 18 or $Tageszeit < 7) ? "nacht" : "tag"; // Je nach Tageszeit werden Nachtbilder oder Morgenbilder gesetzt
					array_push($images, imagecreatefrompng("./Sources/Bilder/sonnig_" .$tageszeitImg. ".png"));
					array_push($images, imagecreatefrompng("./Sources/Bilder/bewoelkt_" .$tageszeitImg. ".png"));
					array_push($images, imagecreatefrompng("./Sources/Bilder/regen_" .$tageszeitImg. ".png"));
					array_push($images, imagecreatefrompng("./Sources/Bilder/gewitter_" .$tageszeitImg. ".png"));
					array_push($images, imagecreatefrompng("./Sources/Bilder/schnee_" .$tageszeitImg. ".png"));					
					break;
				case Kartentypen::Wind:
					array_push($images, imagecreatefrompng("./Sources/Bilder/Pfeile/N-Pfeil.png"));
					array_push($images, imagecreatefrompng("./Sources/Bilder/Pfeile/NO-Pfeil.png"));
					array_push($images, imagecreatefrompng("./Sources/Bilder/Pfeile/NW-Pfeil.png"));
					array_push($images, imagecreatefrompng("./Sources/Bilder/Pfeile/S-Pfeil.png"));
					array_push($images, imagecreatefrompng("./Sources/Bilder/Pfeile/SO-Pfeil.png"));
					array_push($images, imagecreatefrompng("./Sources/Bilder/Pfeile/SW-Pfeil.png"));
					array_push($images, imagecreatefrompng("./Sources/Bilder/Pfeile/W-Pfeil.png"));
					array_push($images, imagecreatefrompng("./Sources/Bilder/Pfeile/O-Pfeil.png"));
					break;
			}
			return $images;
		}
		
    }
?>