<?php
	/**
	* Klasse für das Businessobject Filter
	*/
	class Filter
	{
		private $Datum;
		private $Type;
		
		/**
		* Standardkonstruktor
		*/
		public function __construct($Date, $Type)
		{
			$this->Datum = $Date;
			$this->Type = $Type;
		}
		/**
		* Getter Datum
		*/
		public function getDatum()
		{
			return $this->Datum;
		}
		/**
		* Getter Kartentyp
		*/
		public function getType()
		{
			return $this->Type;
		}
		
	}
?>