<?php

   function get_univisdata( $univis_id ) {
    
    	$univis_url = "http://univis.uni-erlangen.de/prg";
        global $post;

        //$univis_id = get_post_meta( $post->ID, 'fau_person_job_title', true);
        //_rrze_debug($post);

        if($univis_id) {
		//Ueberpruefe ob Vor- und Nachname gegeben sind.
/*		$noetige_felder = array("firstname", "lastname");
		foreach ($noetige_felder as $feld) {
			if(!array_key_exists($feld, $this->optionen) || $this->optionen[$feld] == "") {
				// Fehler: Bitte geben Sie Vor- und Nachname der gesuchten Person an
				echo "<div class=\"hinweis_wichtig\">Bitte geben Sie Vor- und Nachname der gesuchten Person an.</div>";
				return -1;
			}

			if(strrpos($this->optionen[$feld], "&") !== false) {
				echo "Ung&uuml;ltige Eingabe.";
				return -1;
			}
		}
*/
		// Hole Daten von Univis
		$url = $univis_url."?search=persons&id=".$univis_id."&show=xml";



		if(!fopen($url, "r")) {
			// Univis Server ist nicht erreichbar
			return -1;
		}

		$persArray = xml2array($url);
                if(empty($persArray)) {
                    echo "Leider konnte die Person nicht gefunden werden.";
                    return -1;
                } else {
		$person = $persArray["Person"];

		if(count($persArray) == 0 ) {

			// Keine Person gefunden
			return -1;
		}

		// Falls mehrer Personen gefunden wurden, wähle die erste
		if($person) $person = $person[0];

		// Lade Publikationen und Lehrveranstaltungen falls noetig
/*              if ($this->optionen["Personenanzeige_Publikationen"]) {
			$person["publikationen"] = $this->_ladePublikationen($person["id"]);
		}

		if ($this->optionen["Personenanzeige_Lehrveranstaltungen"]) {
			$person["lehrveranstaltungen"] = $this->_ladeLehrveranstaltungenAlle($person["id"]);
		}
*/
                //_rrze_debug($person);
		return $person;
                }
        } else {
            echo "Sie haben keine UnivIS-ID ausgewählt.";
        }
   }