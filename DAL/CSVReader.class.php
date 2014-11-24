<?php
	include "Reader.class.php";

	/**
	* Klasse zum Lesen der CSV - Dateien
	*/
	class CSVReader extends Reader
	{
		private $File;
		
		/**
		* Standardkonstruktor
		*/
		public function __construct($File)
		{
			$this->File = $File;
		}
		
		/**
		* Lesen der Daten aus dem File und Schreiben in ein mehrdimensionales Array
		*/
		public function getData()
		{
			$CSVData = Array();
			
			if (($handle = fopen($this->File, "r")) !== FALSE) 
			{
				$i = 0;
				while(($line = fgetcsv($handle, 1000, ";")) !== FALSE)
				{
					$CSVData[$line[0]][$i] = array($line[2], $line[3], $line[4], $line[5]);
											 
					if($i + 1 <= 6)
					{
						$i++;
					}
					else	
					{
						$i = 0;
					}
				}	
			}
			return $CSVData;
		}
	}
?>