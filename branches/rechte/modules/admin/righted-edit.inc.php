<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "righted - edit funktion";
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

    86343 Koenigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $url = make_ebene($environment["parameter"][1]);

    if ( $cfg["righted"]["right"] == "" || priv_check('', $cfg["righted"]["right"] ) ) {

        // bauen der legende
        foreach ( $cfg["righted"]["button"]  as $key => $value ) {
            switch ($key) {
                case "new":
                    $dataloop["legende"][$key]["color"] = $value["color"];
                    $dataloop["legende"][$key]["text"] = "Kein Recht";
                    break;
                case "add":
                    $dataloop["legende"][$key]["color"] = $value["color"];
                    $dataloop["legende"][$key]["text"] = "Zugewiesenes Recht";
                    break;
                case "del":
                    $dataloop["legende"][$key]["color"] = $value["color"];
                    $dataloop["legende"][$key]["text"] = "Negiertes Recht";
                    break;
            }
        } 

        // ausgeben der url wo man sich gerade befindet
        $ausgaben["url"] = $url;

        $url2 = $url;
        $infos = "";
        // erstellen der info - box
        $infos = priv_info($url,$infos);

        // wenn parameter 2 gesetzt ist, info-box oeffnen
        if ( $environment["parameter"][2] != "" ) {
            $ausgaben["display"] = "visible";
        } else {
            $ausgaben["display"] = "none";
        }

        // holen aller rechte
        $sql ="SELECT * FROM ".$cfg["righted"]["db"]["priv"]["entries"];
        $result = $db -> query($sql);
        while ( $all = $db -> fetch_array($result,1) ) {
            $all_rights[] = $all[$cfg["righted"]["db"]["priv"]["name"]];
        }

        // holen aller gruppen
        $sql ="SELECT * FROM ".$cfg["righted"]["db"]["group"]["entries"];
        $result = $db -> query($sql);
        while ( $all = $db -> fetch_array($result,1) ) {
            $all_groups[$all[$cfg["righted"]["db"]["group"]["key"]]] = $all[$cfg["righted"]["db"]["group"]["name"]];
        }

        $infos = array_reverse($infos);

        $counter = 0;
        foreach ( $all_groups as $group_key => $group_value ) {
            $counter++;
            $dataloop["infos"][$counter]["url"] = " name:".$group_value;
            foreach ( $all_rights as $rights_value ) {
                $background = $cfg["righted"]["button"]["new"]["color"];
                $name = "new";
                foreach ( $infos[$specialvars["dyndb"]] as $info_key => $info_value ) {

                    if ( is_array($info_value["add"]) ) {
                        if ( preg_match("/".$rights_value.",/",$info_value["add"][$group_value]) ) {
                            $background = $cfg["righted"]["button"]["add"]["color"];
                            $name = "add";
                        } 
                    }
                    if ( is_array($info_value["del"]) ) {
                        if ( preg_match("/".$rights_value.",/",$info_value["del"][$group_value]) ) {
                            $background = $cfg["righted"]["button"]["del"]["color"];
                            $name = "del";
                        } 
                    }
                }
                $dataloop["infos"][$counter]["info"] .= "<input name=\"".$name."#".$group_key."\" value=\"".$rights_value."\" style=width:35px;background:".$background." type=\"submit\"></input>";
            }
        }

        // form options holen
        $form_options = form_options(eCRC($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        $element = form_elements( $cfg["righted"]["db"]["content"]["entries"], $form_values );

        // form elemente erweitern
        $element["extension1"] = "<input name=\"extension1\" type=\"text\" maxlength=\"5\" size=\"5\">";
        $element["extension2"] = "<input name=\"extension2\" type=\"text\" maxlength=\"5\" size=\"5\">";

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["righted"]["basis"]."/edit,".$environment["parameter"][1].",".$environment["parameter"][2].",verify.html";
        $sql = "SELECT refid FROM site_menu WHERE mid=".$environment["parameter"][1];
        $result = $db -> query($sql);
        $data = $db -> fetch_array($result,1);
        $ausgaben["form_break"] = $pathvars["virtual"]."/admin/menued/list,".$data["refid"].".html";

        // hidden values
        $ausgaben["form_hidden"] .= "";

        // was anzeigen
        $mapping["main"] = eCRC($environment["ebene"]).".modify";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
            $ausgaben["inaccessible"] .= "# (error_dupe) #(error_dupe)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        #n/a

        // +++
        // page basics
        if ( $environment["parameter"][3] == "verify" && preg_match("/^new#|^del#|^add#/",key($HTTP_POST_VARS)) ){

            // form eingaben pruefen
            form_errors( $form_options, $HTTP_POST_VARS );

            // evtl. zusaetzliche datensatz aendern
            if ( $ausgaben["form_error"] == ""  ) {
                if ( $error ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
            }

            // datensatz aendern
            if ( $ausgaben["form_error"] == ""  ) {

                // recht hinzufuegen
                $raute = strpos(key($HTTP_POST_VARS),"#");
                $gruppe = substr(key($HTTP_POST_VARS),$raute+1);
                $recht = $HTTP_POST_VARS[key($HTTP_POST_VARS)];
                
                $sql = "SELECT pid FROM auth_priv WHERE priv='".$recht."'";
                $result = $db -> query($sql);
                $data_priv = $db -> fetch_array($result,1);
                if ( substr(key($HTTP_POST_VARS),0,$raute) == "add" ) {
                    $sql = "SELECT * FROM auth_content WHERE gid='".$gruppe."' AND pid='".$data_priv["pid"]."' AND db='".$specialvars["dyndb"]."' AND tname='".$url."' AND neg !='-1'";
                    $result_pruef = $db -> query($sql);
                    $treffer = $db -> num_rows($result_pruef,1);
                    if ( $treffer == 1 && $url == $url2 ) {
                        $sql = "DELETE FROM auth_content WHERE gid='".$gruppe."' AND pid='".$data_priv["pid"]."' AND tname='".$url."' AND neg!='-1'";
                    } else {
                        $sql = "INSERT INTO auth_content (gid,pid,db,tname,neg,ebene,kategorie) VALUES ('".$gruppe."','".$data_priv["pid"]."','".$specialvars["dyndb"]."','".$url."','-1','','')";
                    }
                } elseif ( substr(key($HTTP_POST_VARS),0,$raute) == "del" ) {
                    $sql = "DELETE FROM auth_content WHERE gid='".$gruppe."' AND pid='".$data_priv["pid"]."' AND tname='".$url."' AND neg='-1'";
                } else {
                    $sql = "INSERT INTO auth_content (gid,pid,db,tname,ebene,kategorie) VALUES ('".$gruppe."','".$data_priv["pid"]."','".$specialvars["dyndb"]."','".$url."','','')";  
                }
                $result = $db -> query($sql);

                if ( $header == "" ) $header = $cfg["righted"]["basis"]."/edit,".$environment["parameter"][1].",".$environment["parameter"][2].".html";
            }

            // wenn es keine fehlermeldungen gab, die uri $header laden
            if ( $ausgaben["form_error"] == "" ) {
                header("Location: ".$header);
            }
        }
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>