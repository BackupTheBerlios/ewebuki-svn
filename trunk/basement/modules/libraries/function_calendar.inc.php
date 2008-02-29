<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: function_calendar.inc.php 1131 2007-12-12 08:45:50Z chaot $";
// "funktion loader";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2007 Werner Ammon ( wa<at>chaos.de )

    This script is a part of eWeBuKi

    eWeBuKi is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    eWeBuKi is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with eWeBuKi; If you did not, you may download a copy at:

    URL:  http://www.gnu.org/licenses/gpl.txt

    You may also request a copy from:

    Free Software Foundation, Inc.
    59 Temple Place, Suite 330
    Boston, MA 02111-1307
    USA

    You may contact the author/development team at:

    Chaos Networks
    c/o Werner Ammon
    Lerchenstr. 11c

    86343 K�nigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function calendar($tag="",$class="") {

    $tage = array("So", "Mo", "Di", "Mi","Do", "Fr", "Sa");
    setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu');
    if ( $tag == "" ) {
        $heute = getdate();
    } else {
        $heute = getdate($tag);
    }

    // einige daten die spaeter vielleicht noch nuetzlich sind :)
    $tage_monat = $heute["mday"];
    $wochentag_ziffer = $heute["wday"];
    $wochentag = $heute["weekday"];
    $monat = $heute["month"];
    $monat_id = $heute["mon"];
    $jahr = $heute["year"];

    // start-tag
    $start = mktime ( 0, 0, 0, $monat_id, 1, $jahr );
    $start = getdate($start);
    $start =  $start["wday"];
    // start-tag

    $ausgabe = "<table class=\"".$class."\">";
    $counter=0;
    $int_counter = "";

    // bauen er tabellenbeschriftung
    $ausgabe .= "<thead><tr><th colspan=\"7\" scope=\"col\" class=\"monat\">".strftime ("%B", $heute[0])."</th></tr>";
    $ausgabe .= "<tr>";
    foreach ( $tage as $key => $value ) {
        // ersten und letzten tag kennzeichnen
        if ( $key == 0 ) {
            $class = "first";
        } elseif ( $key == 6 ) {
            $class = "last";
        } else {
            $class = "";
        }
        $ausgabe .= "<th scope=\"col\" class=\"".$class."\">".$value."</th>";
    }
    $ausgabe .= "</tr></thead>\n";
    // bauen er tabellenbeschriftung

    $ausgabe .= "<tbody>";
    while ( $stop != "-1" ) {
        $ausgabe .= "<tr>";
        foreach ( $tage as $key => $value ) {
            // ersten und letzten tag kennzeichnen
            if ( $key == 0 ) {
                $class = "first";
            } elseif ( $key == 6 ) {
                $class = "last";
            } else {
                $class = "";
            }
            $counter++;
            if ( $counter > $start && $counter <= ($heute["mday"]+$start) ) {
                $int_counter++;
            } else {
                $int_counter = "";
            }
            $ausgabe .= "<td class=\"".$class."\">".$int_counter."</td>";
        }
        $ausgabe .= "</tr>";
        if ( $counter >= $tage_monat) $stop = -1;
    }
    $ausgabe .= "</tbody>";
    $ausgabe .= "</table>";

    return $ausgabe;

}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>