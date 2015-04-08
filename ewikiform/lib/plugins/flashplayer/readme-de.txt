FlashPlayer Plugin für DokuWiki 2008-05-05 oder neuer
Version 2010-10-24

Aktuelle Version unter http://arnowelzel.de/wiki/misc/flashplayer

Verwendet den JW FLV Player 5.3.1397,
siehe http://www.jeroenwijering.com/?item=JW_FLV_Player

Auf Grundlage der Arbeit von Sam Hall (http://www.dokuwiki.org/plugin:flashplayer),
erweitert von Arno Welzel (http://arnowelzel.de).

Lizensiert unter der Creative Commons Lizenz BY-NC-SA
Siehe auch http://creativecommons.org/licenses/by-nc-sa/3.0/deed.de


Installation
------------

Alle Dateien  in ein Verzeichnis "flashplayer" innerhalb des
plugin-Verzeichnisses der eigenen DokuWiki-Installation kopieren

WICHTIG: Bei Update von einer älteren Version die folgende Zeile im
HEAD-Abschnitt des Templates wieder ENTFERNEN:

<script type="text/javascript" src="<?php echo DOKU_BASE?>lib/plugins/flashplayer/player/swfobject.js"></script>

Dies ist seit Version 2008-10-12 NICHT mehr notwendig!


Verwendung
----------

Um ein Flash Video innerhalb einer Wiki-Seite einzubinden, ist der
folgende Code zu verwenden:

<flashplayer width="Breite in Pixeln" height="Höhe in Pixeln" position="0|1|2">Flash Variablen</flashplayer>

Mit "position" kann (optional) die Ausrichtung festgelegt werden:

0 - linksbündig
1 - zentriert
2 - rechtsbündig

Derzeit ist es noch nicht vorgesehen Text um das Video herumfliessen zu lassen.

Beispiel - anzeigen eines Videos "/demo.flv" mit 480*380 Pixeln, verwenden
von "/demo.jpg" als Vorschaubild:

<flashplayer width="480" height="380">file=/demo.flv&image=/demo.jpg</flashplayer>

Die Flash Variablen geben dem Player an, welche Datei abgespielt werden soll
und welches Bild als Vorschau benutzt werden soll. Man kann auch nur ein Video
ohne Vorschaubild angeben:

<flashplayer width="480" height="380">file=/demo.flv</flashplayer>

Eine detaillierte Beschreibung aller möglichen Flash Variablen findet
man auf http://code.jeroenwijering.com/trac/wiki/FlashVars.


Hinzufügen weiterer Sprachen
----------------------------

Momentan unterstützt das flashplayer-Plugin nur Englisch und Deutsch.

Wenn man weitere Sprachen hinzufügen möchte, sollte man sich mal das
"lang"-Verzeichnis ansehen. Hier kann man einfach die Sprache hinzufügen,
die man benötigt, indem man eine vorhandene Sprache kopiert (entsprechend
der Konventionen im Verzeichnis "/inc/lang" von DokuWiki) und die
entsprechende "lang.php"-Datei übersetzen - vorsicht, es handelt sich um
PHP-Code und die Datei muß im UTF-8-Format abgespeichert werden!


Danksagungen
------------

Jeroen Wijering für seinen JW Player
Sam Hall für die erste Version dieses Plugins


Historie
--------

2008-09-21  - Plugin von Sam Hall angepasst, um valides XHTML zu erzeugen
              und Verwendung von SWFObjects.

2008-10-12  - SWFObjects in Plugin Script-Datei verschoben, so daß dieses
              Script nicht mehr manuell im DokuWiki template eingebunden
              werden muß.

2009-01-17  - JW FLV Player auf version 4.3.132 aktualisiert.

2009-06-14  - Parameter "position" hinzugeügt.

2009-12-25  - JW FLV Player auf Version 5.0.753 aktualisiert.

2010-02-26  - SWFObjects wieder entfernt und auf rein statische Ausgabe umgebaut

2010-02-26a - OBJECT-Element angepasst, damit es auch mit Webkit (Chrome, Safari) funktioniert

2010-05-15  - JW FLV Player auf Version 5.1.897 aktualisiert.

2010-10-24  - JW FLV Player auf version 5.3.1397 aktualisiert.
