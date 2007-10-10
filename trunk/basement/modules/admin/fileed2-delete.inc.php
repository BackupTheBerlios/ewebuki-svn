<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: leer-delete.inc.php 667 2007-08-10 18:13:18Z chaot $";
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

    86343 K�nigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( $cfg["right"] == "" || $rechte[$cfg["right"]] == -1 ) {

        // funktions bereich fuer erweiterungen
        // ***

        // falls keine dateien ausgewaehlt wurden, zurueck zur liste
        if ( count($_SESSION["file_memo"]) == 0 ) {
            header("Location: ".$cfg["basis"]."/list.html");
        }

        // +++
        // funktions bereich fuer erweiterungen

        // datensatz holen
        $sql = "SELECT *
                  FROM ".$cfg["db"]["file"]["entries"]."
                 WHERE ".$cfg["db"]["file"]["key"]." IN (".implode(",",$_SESSION["file_memo"]).")";
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
        $result = $db -> query($sql);
        while ( $data = $db -> fetch_array($result,1) ) {

            $error = del_check($data["fid"]);

            $item = $data["ffname"];

            if ( $cfg["filetyp"][$data["ffart"]] == "img" ){
                $link = $cfg["basis"]."/delete/view,o,".$data["fid"].".html";
            }else{
                $link = $pathvars["filebase"]["webdir"].$data["ffart"]."/".$data["fid"]."/".$data["ffname"];
            }

            $reason  = "";
            $checked = "";

            if ( $error == 0 ){
                // keine fehler
                $loop_name = "possible2delete";
                /* checkbox ggf ankreuzen */
                if ( !$_POST || isset($_POST["delete_cb"][$data["fid"]]) ){
                    $checked = ' checked="true"';
                }
            }elseif( $error > 100 ){
                // warnungen
                if ( $error == 101 ){
                    $reason = "#(group_warning)".implode("<br />",$arrError);
                }
                $loop_name = "warning";
                /* checkbox ggf ankreuzen */
                if ( !$_POST || isset($_POST["delete_cb"][$data["fid"]]) ){
                    $checked = ' checked="true"';
                }
            }else{
                // fehler
                if ( $error == 1 ){
                    $reason = "#(user_error)";
                }elseif ( $error == 2 ){
                    $reason = "#(content_error)".implode("<br />",$arrError);
                }
                $loop_name = "impossible2delete";
            }

            $hidedata[$loop_name][0] = -1;
            $dataloop[$loop_name][] = array(
                    "id" => $data["fid"],
                  "item" => $item,
                  "link" => $link,
                "reason" => $reason,
               "checked" => $checked
            );

        }

        $ausgaben["output"] .= $ausgaben["reference"];

        // funktions bereich fuer erweiterungen
        // ***

        ### put your code here ###

        // +++
        // funktions bereich fuer erweiterungen

        // page basics
        // ***

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["basis"]."/delete.html";
        $ausgaben["form_break"] = $cfg["basis"]."/list.html";

        // hidden values
        $ausgaben["form_hidden"] = "";
        $ausgaben["form_delete"] = "true";

        // was anzeigen
        $mapping["main"] = crc32($environment["ebene"]).".delete";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        // ***
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (user_error) #(user_error)<br />";
            $ausgaben["inaccessible"] .= "# (content_error) #(content_error)<br />";
            $ausgaben["inaccessible"] .= "# (group_warning) #(group_warning)<br />";
            $ausgaben["inaccessible"] .= "# (delete_error) #(delete_error)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }
        // +++
        // unzugaengliche #(marken) sichtbar machen

        // wohin schicken
        #n/a

        // +++
        // page basics


        // das loeschen wurde bestaetigt, loeschen!
        // ***
        if ( $_POST["delete"] != ""
            && $_POST["send"] != "" ) {

            foreach ( $_SESSION["file_memo"] as $value ){

                // test, ob datei zum loeschen ausgewaehlt wurde
                if ( isset($_POST["delete_cb"][$value]) ){

                    // test, ob datei geloescht werden darf
                    $error = del_check($value);

                    if ( ($error == 0 || $error > 100) ){
                        // feststellen ob es ein bild ist
                        $sql = "SELECT ffart, fuid FROM site_file WHERE fid =".$value;
                        $result = $db -> query($sql);
                        $data = $db -> fetch_array($result,1);

                        $type = $cfg["filetyp"][$data["ffart"]];
                        if ( $type == "img" ) {

                            $art = array( "o" => "img", "s" => "img", "m" => "img", "b" => "img", "tn" => "tn" );
                            foreach ( $art as $key => $pre ) {
                                $return = unlink($cfg["fileopt"][$type]["path"].$pathvars["filebase"]["pic"][$key].$pre."_".$value.".".$data["ffart"]);
                                ### sollte evtl. anderst gel�st werden, existiert nur ein file nicht
                                ### laesst sich der datensatz nie l�schen!
                                if ( $return != 1 ) {
                                    $ausgaben["form_error"] = "#(delete_error)";
                                    break;
                                }
                            }
                        } else {
                            $return = unlink($cfg["fileopt"][$type]["path"].$cfg["fileopt"][$type]["name"]."_".$value.".".$data["ffart"]);
                            if ( $return != "1" ) {
                                $ausgaben["form_error"] = "#(delete_error)";
                            }
                        }

                        // datensatz loeschen
                        if ( $ausgaben["form_error"] == "" ) {
                            $sql = "DELETE FROM ".$cfg["db"]["file"]["entries"]."
                                        WHERE ".$cfg["db"]["file"]["key"]."='".$value."';";
                            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                            $result  = $db -> query($sql);
                            if ( !$result ) $ausgaben["form_error"] = $db -> error("#(error_result1)<br />");
                        }
                    }

                }

                unset ($_SESSION["file_memo"][$value]);
            }

            // wohin schicken
            if ( $ausgaben["form_error"] == "" ) {
                header("Location: ".$cfg["basis"]."/list.html");
            }
        }
        // +++
        // das loeschen wurde bestaetigt, loeschen!

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>