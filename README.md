fau-person
============

WordPress Plugin
----------------

Visitenkarten-Plugin für FAU Webauftritte  
Custom Post Type person


## Systemvoraussetzungen:

PHP-Modules:

* php-xml

## Changelog

[changelog.txt](./changelog.txt)

## Funktionsweise und Verwendung:

- Verfügbare Kontakte werden auf Seiten und Beiträgen mit ihrer ID angezeigt (vereinfachte Suche für Shortcode, Metabox kann auch auf die Seite verschoben werden, Sortierung nach letztem Wort des Kontakttitels - im Normalfall der Nachname)    
- Platzhalterbilder für Einrichtung und geschlechtsneutrale Person vorhanden   
- Kurzbeschreibung für Listenanzeige wird aus allgemeinem Text generiert, wenn das Feld leer ist (55 Wörter oder bis zum Weiterlesen-Tag)    
- `format="shortlist"` für Auflistung von Titel (Präfix), Vorname, Nachname, Suffix, ggf. Kurzauszug (bei showlist=1)    
- Eingabefeld für allgemeinen Text (z.B. Lebenslauf, WYSIWYG-Editor), Kurzbeschreibung für Listenanzeige (falls im Shortcode `showlist=1` gewählt ist) und Kurzauszug für Sidebaranzeige (falls im Shortcode `showsidebar=1` gewählt ist)

### Shortcode: Kontakt (css-Klassen an FAU-Webauftritt angepasst)
  
Kontakte können mit Angabe der WordPress-Kontakt-ID abgerufen werden:

    [kontakt id="12345"]

Eingabe mehrerer ids möglich (kommasepariert, z. B. `id="42, 44, 56"`) für die Anzeige mehrerer Kontakte mit gleichen Shortcode-Parametern     
Für die Anzeige mehrerer Kontakte über Kontakt-Kategorien kann auch Kategorie-Slug gewählt werden (hier `alle-leute`): 

    [kontakt category="alle-leute"]
   
oder 
   
    [kontaktliste category="alle-leute"]    


#### optionale Parameter (Parameter aus früheren Versionen funktionieren noch):  
* `show` (nur anzugeben, wenn ein zusätzliches Feld zu den Standardfeldern angezeigt werden soll), `hide` (nur anzugeben, wenn die Anzeige eines Standardfeldes nicht gewünscht ist)    
Folgende Werte können eingegeben werden:    
`kurzbeschreibung, organisation, abteilung, position, titel, suffix, adresse, raum, telefon, fax, mobil, mail, webseite, mehrlink, kurzauszug, sprechzeiten, publikationen, bild`   
Beispiel: 

    [kontakt id="12345" show="adresse, raum, sprechzeiten" hide="position, telefon"]    

* `format`: je nach Wert unterscheidet sich die Ausgabedarstellung und die angezeigten Standardparameter:    
  * `name`: Ausgabe von Titel, Vorname, Nachname und Suffix (sofern vorhanden) im Fließtext mit Link auf die Kontaktseite der Person    
  * `page`: vollständige Ausgabe des ganzen Kontaktes wie bei der Kontakt-Einzelseite, die Parameter `show` und `hide` haben hierauf keinen Einfluss    
  * `sidebar`: Ausgabe wie bei der Anzeige in der Sidebar im Theme    
  * `liste`: Ausgabe der Namen mit Listenpunkten, unten drunter Kurzbeschreibung    
Beispiel: 

    [kontakt id="42, 44, 56" format="name"]

### Vorlage zur Singledarstellung

    templates/single-person.php

kann gerne ins eigene Theme übernommen und daran angepasst werden, beigefügte Vorlage ist an FAU-Fakultätsthemes angepasst
Es wird zuerst im Theme geschaut, ob eine `single-person.php` vorhanden ist, wenn ja wird die genommen, ansonsten die vom Plugin
