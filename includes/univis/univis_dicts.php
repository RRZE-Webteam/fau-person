<?php

class Dicts {

    public static $acronyms = array(
        "Dr." => "Doktor",
        "Prof." => "Professor",
        "Dipl." => "Diplom",
        "Inf." => "Informatik",
        "Wi." => "Wirtschaftsinformatik",
        "Ma." => "Mathematik",
        "Ing." => "Ingenieurwissenschaft",
        "B.A." => "Bakkalaureus",
        "M.A." => "Magister Artium",
        "phil." => "Geisteswissenschaft",
        "pol." => "Politikwissenschaft",
        "nat." => "Naturwissenschaft",
        "soc." => "Sozialwissenschaft",
        "techn." => "technische Wissenschaften",
        "vet.med." => "Tiermedizin",
        "med.dent." => "Zahnmedizin",
        "h.c." => "ehrenhalber",
        "med." => "Medizin",
        "jur." => "Recht",
        "rer." => ""
    );
    
    // Reihenfolge wichtig, da sonst Mehrfachersetzung von z.B. sem
    public static $lecturetypen = array(
        "awa" => "Anleitung zu wiss. Arbeiten  (AWA)",
        "ag" => "Arbeitsgemeinschaft  (AG)",
        "ak" => "Aufbaukurs  (AK)",
        "ek" => "Einf&uuml;hrungskurs  (EK)",
        "ex" => "Exkursion  (EX)",
        "gk" => "Grundkurs  (GK)",
        "sem" => "Seminar  (SEM)",
        "ts" => "Theorieseminar  (TS)",
        "es" => "Examensseminar  (ES)",
        "mas" => "MA-Seminar  (MAS)", 
        "as" => "Aufbauseminar  (AS)",
        "gs" => "Grundseminar  (GS)",
        "hs" => "Hauptseminar  (HS)",
        "re" => "Repetitorium  (RE)",
        "kk" => "Klausurenkurs  (KK)",
        "klv" => "Klinische Visite  (KLV)",
        "ko" => "Kolloquium  (KO)",
        "ks" => "Kombiseminar  (KS)",
        "ku" => "Kurs  (KU)",
        "ms" => "Mittelseminar  (MS)",
        "os" => "Oberseminar  (OS)",
        "pr" => "Praktikum  (PR)",
        "prs" => "Praxisseminar  (PRS)",
        "pjs" => "Projektseminar  (PJS)",
        "ps" => "Proseminar  (PS)",
        "sl" => "Sonstige Lehrveranstaltung  (SL)",
        "tut" => "Tutorium  (TUT)",
        "v-ue" => "Vorlesung mit &Uuml;bung  (V/UE)",
        "ue" => "&Uuml;bung  (UE)",
        "vorl" => "Vorlesung  (VORL)",
        "hvl" => "Hauptvorlesung  (HVL)",
        "pf" => "Pr&uuml;fung  (PF)",
        "gsz" => "Gremiensitzung  (GSZ)"
    );
    public static $lecturetypen_short = array(
        "awa" => "Anleitung zu wiss. Arbeiten",
        "ag" => "Arbeitsgemeinschaft",
        "ak" => "Aufbaukurs",
        "ek" => "Einf&uuml;hrungskurs",
        "ex" => "Exkursion",
        "gk" => "Grundkurs",
        "sem" => "Seminar",
        "ts" => "Theorieseminar",
        "es" => "Examensseminar",
        "mas" => "Masterseminar",
        "as" => "Aufbauseminar",
        "gs" => "Grundseminar",
        "hs" => "Hauptseminar",
        "re" => "Repetitorium",
        "kk" => "Klausurenkurs",
        "klv" => "Klinische Visite",
        "ko" => "Kolloquium",
        "ks" => "Kombiseminar",
        "ku" => "Kurs",
        "ms" => "Mittelseminar",
        "os" => "Oberseminar",
        "pr" => "Praktikum",
        "prs" => "Praxisseminar",
        "pjs" => "Projektseminar",
        "ps" => "Proseminar",
        "sl" => "Sonstige Lehrveranstaltung",
        "tut" => "Tutorium",
        "v-ue" => "Vorlesung mit &Uuml;bung",
        "ue" => "&Uuml;bung",
        "vorl" => "Vorlesung",
        "hvl" => "Hauptvorlesung",
        "pf" => "Pr&uuml;fung",
        "gsz" => "Gremiensitzung"
    );
    public static $leclanguages = array(
        "D" => "Deutsch"
    );
    public static $pubtypes = array(
        "artmono" => "Artikel im Sammelband",
        "arttagu" => "Artikel im Tagungsband",
        "artzeit" => "Artikel in Zeitschrift",
        "techrep" => "Interner Bericht (Technischer Bericht, Forschungsbericht)",
        "hschri" => "Hochschulschrift (Dissertation, Habilitationsschrift, Diplomarbeit etc.)",
        "dissvg" => "Hochschulschrift (auch im Verlag erschienen)",
        "monogr" => "Monographie",
        "tagband" => "Tagungsband (nicht im Verlag erschienen)",
        "schutzr" => "Schutzrecht"
    );
    public static $hstypes = array(
        "diss" => "Dissertation",
        "dipl" => "Diplomarbeit",
        "mag" => "Magisterarbeit",
        "stud" => "Studienarbeit",
        "habil" => "Habilitationsschrift",
        "masth" => "Masterarbeit",
        "bacth" => "Bachelorarbeit",
        "intber" => "Interner Bericht",
        "diskus" => "Diskussionspapier",
        "discus" => "Discussion paper",
        "forber" => "Forschungsbericht",
        "absber" => "Abschlussbericht",
        "patschri" => "Patentschrift",
        "offenleg" => "Offenlegungsschrift",
        "patanmel" => "Patentanmeldung",
        "gebrmust" => "Gebrauchsmuster"
    );

}

?>