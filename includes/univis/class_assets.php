<?php

class Assets {

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

		$this->optionen = $optionen;

	}

	public function holeDaten() {
		$daten = array(
			"beschreibung" => $this->holeBeschreibung(),
			"bild" => $this->holeBild(),
			"download_link" => $this->holeDownloadLink()
		);

		return $daten;
	}

	public function holeBild() {
		// Pruefe ob Bild geladen werden soll.
		if(!$this->optionen["Personenanzeige_Bildsuche"])	return NULL;

		$datentypen = array(".png", ".jpg", ".gif");

		foreach ($datentypen as $typ) {
			$filepath = $this->filepath().$typ;
			if(!file_exists($filepath)) {
				// Datei existiert nicht.
			}else{
				$daten = base64_encode(file_get_contents($filepath));
				return 'data:image/' . $typ . ';base64,' . $daten;
			}
		}
		return NULL;
	}

	private function holeBeschreibung() {
		// Pruefe ob Zusatzdatei geladen werden soll
		if(!$this->optionen["Personenanzeige_ZusatzdatenInDatei"]) return NULL;

		$filepath = $this->filepath().".txt";
		if(!file_exists($filepath)) {
			// Datei existiert nicht.
			return NULL;
		}
		return file_get_contents($filepath);
	}

	private function holeDownloadLink() {
		$filepath = $this->filepath().".zip";
		if(!file_exists($filepath)) {
			// Datei existiert nicht.
			return NULL;
		}
		return substr($filepath, 8);
	}

	private function filepath() {
		$optionen = $this->optionen;
		if(!$optionen) {
			return -1;
		}

		if(!array_key_exists("task", $optionen)) {
			// Fehler in Konifguration
			return -1;
		}

		$path = "../../../";
		$path = $path.$optionen["Datenverzeichnis"]."/".$optionen["task"];
		switch ($optionen["task"]) {
			case 'mitarbeiter-alle':				return $path."/".(int)$optionen["UnivISOrgNr"];
			case 'mitarbeiter-orga':				return $path."/".(int)$optionen["UnivISOrgNr"];
			case 'mitarbeiter-einzeln':				return $path."/".strtolower($optionen["firstname"]."-".$optionen["lastname"]);
			case 'lehrveranstaltungen-alle':		return $path."/".(int)$optionen["UnivISOrgNr"];
			case 'lehrveranstaltungen-einzeln':		return $path."/".(int)$optionen["id"];
			//case 'lehrveranstaltungen-kalender':	return $path."/".(int)$optionen["UnivISOrgNr"];
			case 'publikationen':					return $path."/".(int)$optionen["UnivISOrgNr"];

			default:								return -1;
		}
	}

}

?>