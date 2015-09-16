ACHTUNG! KONFLIKT MIT DEM PLUGIN RRZE-UNIVIS MÖGLICH! DIESES UNBEDINGT VORHER AUF MINDESTENS VERSION 1.0.4 UPDATEN!
AB VERSION 1.2.0 FUNKTIONIERT DIE UNIVIS-ANBINDUNG NUR NOCH, WENN ZUSÄTZLICH DAS PLUGIN univis-data (https://gitlab.rrze.fau.de/rrze-webteam/univis-data) INSTALLIERT IST!

fau-person
============

WordPress Plugin
----------------

Visitenkarten-Plugin für FAU Webauftritte  
Custom Post Type person

####Version 1.2.7:

- Bugfixes (fehlende Anzeige Mehr-Link, falsche Einbindung des Kurzauszugs in Sidebar)      

####Version 1.2.6:

- fehlendes schließendes div ergänzt in Einzeldarstellung     

####Version 1.2.5:

- Auf Kontakt-Detailseite und für Shortcodes mit format="page" doppelte Anzeige der Position entfernt (als Überschrift h2 weg)     
- UnivIS-ID (8-stellige Zahl) und PLZ (5-stellige Zahl) bei Eingabe validiert    

####Version 1.2.4:

- fehlende Rechte auf "Suche nach UnivIS-ID"-Seite ergänzt     
- Sortierung der verfügbaren Kontakte nach Nachname in der Metabox "Kontaktinformationen" bei Seiten und Beiträgen    

####Version 1.2.3:

- fehlenden Widget-Titel hinzugefügt    

####Version 1.2.2:

- fehlerhafter Umbruch nach Shortcode-Parameter format="name" korrigiert    
- TinyMCE-Unterstützung eingebaut (jetzt auch über „Werkzeuge“ Shortcode auswählbar)    
- Hinweistext zu Telefax-Nummer geändert    

####Version 1.2.1:

- Beschleunigter Abruf der Daten bei Anschluss an UnivIS: Über das Plugin univis-data werden in den Tabellen wp_univis und wp_univismeta alle UnivIS-Daten zwischengespeichert.    

####Version 1.1.2:

- doppelte Anzeige der PLZ bei Einbindung aus UnivIS beseitigt    
- Name der Person verlinkt auf ausführliche Kontaktseite der Person, es sei denn, ein anderer "Mehr"-Link ist im Kontakt hinterlegt, dann wird auf diesen verlinkt: Eingabe des "Mehr"-Links in den Bereich "Social Media" verschoben    
- Shortcode-Parameter ergänzt:    
-- show und hide: um Einzelwerte anzeigen zu lassen oder auszublenden (Werte entsprechen den Bezeichnungen der Felder bei im Kontakteingabeformular). Mit show werden die entsprechenden Werte angezeigt, mit hide verborgen:    
   kurzbeschreibung, organisation, abteilung, position, titel, suffix, adresse, raum, telefon, fax, mobil, mail, webseite, mehrlink, kurzauszug, sprechzeiten, publikationen, bild     
   Beispiel: [person id="12345" show="adresse, raum, sprechzeiten" hide="position, telefon"]    
-- format: um verschiedene Ausgabeformate zu erhalten (je nachdem auch entsprechende Felder ein- oder ausgeblendet)    
   name: Ausgabe von Titel, Vorname, Nachname und Suffix (sofern vorhanden) im Fließtext mit Link auf die Kontaktseite der Person    
   page: vollständige Ausgabe des ganzen Kontaktes wie bei der Kontakt-Einzelseite, die Parameter show und hide haben hierauf keinen Einfluss    
   sidebar: Ausgabe wie bei der Anzeige in der Sidebar im Theme    
   liste: Ausgabe der Namen mit Listenpunkten, unten drunter Kurzbeschreibung    

####Version 1.1.0:

- UnivIS-Schnittstelle ergänzt: Bei Eingabe der UnivIS-ID der Person und Aktivieren von "Daten aus UnivIS anzeigen" werden in der Ausgabe die Daten angezeigt, die in UnivIS hinterlegt sind. Die entsprechenden Werte werden unterhalb der Felder angezeigt. Außerdem ist die Suche nach der UnivIS-ID in einem Unterpunkt möglich.    

Funktionsweise:

- Verfügbare Kontakte werden auf Seiten und Beiträgen mit ihrer ID angezeigt (vereinfachte Suche für Shortcode, Metabox kann auch auf die Seite verschoben werden, Sortierung nach Kontakttiteln, als Vornamen)    
- Platzhalterbilder für Einrichtung und geschlechtsneutrale Person vorhanden   
- Kurzbeschreibung für Listenanzeige wird aus allgemeinem Text generiert, wenn das Feld leer ist (55 Wörter oder bis zum Weiterlesen-Tag)    
- format="shortlist" für Auflistung von Titel (Präfix), Vorname, Nachname, Suffix, ggf. Kurzauszug (bei showlist=1)    
- Eingabefeld für allgemeinen Text (z.B. Lebenslauf, WYSIWYG-Editor), Kurzbeschreibung für Listenanzeige (falls im Shortcode showlist=1 gewählt ist) und Kurzauszug für Sidebaranzeige (falls im Shortcode showsidebar=1 gewählt ist)

#####Shortcode person (css-Klassen an FAU-Webauftritt angepasst)
######Beispiel:  
Titel des eingetragenen Kontaktes = Max Mustermann:  
[person slug='Max Mustermann']  

Kontakte können alternativ auch mit der ID abgerufen werden:
[person id="12345"]

Eingabe mehrerer ids möglich (kommasepariert, z. B. id="42, 44, 56") für die Anzeige mehrerer Personen mit gleichen Shortcode-Parametern untereinander     

ACHTUNG: In manchen Fällen wird auch bei korrekter Schreibweise der Slug nicht gefunden (z.B. wenn Umlaut beinhaltet ist). Zuverlässiger ist die Anzeige über die ID.


######optionale Parameter:  
- default = TRUE, d.h. nur anzugeben wenn Anzeige nicht gewünscht ist (z.B. showtelefon=0):  
showtelefon  
showtitle  
showsuffix  
showposition  
showinstitution  
showmail  
showabteilung    

- default = FALSE, d.h. nur anzugeben wenn Anzeige gewünscht ist (z.B. showfax=1):
showfax *  
showwebsite *  
showaddress *  
showroom *  
showdescription *  
showlist    
showsidebar    
showthumb    
showpubs  
showoffice  
showlink  
extended (fasst alle Parameter mit * zusammen, so dass nur extended=1 angegeben werden muss)

- format = full
Anzeige wie bei einer Kontakt-Einzelseite

#####Vorlage zur Singledarstellung: templates/single-person.php
kann gerne ins eigene Theme übernommen und daran angepasst werden, Vorlage ist an FAU-Webauftritt angepasst
Es wird zuerst im Theme geschaut, ob eine single-person.php vorhanden ist, wenn ja wird die genommen, ansonsten die vom Plugin




