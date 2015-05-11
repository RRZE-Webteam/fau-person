fau-person
============

WordPress Plugin
----------------

Visitenkarten-Plugin für FAU Webauftritte  
Custom Post Type person


####Version 1.0.10:

- Kurzbeschreibung für Listenanzeige wird aus allgemeinem Text generiert, wenn das Feld leer ist (55 Wörter oder bis zum Weiterlesen-Tag)    
- format="shortlist" ergänzt für Auflistung von Titel (Präfix), Vorname, Nachname, Suffix, ggf. Kurzauszug (bei showlist=1)    
- Eingabe mehrerer ids möglich (kommasepariert, z. B. id="42,44,56") für die Anzeige mehrerer Personen mit gleichen Shortcode-Parametern untereinander    

####Version 1.0.9:

- Felder Institution und Abteilung geteilt    
- Feld Mobiltelefon ergänzt
- Eingabefeld für allgemeinen Text (z.B. Lebenslauf, WYSIWYG-Editor), Kurzbeschreibung für Listenanzeige (falls im Shortcode showlist=1 gewählt ist) und Kurzauszug für Sidebaranzeige (falls im Shortcode showsidebar=1 gewählt ist)

#####Shortcode person (css-Klassen an FAU-Webauftritt angepasst)
######Beispiel:  
Titel des eingetragenen Kontaktes = Max Mustermann:  
[person slug='Max Mustermann']  

Kontakte können alternativ auch mit der ID abgerufen werden:
[person id="12345"]

ACHTUNG: In manchen Fällen wird auch bei korrekter Schreibweise der Slug nicht gefunden (z.B. wenn Umlaut beinhaltet ist). Zuverlässiger ist die Anzeige über die ID.


######optionale Parameter:  
- default = TRUE, d.h. nur anzugeben wenn Anzeige nicht gewünscht ist (z.B. showtelefon=0):  
showtelefon  
showtitle  
showsuffix  
showposition  
showinstitution  
showmail  

- default = FALSE, d.h. nur anzugeben wenn Anzeige gewünscht ist (z.B. showfax=1):
showfax *  
showwebsite *  
showaddress *  
showroom *  
showdescription *  
showpubs  
showoffice  
showlink  
extended (fasst alle Parameter mit * zusammen, so dass nur extended=1 angegeben werden muss)


- format = full
Anzeige wie bei einer Kontakt-Einzelseite

#####Vorlage zur Singledarstellung: templates/single-person.php
kann gerne ins eigene Theme übernommen und daran angepasst werden, Vorlage ist an FAU-Webauftritt angepasst
Es wird zuerst im Theme geschaut, ob eine single-person.php vorhanden ist, wenn ja wird die genommen, ansonsten die vom Plugin




