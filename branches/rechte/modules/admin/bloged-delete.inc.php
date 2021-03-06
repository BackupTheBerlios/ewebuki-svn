<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "leer - delete funktion";
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

    86343 Königsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( $cfg["bloged"]["blogs"][make_ebene($environment["parameter"][4])]["right"] == "" ||
         priv_check(make_ebene($environment["parameter"][4]),$cfg["bloged"]["blogs"][make_ebene($environment["parameter"][4])]["right"])
        || ( function_exists(priv_check_old) && priv_check_old("",$cfg["bloged"]["blogs"][make_ebene($environment["parameter"][4])]["right"]) )
       ) {
        $url = make_ebene($environment["parameter"][4]);

        $delete = show_blog($url,$cfg["bloged"]["blogs"][$url]["tags"],"","","",$cfg["bloged"]["blogs"][$url]["sortable"]);

        // ruecksprung finden
        $header = $url;
        if ( $cfg["bloged"]["blogs"][$url]["category"] != "" ) {
            $preg = "(\[".$cfg["bloged"]["blogs"][$url]["category"]."\])(.*)\[\/".$cfg["bloged"]["blogs"][$url]["category"]."\]";
            preg_match("/$preg/U",$delete[1]["all"],$regs);
            $header = $regs[2];
        }

        // fehlermeldungen
        $ausgaben["form_error"] = "";
        $hidedata["delete"]["inhalt"] = $delete[1]["all"];

        // navigation erstellen
        $ausgaben["form_aktion"] = $pathvars["virtual"].$environment["ebene"]."/delete,".$environment["parameter"][1].",".$environment["parameter"][2].",".$environment["parameter"][3].",".$environment["parameter"][4].".html";
        $ausgaben["form_break"] = $pathvars["virtual"].$header.".html";

        // hidden values
        $ausgaben["form_hidden"] = "";
        $ausgaben["form_delete"] = True;

        // was anzeigen
        $mapping["main"] = "-2051315182.delete";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        // ***

        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }
        // +++
        // unzugaengliche #(marken) sichtbar machen

        // das loeschen wurde bestaetigt, loeschen!
        // ***
        if (  $HTTP_POST_VARS["send"] != "" ) {
            // datensatz loeschen
            if ( $ausgaben["form_error"] == "" ) {
                $sql = "UPDATE ".$cfg["bloged"]["db"]["bloged"]["entries"]." SET status = 0 WHERE ".$cfg["bloged"]["db"]["bloged"]["key"]."='".eCRC(make_ebene($environment["parameter"][4])).".".$environment["parameter"][2]."' AND content REGEXP '^\\\[!\\\]'";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $result  = $db -> query($sql);
                if ( !$result ) $ausgaben["form_error"] = $db -> error("#(error_result)<br />");
            }

            // wohin schicken
            if ( $ausgaben["form_error"] == "" ) {
                header("Location: ".$pathvars["virtual"].$header.".html");
            }
        }
        // +++
        // das loeschen wurde bestaetigt, loeschen!

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>