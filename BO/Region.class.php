<?php
	/**
	* Die Karte Region ist für alles rund um die einzelnen Regionen zuständig
	*/
	class Region
	{
		public $IndexRegion;
		public $Name;
		public $Position_x;
		public $Position_y;
		
		public $TemperatureForDay;
		public $PollenbelastungForDay;
		public $WindForDay;
		public $WetterlageForDay;
		
		/**
		* Standardkonstruktor
		*/
		public function __construct($IndexRegion, $Name, $Position_x, $Position_y)
		{
			$this->IndexRegion = $IndexRegion;
			$this->Name = $Name;
			$this->Position_x = $Position_x;
			$this->Position_y = $Position_y;
		}
		
		/**
		* Getter x-Position
		*/
		public function getPosition_x()
		{
			return $this->Position_x;
		}
		
		/**
		* Getter y-Position
		*/
		public function getPosition_y()
		{
			return $this->Position_y;
		}
		
		/**
		* Getter Name Region
		*/
		public function getName()
		{
			return $this->Name;
		}
		
		/**
		* Getter Index Region
		*/
		public function getIndexRegion()
		{
			return $this->IndexRegion;
		}
		
		/**
		* Setzen der Daten für Temperatur, Wind, Wetterlage und Pollenbelastung für den aktuellen Tag
		*/
		public function setData($Temperature, $Wind, $Wetterlage, $Pollenbelastung)
		{
			$this->TemperatureForDay = $Temperature;
			$this->WindForDay = $Wind;
			$this->WetterlageForDay = $Wetterlage;
			$this->PollenbelastungForDay = $Pollenbelastung;
		}
		
		/**
		* Erhalten eines Arrays zum Wind (1. Teil ergibt Windrichtung, 2. ergibt Geschwindigkeit)
		*/
		public function getWinddata()
		{
			$Windteile = split("/", $this->WindForDay, 2);
		
				return $Windteile;
		}
		
		/**
		* Erhalten eines Arrays zur Temperatur (1. Teil ergibt Mindesttemperatur, 2. Teil ergibt Maximaltemperatur)
		*/
		public function getTemperatureData()
		{
			$TemperaturTeile = split("/", $this->TemperatureForDay, 2);
			
			return $TemperaturTeile;
		}
		
		/**
		* Erhalten des Indexes (Pollenbelastung wird nicht dekrementiert)
		*/
		public function getIndexOfValue($Kartentyp)
		{
			if($Kartentyp == Kartentypen::Wetterlage){
				return $this->WetterlageForDay - 1;
			}
			else if($Kartentyp == Kartentypen::Pollenflug){
				return $this->PollenbelastungForDay;
			}
		}
		
	}
?>