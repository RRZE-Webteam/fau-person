fau-person
============

WordPress Plugin
----------------

Visitenkarten-Plugin für FAU Webauftritte  
Custom Post Type person


####Version 1.0.8:

#####Shortcode person (css-Klassen an FAU-Webauftritt angepasst)
######Beispiel:  
Titel der eingetragenen Person = Max Mustermann:  
[person slug='Max Mustermann']  

Personen können alternativ auch mit der ID abgerufen werden:
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
Anzeige wie bei einer Personen-Einzelseite

#####Vorlage zur Singledarstellung: templates/single-person.php
kann gerne ins eigene Theme übernommen und daran angepasst werden, Vorlage ist an FAU-Webauftritt angepasst
Es wird zuerst im Theme geschaut, ob eine single-person.php vorhanden ist, wenn ja wird die genommen, ansonsten die vom Plugin




