<?PHP

class Cache {
        //geändert!
	//public static $path = "cache_dir";
        public static $path;
	//public static $path = plugins_url( "cache_dir", __FILE__); 
        
	/** 
	* Optionen 
	* 
	* @var array
	* @access private 
	*/ 
	private $optionen = NULL; 


	/**
	 * Constructor.
	 *
	 *
	 * @param Uebergebene argumente
	 * @param Pfad zu Conf Datei
	 * @access 	public
	 */
	function __construct($optionen) {
            //self::$path = plugins_url( "cache_dir", __FILE__);
            self::$path = dirname( plugin_basename( __FILE__ ) )."/cache_dir";
		$this->optionen = $optionen;
		
	}

	public function holeDaten($force = false) {
	
		$sucheDateiErgebnis = $this->sucheDatei();
		
		if($sucheDateiErgebnis > 0) {
			// Datei ist vorhanden und gueltig
			return file_get_contents($this->filepath());
		}

		if($sucheDateiErgebnis == 0 && $force) {
			// Datei nicht gueltig, aber:
			// Ausgabe durch Parameter forciert.
			return file_get_contents($this->filepath());
		
		}

		return -1;
	}

	public function setzeDaten($data) {
		
		$filepath = $this->filepath();
                if(file_exists($filepath)) {
                    file_put_contents($filepath, $data);
                }
	}

	private function sucheDatei() {
		
		// Generiere Pfad.
		$filepath = $this->filepath();

		if(!file_exists($filepath)) {
			//Datei existiert nicht.
			return -1;
		}

		$expire = $this->optionen["SeitenCache"];
		// Ueberpruefe Date auf Gueltigkeit
		if(time() < (filemtime($filepath) + $expire)) {
			// Datei gueltig
			return 1;
		}else{
			// Datei gefunden aber nicht gueltig
			return 0;
		}
	}

	private function filepath() {
		// Key md5 codieren
		$key = md5($this->key());
		return self::$path."/".$key;
	}

	private function key() {
		$optionen = $this->optionen;
		if(!$optionen) {
			return -1;
		}

		if(!array_key_exists("task", $optionen)) {
			// Fehler in Konifguration
			return -1; 
		}
		
		switch ($optionen["task"]) {
			case 'mitarbeiter-alle':				return $optionen["task"]."/".$optionen["UnivISOrgNr"];
			case 'mitarbeiter-einzeln':				return $optionen["task"]."/".$optionen["lastname"]."-".$optionen["firstname"];
			case 'lehrveranstaltungen-alle':		return $optionen["task"]."/".$optionen["UnivISOrgNr"];
			case 'lehrveranstaltungen-einzeln':		return $optionen["task"]."/".$optionen["id"];
			case 'publikationen':					return $optionen["task"]."/".$optionen["UnivISOrgNr"];
				
			default:								return -1;
		}

	}

}

?>