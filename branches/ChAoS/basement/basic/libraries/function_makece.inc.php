<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "content editor erstellen";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001, 2002, 2003 Werner Ammon <wa@chaos.de>

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

    function makece($ce_formname, $ce_name, $ce_inhalt) {
        global $db, $pathvars, $ausgaben, $extension;
        $ausgaben["ce_name"] = $ce_name;

        // vogelwilde regex die viel arbeit erspart hat
        preg_match_all("/_([0-9]*)./",$ce_inhalt,$found);
        #$array = array_merge(explode(";",$fileid),$found[1]);

        // file memo auslesen und zuruecksetzen
        global $HTTP_SESSION_VARS;
        session_register("images_memo");
        if ( is_array($HTTP_SESSION_VARS["images_memo"]) ) {
            $array = array_merge($HTTP_SESSION_VARS["images_memo"],$found[1]);
            unset($HTTP_SESSION_VARS["images_memo"]);
        } else {
            $array = $found[1];
        }

        // wenn es thumbnails gibt, anzeigen
        if ( count($array) >= 1 ) {
            foreach ( $array as $value ) {
                if ( $where != "" ) $where .= " OR ";
                $where .= "fid = '".$value."'";
            }
            $sql = "SELECT * FROM site_file WHERE ".$where;
            $result = $db -> query($sql);
            $extension= "";

            $tn = "<table><tr><td>";
            while ( $data = $db -> fetch_array($result, NOP) ) {
                #$file[$data["fid"]] = array(
                #                        "fart"  =>  $data["ffart"],
                #                        "fdesc" =>  $data["fdesc"],
                #                        );
                switch ( $data["ffart"] ) {
                    case "pdf":
                        #$tn .= "<a href=\"#\" onclick=\"INSst('doc".$data["fid"]."','".$ce_formname."','".$ce_name."')\"><img src=\"".$pathvars["images"]."pdf.png"."\"></a> ";
                        $tn .= "<table align=\"left\">";
                        $tn .= "<tr><td><a href=\"#\" onclick=\"INSst('doc".$data["fid"]."','".$ce_formname."','".$ce_name."')\">PDF</a></td></tr>";

                        $tn .= "<tr><td><img src=\"".$pathvars["images"]."pdf.png"."\"></td></tr>";
                        $tn .= "</table>";

                        $extension .= "else if (st=='doc".$data["fid"]."')\nst='[LINK=/dateien/dokumente/doc_".$data["fid"].".".$data["ffart"]."]".$data["fdesc"]."[/LINK]';";
                        break;
                    default:
                        $imgsize = getimagesize($pathvars["filebase"]["maindir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["tn"]."tn_".$data["fid"].".".$data["ffart"]);
                        $imgsize = " ".$imgsize[3];
                        #$tn .= "<a href=\"#\" onclick=\"INSst('imb".$data["fid"]."','".$ce_formname."','".$ce_name."')\"><img src=\"/dateien/bilder/thumbnail/tn_".$data["fid"].".".$data["ffart"]."\"></a> ";
                        $tn .= "<table align=\"left\">";
                        $tn .= "<tr><td><a href=\"#\" onclick=\"INSst('imo".$data["fid"]."','".$ce_formname."','".$ce_name."')\">Org</a> ";
                        $tn .= "<a href=\"#\" onclick=\"INSst('imb".$data["fid"]."','".$ce_formname."','".$ce_name."')\">Big</a> ";
                        $tn .= "<a href=\"#\" onclick=\"INSst('imm".$data["fid"]."','".$ce_formname."','".$ce_name."')\">Mid</a> ";
                        $tn .= "<a href=\"#\" onclick=\"INSst('ims".$data["fid"]."','".$ce_formname."','".$ce_name."')\">Sma</a></td></tr>";

                        $tn .= "<tr><td><a href=\"#\" onclick=\"INSst('imo".$data["fid"]."','".$ce_formname."','".$ce_name."')\"><img".$imgsize." border=\"0\" src=\"".$pathvars["filebase"]["webdir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["tn"]."tn_".$data["fid"].".".$data["ffart"]."\"></a></td></tr>";
                        $tn .= "</table>";

                        $extension .= "else if (st=='imo".$data["fid"]."')\nst='[IMG=".$pathvars["filebase"]["webdir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["o"]."img_".$data["fid"].".".$data["ffart"]."]".$data["fdesc"]."[/IMG]';";
                        $extension .= "else if (st=='imb".$data["fid"]."')\nst='[IMG=".$pathvars["filebase"]["webdir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["b"]."img_".$data["fid"].".".$data["ffart"]."]".$data["fdesc"]."[/IMG]';";
                        $extension .= "else if (st=='imm".$data["fid"]."')\nst='[IMG=".$pathvars["filebase"]["webdir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["m"]."img_".$data["fid"].".".$data["ffart"]."]".$data["fdesc"]."[/IMG]';";
                        $extension .= "else if (st=='ims".$data["fid"]."')\nst='[IMG=".$pathvars["filebase"]["webdir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["s"]."img_".$data["fid"].".".$data["ffart"]."]".$data["fdesc"]."[/IMG]';";
                }
                $i++;
                $a = $i / 6;
                if ( is_int($a) ) $tn .="</td></tr><tr><td>";
            }
            $tn .= "</td></tr></table>";
        }

        $ausgaben["ce_script"] = parser("cms.edit-js","");
        $cetag = array( "b"     => array( "1", "Fett"),
                        "i"     => array( "1", "Kursiv"),
                        "cent"  => array( "1", "Zentrieren"),
                        "bi"    => array( "",  "Fett und Kursiv"),
                        "q"     => array( "",  "Anf�hrungszeichen"),
                        "c"     => array( "",  "Zitat"),
                        "ul"    => array( "1", "Normale Liste"),
                        "ol"    => array( "",  "Sortierte Liste"),
                        "img"   => array( "",  "Bild einf�gen"),
                        "imgb"  => array( "",  "Bild mit Beschriftung einf�gen"),
                        "link"  => array( "",  "Link einf�gen"),
                        "linkb" => array( "1", "Link �ber einf�gen"),
                        "e"     => array( "",  "E-Mail einf�gen"),
                        "eb"    => array( "1", "E-Mail �ber einf�gen"),
                        "h1"    => array( "1", "Haupt�berschrift einf�gen"),
                        "h2"    => array( "1", "Unter�berschrift einf�gen"),
                        "hl"    => array( "1", "Waagrechte Linie"),
                        "tab"   => array( "1", "Tabelle einf�gen"),
                        "row"   => array( "1", "Zeile einf�gen"),
                        "col"   => array( "1", "Spalte einf�gen"),
                        "int"   => array( "",  "Intelligenter Link"),
                      );

        $ausgaben["ce_dropdown"]  = "<select style=\"width:95px;font-family:Helvetica, Verdana, Arial, sans-serif;font-size:12px;\" name=\"st\" size=\"1\" onChange=\"INSst(this.options[this.selectedIndex].value,'".$ce_formname."','".$ce_name."')\">";
        $ausgaben["ce_dropdown"] .= "<option value=\"\">TAG Select</option>";
        foreach( $cetag as $key => $value ) {
            if ( $value[0] == 1 ) {
                $ausgaben["ce_button"] .= "<a href=\"#\" onclick=\"INSst('".$key."','".$ce_formname."','".$ce_name."')\"><img src=\"".$pathvars["images"]."cms-tag-".$key.".png\" alt=\"".$value[1]."\" width=\"23\" height=\"22\" border=\"0\"></a> ";
            }
            $ausgaben["ce_dropdown"] .= "<option value=\"".$key."\">".$value[1]."</option>";
            #ce_anker
        }
        $ausgaben["ce_dropdown"] .= "</select>";
        $ausgaben["ce_button"] .= "<input name=\"image\" type=\"image\" id=\"image\" value=\"add\" src=\"".$pathvars["images"]."cms-tag-imgb.png\" width=\"23\" height=\"22\">";

        $ausgaben["ce_upload"] .= "<select style=\"width:95px;font-family:Helvetica, Verdana, Arial, sans-serif;font-size:12px;\" name=\"upload\" onChange=\"submit()\">";
        $ausgaben["ce_upload"] .= "<option value=\"\">Upload</option>";
        $ausgaben["ce_upload"] .= "<option value=\"1\">1 Datei</option>";
        $ausgaben["ce_upload"] .= "<option value=\"2\">2 Dateien</option>";
        $ausgaben["ce_upload"] .= "<option value=\"3\">3 Dateien</option>";
        $ausgaben["ce_upload"] .= "<option value=\"4\">4 Dateien</option>";
        $ausgaben["ce_upload"] .= "<option value=\"5\">5 Dateien</option>";
        $ausgaben["ce_upload"] .= "</select>";

        $ausgaben["ce_inhalt"] = $ce_inhalt;
        $ausgaben["ce_eventh"] = "onKeyDown=\"zaehler();\" onSelect=\"chk('content',500);\"";
        return $tn;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
