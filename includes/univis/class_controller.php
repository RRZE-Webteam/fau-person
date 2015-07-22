<?php

require_once("class_univis.php");
//require_once("class_render.php");
//require_once("class_cache.php");
//require_once("class_assets.php");
//require 'Mustache/Autoloader.php';

class Controller {

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
	function __construct($task, $args, $confFile=NULL) {

		$this->_ladeConf($confFile, $args);

		if($task && $this->optionen)
			$this->optionen["task"] = $task;

	}
        
    private static function get_options() {
        $defaults = self::default_options();

        $options = (array) get_option(self::option_name);
        $options = wp_parse_args($options, $defaults);

        $options = array_intersect_key($options, $defaults);

        return $options;
    }

    private static function default_options() {
        $linktext = '<b><i>Univ</i>IS</b> - Informationssystem der FAU';
        $options = array(
            'univis_default_link' => $linktext,
            
        );
        return $options;
    }
    
    private static function get_defaults() {
        $defaults = array(
			'UnivISOrgNr' => '0',
			'task' => 'mitarbeiter-alle',
                        'Personenanzeige_Verzeichnis' => '',
			'Personenanzeige_Bildsuche' =>	'1',
			'Personenanzeige_ZusatzdatenInDatei' =>	'1',
			'Personenanzeige_Publikationen'	=> '0',
			'Personenanzeige_Lehrveranstaltung' => '1',
                        'Lehrveranstaltung_Verzeichnis' => '',
                        'SeitenCache' => '0',
			'START_SOMMERSEMESTER' => '1.4',
			'START_WINTERSEMESTER' => '1.10',
			'Zeige_Sprungmarken' => '0',
			'OrgUnit' => '',
			'Sortiere_Alphabet' => '0',
			'Sortiere_Jobs' => '1',
                        'Ignoriere_Jobs' => 'Sicherheitsbeauftragter|IT-Sicherheits-Beauftragter|Webmaster|Postmaster|IT-Betreuer|UnivIS-Beauftragte',
                        'Datenverzeichnis' => ''
	);
        return $defaults;
    }               
        
        

	function ladeHTML() {
		$cache = new Cache($this->optionen);
		$datenAusCache = $cache->holeDaten();

		if($datenAusCache != -1) {
			// Daten wurden aus Cache geladen
			return $datenAusCache;
		}

		// Lade Daten von Univis
		$univis = new UNIVIS($this->optionen);
		$daten = $univis->ladeDaten();

		// Pruefe ob Daten erfolgreich geladen wurden.
		if($daten != -1) {
			// Passe Datenstruktur fuer Templating an.
			$render = new Render($this->optionen);
			$daten = $render->bearbeiteDaten($daten);

			// Lade Zusatzinformationen
			$assets = new Assets($this->optionen);
			$daten["assets"] = $assets->holeDaten();

			// Daten rendern
			$html = $this->_renderTemplate($daten);

			if($html != -1) {	//Rendern erfolgreich?

				// Gerenderte Daten in Cache speichern
				$cache->setzeDaten($html);
				return $html;
			}else{
				return "Template Fehler: Konnte Template Datei nicht finden.";
			}

		}else{
			// Lade Daten aus Cache (auch veraltete).
			$datenAusCache = $cache->holeDaten(true);

			if($datenAusCache != -1) {
				return $datenAusCache;
			}else{
				// Konnte keine Daten laden. Alternativausgabe laden
				if($this->optionen["task"] == "mitarbeiter-einzeln") {
					// Lade Mitarbeiter Alle
					echo "<div class=\"hinweis_wichtig\"><h4>Fehler: Konnte Person nicht finden.</h4><p>Bitte wählen sie eine Person aus der Liste.</p></div><br class=\"clear\" />";
					$this->optionen["task"] = "mitarbeiter-alle";
					return $this->ladeHTML();
				}
				if ($this->optionen["task"] == "lehrveranstaltungen-einzeln") {
					// Lade Lehrveranstaltungen Alle
					echo "<div class=\"hinweis_wichtig\"><h4>Fehler: Konnte Lehrveranstaltungen nicht finden.</h4><p>Bitte wählen sie eine Lehrveranstaltung aus der Liste.</p></div><br class=\"clear\" />";
					$this->optionen["task"] = "lehrveranstaltungen-alle";
					return $this->ladeHTML();
				}
			}
		}
	}

	private function _renderTemplate($daten) {
		Mustache_Autoloader::register();

		$m = new Mustache_Engine;
		$template = $this->_get_template();

		if($template == -1) return -1;

		return  $m->render($template, $daten);
	}


	private function _ladeConf($fpath, $args=NULL){
		$options= array();
                if(is_array($fpath)) {
                    $this->optionen = $fpath;
                    return;
                }

		// defaults
		$defaults = array(
			'UnivISOrgNr' => '0',
			'task' => 'mitarbeiter-alle',
			'Personenanzeige_Bildsuche' =>	'1',
			'Personenanzeige_ZusatzdatenInDatei' =>	'1',
			'Personenanzeige_Publikationen'	=> '0',
			'Personenanzeige_Lehrveranstaltung' => '1',
			'START_SOMMERSEMESTER' => '1.4',
			'START_WINTERSEMESTER' => '1.10',
			'Zeige_Sprungmarken' => '1',
			'OrgUnit' => '',
			'Sortiere_Alphabet' => '0',
			'Sortiere_Jobs' => '1'
		);

		// load options
		if ($fpath == NULL) {
			$fpath = '../../univis.conf';
		}
		$fpath_alternative = $_SERVER["DOCUMENT_ROOT"].'/vkdaten/univis.conf';                
		
                if(file_exists($fpath_alternative)){ $fpath = $fpath_alternative; }
		$options = array();
		$fh = fopen($fpath, 'r') or die('Cannot open file!');
		while(!feof($fh)) {
			$line = fgets($fh);
			$line = trim($line);
			if((strlen($line) == 0) || (substr($line, 0, 1) == '#')) {
				continue; // ignore comments and empty rows
			}
			$arr_opts = preg_split('/\t/', $line); // tab separated
			$options[$arr_opts[0]] = $arr_opts[1];
		}
		fclose($fh);

		// merge defaults with options
		$this->optionen = array_merge($defaults, $options);
		if($args)
			$this->optionen = array_merge($this->optionen, $args);

	}

	function _get_template() {
		$filename = $this->optionen['task'].".shtml";
                //geändert!
                //$filename = "templates/".$filename;
                $filename = plugins_url( "templates/".$filename, __FILE__);
		$handle = fopen($filename, "r");
                //geändert!
                //$contents = fread($handle, filesize($filename));
                $contents = stream_get_contents($handle);
		fclose($handle);
		return $contents;
	}
        
        
}

?>