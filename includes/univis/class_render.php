<?php

require_once("univis_dicts.php");
//require_once 'iCalcreator.class.php';

class Render {


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
	 * @param OPtionen
	 * @access 	public
	 */
	function __construct($optionen) {

		$this->optionen = $optionen;

	}


	function bearbeiteDaten($daten) {

		if (!empty($this->optionen)) {
			switch($this->optionen["task"]){
				case "mitarbeiter-alle":
					return $this->_bearbeiteMitarbeiterAlle($daten);

				case "mitarbeiter-orga":
					return $this->_bearbeiteMitarbeiterOrga($daten);

				case "mitarbeiter-einzeln":
					return $this->_bearbeiteMitarbeiterEinzeln($daten);

				case "publikationen":
					return $this->_bearbeitePublikationen($daten);

				case "lehrveranstaltungen-alle":
					return $this->_bearbeiteLehrveranstaltungenAlle($daten);

				case "lehrveranstaltungen-einzeln":
					return $this->_bearbeiteLehrveranstaltungenEinzeln($daten);

				case "lehrveranstaltungen-kalender":
					return $this->_bearbeiteLehrveranstaltungenKalender($daten);

				default:
					echo "Fehler: Unbekannter Befehl\n";
					return -1;
			}
		}
		return -1;
	}


	private function _bearbeiteMitarbeiterAlle($daten) {
		/////////	Daten Formatieren
		////////////////
		//	Array: ["ORGNAME"] => Array: PERSON-ARRAY
		////////////////


		// Standard Aufteilung: orgname
                $personen = $daten['Person'];
                $jobnamen = $daten['jobs'];
                $such_kategorie = "orgname";
		if($this->optionen["Sortiere_Jobs"]) {
			// Bei Lehrstuehlen ist es aber sinnvoller nach Jobs bzw. Rang zu gliedern.
			$such_kategorie = "rang";
                        $jobs = $daten['Org'][0]['jobs'][0]['job'];
		}
		$gruppen = array();
		$gruppen_dict = array();
                $gruppen_personen = array();
                $gruppen_text = array();
		foreach ($personen as $person) {
                        //Text-Felder müssen auch angezeigt werden, deshalb rausgenommen
			//if(empty($person["firstname"]))
			//	continue;
                        
                                                
			if(empty($person[$such_kategorie])) {
				continue;
			}
                        if(isset($person["title"])) {
                            $person["title-long"] = $this->_str_replace_dict(Dicts::$acronyms, $person["title"]);
                        }
			
                        if(isset($person["firstname"])&&isset($person["lastname"])) {
                            $name = $person["firstname"]."-".$person["lastname"];
                            //$name = $person["@attributes"]["key"];

                            //$person["nameurl"] = str_replace("Person.", ":", $name);
                            //$person["nameurl"] = str_replace(".", "/", $person["nameurl"]);
                            //$person["nameurl"] = $person["semester"] . $person["nameurl"];


                            $person["nameurl"] = strtolower($this->umlaute_ersetzen($name));
                            //$person["nameurl"] = str_replace("-", "_", $person["nameurl"]);
                            $person["nameurl"] = str_replace(" ", "-", $person["nameurl"]);
                        }
                        
                        if(isset($person['text'])) {
                           
                            // Suche nach eingetragen Mailadressen bzw. URLs
                            $suchstring_0 = '\*\*';   // ** durch * ersetzen
                            $html_0 = '*';
                            $suchstring_1 = '\|\|';  // || durch | ersetzen
                            $html_1 = '|';
                            $suchstring_2 = '\^\^';     // ^^ durch ^ ersetzen
                            $html_2 = '^';
                            $suchstring_3 = '__';  // __ durch _ ersetzen
                            $html_3 = '_';
                           
                            $suchstring_4 = '/^- ?(.+)/m';   // - am Anfang der Zeilen: Jeder Listenpunkt wird als vollständige Aufzählung umgesetzt
                            $html_4 = '<ul><li>$1</li></ul>';
                            
                            $suchstring_5 = '/\*(.+)\*/';    // *fett*
                            $html_5 =  '<b>$1</b>';
                            $suchstring_6 = '/\|(.+)\|/';  // |kursiv|
                            $html_6 = '<i>$1</i>';
                            $suchstring_7 = '/\^(.+)\^/';    // pi^2^
                            $html_7 = '<sup>$1</sup>';
                            $suchstring_8 = '/_(.+)_/';    // H_2_O
                            $html_8 = '<sub>$1</sub>';
                            $suchstring_9 = '/\[(.+?)\]\s?(\S+)/'; // [Linktext] Ziel-URL bzw. -Mailadresse
                            $html_9 = "<a href='$2'>$1</a>";
                            //nachfolgender Code gibt Probleme mit Linktext
                            //$suchstring_10 = '/((http|https):\/\/\S+)/'; // http://www.blabla.de/ oder https://www.blaljblsfd.de/lsdjfl.html
                            //$html_10 = "<a href='$1'>$1</a>";
                            //$suchstring_11 = '/(mailto:[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4})/';    // mailto:name@firma.de                          
                            //$html_11 = "<a href='$1'>$1</a>";
                           
                            // Umsetzung in HTML-Link
                            for ($i=0; $i<4; $i++) {
                                $suchstring = 'suchstring_' . $i;
                                $html = 'html_' . $i;
                                $person['text'] = str_replace($$suchstring, $$html, $person['text']);                                
                            }
                                
                            for ($i=4; $i<10; $i++) {
                                $suchstring = 'suchstring_' . $i;
                                $html = 'html_' . $i;
                                $person['text'] = preg_replace($$suchstring, $$html, $person['text']);
                            }
                            // Leerzeile durch Zeilenumbruch und zwei Leerzeilen durch Absatz
                             $person['text'] = str_replace(PHP_EOL, '<br>', $person['text']);
                             $person['text'] = str_replace("<br>\r<br>", '<br>', $person['text']);
                        }
			$gruppen_namen = explode("|", $person[$such_kategorie]);
                                
			foreach ($gruppen_namen as $gruppen_name) {
                            if(empty($gruppen_dict[$gruppen_name])) {
					$gruppen_dict[$gruppen_name] = array();
				}


				array_push($gruppen_dict[$gruppen_name], $person);
                            /*if(isset($person["id"])) {
         			if(empty($gruppen_personen[$gruppen_name])) {
              					$gruppen_personen[$gruppen_name] = array();                                      
				}
				array_push($gruppen_personen[$gruppen_name], $person);
                                
                            } else {
                                if(empty($gruppen_text[$gruppen_name])) {
              					$gruppen_text[$gruppen_name] = array();      
				}
 
				array_push($gruppen_text[$gruppen_name], $person);*/
                            // Suche nach eingetragen Mailadressen bzw. URLs
                            //$suchstring = '/\[(.+?)\](\S+)/';
                            // Umsetzung in HTML-Link
                            //$html = "<a href='$2'>$1</a>";
                        
                            //$gruppen_text[$gruppen_name][0]['text'] = preg_replace($suchstring, $html, $gruppen_text[$gruppen_name][0]['text']);
                            }                                 
		}

                    foreach ($jobnamen as $gruppen_name) {
                            $gruppen_personen = $gruppen_dict[$gruppen_name];
                                                                                    
                            if(isset($gruppen_personen[0]['lastname'])){
                                $gruppen_personen = $this->array_orderby($gruppen_personen, "lastname", SORT_ASC, "firstname", SORT_ASC);
                            }
                            $gruppen_obj = array(
                                    "name" => $gruppen_name,
                                    //"personen" => $this->record_sort($gruppen_personen, "lastname")
                                    "personen" => $gruppen_personen
                            );
         
                            array_push($gruppen, $gruppen_obj);
                    }                  
                
                //Sortierung der Ergebnisse nach dem Funktionsfeld
                //$gruppen = $this->record_sort($gruppen, "name");
                        
		if($this->optionen["OrgUnit"] != "") {
			$gruppe = array(
				"name" => $this->optionen["OrgUnit"],
				"personen" => $gruppen_dict[$this->optionen["OrgUnit"]]
			);
                        $gruppen = array($gruppe);
		}

		// Sollen die Personen alphabetisch sortiert werden?
		if($this->optionen["Sortiere_Alphabet"] != 0) {
			$personen = array();


			foreach ($gruppen as $gruppe) {
				foreach ($gruppe["personen"] as $person) {
                                    if(isset($person['id'])&&isset($person['lastname'])) {
					$personen[] = $person;
                                    }
				}
			}

			$personen = $this->record_sort($personen, "id");
                        $personen = $this->array_orderby($personen,"lastname", SORT_ASC, "firstname", SORT_ASC );
			
                        $gruppe = array("name" => "Alle Mitarbeiter", "personen" => $personen);
			$gruppen = array($gruppe);

		}

		// Zeige keine Sprungmarken falls nur eine OrgUnit vorhanden ist.
		if(count($gruppen) <= 1) {
			$this->optionen["Zeige_Sprungmarken"] = 0;
		}

                //Workaround für die Ausgabe [leer]
                for ($i=0; $i < count($gruppen); $i++) {
			if($gruppen[$i]["name"] =='[leer]') {
                            $gruppen[$i]["name"] = '';
			}                        
                }

		return array("gruppen" => $gruppen, "optionen" => $this->optionen);
	}

	private function _bearbeiteMitarbeiterOrga($personen) {
		/////////	Daten Formatieren
		////////////////
		//	Array: ["ORGNAME"] => Array: PERSON-ARRAY
		////////////////


		$such_kategorie = "orgname";
		$gruppen = array();
		$gruppen_dict = array();

		foreach ($personen as $person) {
			if(empty($person["firstname"]))
				continue;

			if(empty($person[$such_kategorie])) {
				continue;
			}
                        if(isset($person["title"])) {
                            $person["title-long"] = $this->_str_replace_dict(Dicts::$acronyms, $person["title"]);
                        }
                        $name = $person["firstname"]."-".$person["lastname"];
			$person["nameurl"] = strtolower($this->umlaute_ersetzen($name));
			$person["nameurl"] = str_replace(" ", "-", $person["nameurl"]);

			$gruppen_namen = explode("|", $person[$such_kategorie]);

			foreach ($gruppen_namen as $gruppen_name) {
				if(empty($gruppen_dict[$gruppen_name])) {
					$gruppen_dict[$gruppen_name] = array();
				}

				array_push($gruppen_dict[$gruppen_name], $person);
			}
		}


		foreach ($gruppen_dict as $gruppen_name => $gruppen_personen) {
			$gruppen_obj = array(
				"name" => $gruppen_name,
				"personen" => $gruppen_personen
			);

			array_push($gruppen, $gruppen_obj);
		}

		// Soll nur eine bestimmte Org-Einheit angezeigt werden?
		if($this->optionen["OrgUnit"] != "") {
			$gruppe = array(
				"name" => $this->optionen["OrgUnit"],
				"personen" => $gruppen_dict[$this->optionen["OrgUnit"]]
			);
			$gruppen = array($gruppe);
		}

		// Sollen die Personen alphabetisch sortiert werden?
		if($this->optionen["Sortiere_Alphabet"] != 0) {
			$personen = array();

			foreach ($gruppen as $gruppe) {
				foreach ($gruppe["personen"] as $person) {
					$personen[] = $person;
				}
			}

			$personen = $this->record_sort($personen, "lastname");

			$gruppe = array("name" => "Alle Mitarbeiter", "personen" => $personen);
			$gruppen = array($gruppe);

		}

		// Zeige keine Sprungmarken falls nur eine OrgUnit vorhanden ist.
		if(count($gruppen) <= 1) {
			$this->optionen["Zeige_Sprungmarken"] = 0;
		}

		return array("gruppen" => $gruppen, "optionen" => $this->optionen);
	}



	private function _bearbeiteMitarbeiterEinzeln($person) {
		if(!empty($person)) {
			$person["title-long"] = $this->_str_replace_dict(Dicts::$acronyms, $person["title"]);
			$name = $person["firstname"]."_".$person["lastname"];
			$person["nameurl"] = strtolower($this->umlaute_ersetzen($name));
			$person["nameurl"] = str_replace(" ", "%20", $person["nameurl"]);

			// Lade Publikationen
			$publikationen = $this->_bearbeitePublikationen($person["publikationen"]);

			if($publikationen) $person["publikationen"] = $publikationen;
			else unset($person["publikationen"]);


			// Lade Lehrveranstaltungen
			$lehrveranstaltungen = $this->_bearbeiteLehrveranstaltungenAlle($person["lehrveranstaltungen"]);

			if($lehrveranstaltungen) $person["lehrveranstaltungen"] = $lehrveranstaltungen;
			else unset($person["lehrveranstaltungen"]);

			return array("person" => $person, "optionen" =>$this->optionen);
		}
	}

	private function _bearbeitePublikationen($publications) {
		if(!$publications) return NULL;
		$this->_rename_key("hstype", $publications, Dicts::$hstypes);

		// Nach Jahren gruppieren
		$publications = $this->_group_by("year", $publications);

		// Kehre Reihnefolge um: Neu->Alt
		// Und: Aendere Personendaten so um, dass beim Templateing unterschieden werden kann,
		// ob es ein kompletter Personendatensatz vorliegt oder nur der Name.
		// Dazu muss durch verschiedene Schleifen auf die Autoren zugegriffen werden.
		$publications_sorted = array();
		for($i = 0; $i < count($publications); $i++) {
			$year = $publications[count($publications)-1-$i];

			for ($k=0; $k < count($year["data"]); $k++) {
				$publication = $year["data"][$k];

				for ($m=0; $m < count($publication["authors"]); $m++) {
					$author = $publication["authors"][$m]["author"];

					for ($a=0; $a < count($author); $a++) {

						if(array_key_exists("id", $author[$a]["pkey"][0])) {
							$year["data"][$k]["authors"][$m]["author"][$a]["pkey"]["full-profile"] = $year["data"][$k]["authors"][$m]["author"][$a]["pkey"];
							$year["data"][$k]["authors"][$m]["author"][$a]["pkey"]["full-profile"][0]["firstname_small"] = strtolower($this->umlaute_ersetzen($year["data"][$k]["authors"][$m]["author"][$a]["pkey"]["full-profile"][0]["firstname"]));
							$year["data"][$k]["authors"][$m]["author"][$a]["pkey"]["full-profile"][0]["lastname_small"] = strtolower($this->umlaute_ersetzen($year["data"][$k]["authors"][$m]["author"][$a]["pkey"]["full-profile"][0]["lastname"]));
						}else{
							$name = $year["data"][$k]["authors"][$m]["author"][$a]["pkey"][0]["lastname"];
							$year["data"][$k]["authors"][$m]["author"][$a]["pkey"][0]["name"] = $name;
						}

					}
				}

			}
			array_push($publications_sorted, $year);
		}

		$publications = $publications_sorted;

		return array( "years" => $publications, "optionen" => $this->optionen);
	}

	private function _bearbeiteLehrveranstaltungenAlle($veranstaltungen) {
		if(!$veranstaltungen) return NULL;

		$this->_rename_key("type", $veranstaltungen, Dicts::$lecturetypen);

		for ($i=0; $i < count($veranstaltungen); $i++) {
			// Einzelne Veranstaltung bearbeiten
			$veranstaltung_edit = $this->_bearbeiteLehrveranstaltungenEinzeln($veranstaltungen[$i]);
			$veranstaltungen[$i] = $veranstaltung_edit["veranstaltung"];
		}

		//Nach Jahren gruppieren
		$veranstaltungen = $this->_group_by("type", $veranstaltungen);

		return array( "veranstaltungen" => $veranstaltungen, "optionen" => $this->optionen);
	}

	private function _bearbeiteLehrveranstaltungenKalender($veranstaltungen) {
		if(!$veranstaltungen) return NULL;

		$this->_rename_key("type", $veranstaltungen, Dicts::$lecturetypen);

		$tz     = "Europe/Berlin";                   // define time zone
		$config = array( "unique_id" => "fau.rrze.kalender.univis" // set a (site) unique id
		               , "TZID"      => $tz );          // opt. "calendar" timezone
		$v      = new vcalendar( $config );
		$v->setProperty('method', 'PUBLISH' );
		$v->setProperty( "x-wr-calname", "Lehrveranstaltungen" );
		$v->setProperty( "X-WR-CALDESC", "Lehrveranstaltungen" );
		$v->setProperty( "X-WR-TIMEZONE", $tz );

		foreach ($veranstaltungen as $veranstaltung) {
			$veranstaltung = $this->_bearbeiteLehrveranstaltungenEinzeln($veranstaltung);
			$veranstaltung = $veranstaltung["veranstaltung"];

			$titel = $veranstaltung["name"];
			$beschreibung = $veranstaltung["summary"];
			$details = $veranstaltung["details"];

			foreach ($veranstaltung["terms"] as $terms) {
				foreach ($terms as $term) {
					foreach ($term as $ev) {
						$vevent = new vevent();

						$startdate;
						$enddate;

						if(isset($ev["repeat"])) {
							if(substr($ev["repeat"],0,1) == "w") {
								// Woechentlich
								$tage = substr($ev["repeat"],3,1);
								$startdate = date("Ymd\THi", strtotime("Sunday ".$ev["starttime"]." + ".$tage." Days - 30 Weeks"));
								$enddate = date("Ymd\THi", strtotime("Sunday ".$ev["endtime"]." + ".$tage." Days - 30 Weeks"));
								$vevent->setProperty( "rrule", array( "FREQ" => "WEEKLY", "INTERVAL" => substr($ev["repeat"],1,1)));
							}

							if(substr($ev["repeat"],0,1) == "s") {
								// Einzeltermin
								$startdate = date("Ymd\THi", strtotime($ev["startdate"]." ".$ev["starttime"]));
								$enddate = date("Ymd\THi", strtotime($ev["enddate"]." ".$ev["endtime"]));
							}

							if(substr($ev["repeat"],0,1) == "b") {
								// Blocktermin
								$startdate = date("Ymd\THi", strtotime($ev["startdate"]." ".$ev["starttime"]));
								$enddate = date("Ymd\THi", strtotime($ev["startdate"]." ".$ev["endtime"]));
								$tage = (strtotime($ev["enddate"]) - strtotime($ev["startdate"])) / (60*60*24) + 1;
								$vevent->setProperty( "rrule", array( "FREQ" => "DAILY", "COUNT" => $tage));
							}
						}



						$vevent->setProperty( "dtstart", $startdate );
						$vevent->setProperty( "dtend", $enddate );
						$vevent->setProperty( "LOCATION", $ev["room_short"]);
						$vevent->setProperty('SUMMARY', $titel);
						$vevent->setProperty('DESCRIPTION', $beschreibung);
						$v->setComponent($vevent);
					}
				}
			}
		}

		return array( "ics" => $v->returnCalendar(), "optionen" => $this->optionen);
	}

	private function _bearbeiteLehrveranstaltungenEinzeln($veranstaltung) {


		$this->_rename_key("type", $veranstaltung, Dicts::$lecturetypen);

		// Dozs
		for ($i = 0; $i<count($veranstaltung["dozs"]); $i++) {
			for ($k = 0; $k < count($veranstaltung["dozs"][$i]["doz"]); $k++) {

				$veranstaltung["dozs"][$i]["doz"][$k]["firstname_small"] = strtolower($this->umlaute_ersetzen($veranstaltung["dozs"][$i]["doz"][$k]["firstname"]));
				$veranstaltung["dozs"][$i]["doz"][$k]["lastname_small"] = strtolower($this->umlaute_ersetzen($veranstaltung["dozs"][$i]["doz"][$k]["lastname"]));
			}
		}


		//Begin: Angaben
		$angaben = array();

		//Typ
		if($veranstaltung["type"]) {
			$type = $this->_str_replace_dict(Dicts::$lecturetypen_short, $veranstaltung["type"]);
			array_push($angaben, $type);
		}

		//Schein
		if($veranstaltung["schein"] && $veranstaltung["schein"] == "ja") {
			array_push($angaben, "Schein");
		}

		//SWS
		if ($veranstaltung["sws"]) {
			array_push($angaben, $veranstaltung["sws"]." SWS");
		}

		//ECTS
		if($veranstaltung["ects"] && $veranstaltung["ects"] == "ja") {
			array_push($angaben, "ECTS-Studium");
		}

		if($veranstaltung["ects_cred"]) {
			array_push($angaben, "ECTS-Credits: ".$veranstaltung["ects_cred"]);
		}

		//Anfänger
		if($veranstaltung["beginners"] && $veranstaltung["beginners"] == "ja") {
			array_push($angaben, "für Anfänger geeignet");
		}

		//Gasthörer
		if($veranstaltung["gast"] && $veranstaltung["gast"] == "ja") {
			array_push($angaben, "für Gasthörer zugelassen");
		}

		//Evaluation
		if($veranstaltung["evaluation"] && $veranstaltung["evaluation"] == "ja") {
			array_push($angaben, "Evaluation");
		}

		//Unterrrichtssprache
		if ($veranstaltung["leclanguage"]) {
			$formated = $this->_str_replace_dict(Dicts::$leclanguages, $veranstaltung["leclanguage"]);
			array_push($angaben, "Unterrichtssprache ".$formated);
		}

		//Comment
		if($veranstaltung["comment"]) {
			array_push($angaben, $veranstaltung["comment"]);
		}

		$veranstaltung["angaben"] = implode(", ", $angaben);

		//Begin Zeit und Ort
		for ($_terms=0; $_terms < count($veranstaltung["terms"]); $_terms++) {
			for ($_term=0; $_term < count($veranstaltung["terms"][$_terms]["term"]); $_term++) {
				$lecture = &$veranstaltung["terms"][$_terms]["term"][$_term];

				$date = array();

				$repeat = explode(" ", $lecture["repeat"]);
				if($repeat) {
					$dict = array(
						"w1" => "",
						"w2" => "Alle zwei Wochen",
						"w3" => "Alle drei Wochen",
						"w4" => "Alle vier Wochen",
						"s1" => "Einzeltermin am"
					);

					if(array_key_exists($repeat[0], $dict))
						array_push($date, $dict[$repeat[0]]);

					if($repeat[0] == "s1") {
						$formated = date("d.m.Y", strtotime($lecture["startdate"]));
						array_push($date, $formated);
					}

					if(count($repeat)>0) {
						$days_short = array(
							1 => "Mo",
							2 => "Di",
							3 => "Mi",
							4 => "Do",
							5 => "Fr",
							6 => "Sa",
							7 => "So"
						);

						$days_long = array(
							1 => "Montag",
							2 => "Dienstag",
							3 => "Mittwoch",
							4 => "Donnerstag",
							5 => "Freitag",
							6 => "Samstag",
							7 => "Sonntag"
						);

						array_push($date, $days_short[$repeat[1]]);

					}
				}

				$lecture["date"] = implode(" ", $date);

				$lecture["room_short"] = $lecture["room"][0]["short"];

				if($lecture["exclude"]) {
					$dates = explode(",", $lecture["exclude"]);

					for ($i=0; $i < count($dates); $i++) {
						if($dates[$i]=="vac")
							unset($dates[$i]);
						else
							$dates[$i] = date("d.m.Y", strtotime($dates[$i]));
					}

					$lecture["exclude"] = implode(", ", $dates);
				}
			}
		}//end Zeit und Ort


		//Summary
		$veranstaltung["summary"] = str_replace("\n", "<br/>", $veranstaltung["summary"]);

		//Organizational
		$veranstaltung["organizational"] = str_replace("\n", "<br/>", $veranstaltung["organizational"]);

		//ECTS Summary
		$veranstaltung["ects_summary"] = str_replace("\n", "<br/>", $veranstaltung["ects_summary"]);

		$veranstaltung["ects_infos"] = ($veranstaltung["ects_name"] || $veranstaltung["ects_summary"] || $veranstaltung["ects_literature"]);
		$veranstaltung["zusatzinfos"] = ($veranstaltung["keywords"] || $veranstaltung["turnout"] || $veranstaltung["url_description"]);


		return array( "veranstaltung" => $veranstaltung, "optionen" => $this->optionen);
	}

	private function _str_replace_dict($dict, $str) {
		foreach ($dict as $key => $value) {
			$str = str_replace($key, $value, $str);
		}
		return $str;
	}

	private function _rename_key($search_key, &$arr, $dict) {
            if (is_array($arr)) {
                foreach ($arr as &$veranstaltung) {
                    if (is_array($veranstaltung)) {
                        foreach ($veranstaltung as $key => &$value) {
                            if ($key == $search_key) {
                                $value = $this->_str_replace_dict($dict, $value);
                            }
                        }
                    }
                }
            }
	}

	private function _group_by($key_name, $arr) {

		$gruppen = array();

		$gruppen_dict = array();
		foreach ($arr as $child) {

			$gruppenName = $child[$key_name];

			if($gruppen_dict[$gruppenName]==NULL)
				$gruppen_dict[$gruppenName] = array();
			array_push($gruppen_dict[$gruppenName], $child);
		}

		foreach ($gruppen_dict as $gruppen_name => $gruppen_data) {
			$gruppen_obj = array(
				"title" => $gruppen_name,
				"data" => $gruppen_data
			);

			array_push($gruppen, $gruppen_obj);
		}

		return $gruppen;
	}

	private function umlaute_ersetzen($text){
		$such_array  = array ('ä', 'à', 'á', 'â', 'æ', 'ã', 'å', 'ā',
							  'ö', 'ô', 'ò', 'ó', 'œ', 'ø', 'ō', 'õ',
							  'ü', 'û', 'ù', 'ú', 'ū',
							  'è', 'é', 'ê', 'ë', 'ē', 'ė', 'ę',
							  'ß');
		$ersetzen_array = array ('ae', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
								 'oe', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
								 'ue', 'u', 'u', 'u', 'u',
								 'e', 'e', 'e', 'e', 'e', 'e', 'e',
								 'ss');
		$neuer_text  = str_replace($such_array, $ersetzen_array, $text);
		return $neuer_text;
	}

	private function record_sort($records, $field, $reverse=false) {
	    $hash = array();

	    foreach($records as $record)
	    {
	        $hash[$record[$field]] = $record;
	    }

	    ($reverse)? krsort($hash) : ksort($hash);

	    $records = array();

	    foreach($hash as $record)
	    {
	        $records[] = $record;
	    }

	    return $records;
	}
        
        private function array_orderby(){
		$args = func_get_args();
		$data = array_shift($args);
		foreach ($args as $n => $field) {
			if (is_string($field)) {
				$tmp = array();
				foreach ($data as $key => $row)
					$tmp[$key] = $row[$field];
				$args[$n] = $tmp;
			}
		}
		$args[] = &$data;
		call_user_func_array('array_multisort', $args);
		return array_pop($args);
	}


}

?>