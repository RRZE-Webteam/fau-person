fau-person
============

WordPress Plugin
----------------

Visitenkarten-Plugin für FAU Webauftritte  
Custom Post Type person

####Achtung:
Es kann zu Kompatibiltätsproblemen beim gleichzeitigen Einsatz mit dem FAU-Plugin kommen!

####Version 1.0:

#####Shortcode person (css-Klassen an FAU-Webauftritt angepasst)
######Beispiel:  
Titel der eingetragenen Person = Max Mustermann:  
[person slug='Max Mustermann']  
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

#####Vorlage zur Singledarstellung: templates/single-person.php
kann gerne ins eigene Theme übernommen und daran angepasst werden, Vorlage ist an FAU-Webauftritt angepasst
Es wird zuerst im Theme geschaut, ob eine single-person.php vorhanden ist, wenn ja wird die genommen, ansonsten die vom Plugin




