<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "file_verarbeitung";
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

    86343 Königsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    function file_verarbeitung($destination, $name, $limit, $valid, $root = "") {

        global $debugging;

        // document root selbstaendig setzen und abschliesenden / korrigieren
        if ( $root == "" ) {
            if ( substr($_SERVER["DOCUMENT_ROOT"],-1,1) == "/" ) {
                $document_root = substr($_SERVER["DOCUMENT_ROOT"],0,-1);
            } else {
                $document_root = $_SERVER["DOCUMENT_ROOT"];
            }
        } else {
            $document_root = $root;
        }


        // php major version muss 4 sein!
        if ( substr(strstr($_SERVER["SERVER_SOFTWARE"],"PHP"),4,1) == 4 ) {

            $destination = $document_root.$destination."/";
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "version: ".$_SERVER["SERVER_SOFTWARE"].$debugging["char"];
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "file destination: ".$destination.$debugging["char"];
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "file name tmp: ".$_FILES[$name]["tmp_name"].$debugging["char"];
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "file name: ".$_FILES[$name]["name"].$debugging["char"];
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "file mime type: ".$_FILES[$name]["type"].$debugging["char"];
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "file size: ".$_FILES[$name]["size"].$debugging["char"];

            if ( substr($_FILES[$name]["name"],-4,1) == "." ) {
                $dateiendung = substr($_FILES[$name]["name"],-3,3);
            } else {
                $dateiendung = substr($_FILES[$name]["name"],-4,4);
            }
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "file extension: ".$dateiendung.$debugging["char"];

            // php minor version auf oder >= 2 pruefen
            if ( substr(strstr($_SERVER["SERVER_SOFTWARE"],"PHP"),6,1) >= 2 ) {
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "file error: ".$_FILES[$name]["error"].$debugging["char"];
                $array["returncode"] = $_FILES[$name]["error"];
            }

            // php 4.2.x fehler ueberschreiben php 4.1.x fehler
            if ( $array["returncode"] == 0 ) {
                if ( is_null($_FILES[$name]["name"]) && is_null($_FILES[$name]["size"]) ) {
                    $array["returncode"] = 10;
                } elseif ( $_FILES[$name]["size"] >= $limit ) {
                    $array["returncode"] = 7;
                } elseif ( !in_array($dateiendung, $valid) ) {
                    $array["returncode"] = 8;
                } elseif ( file_exists($destination.$_FILES[$name]["name"]) ) {
                    $array["returncode"] = 9;
                }
            }

            if ( $array["returncode"] == 0 ) {
                $MySafeModeUid = getmyuid();
                passthru ("chuid ".$_FILES[$name]["tmp_name"]." ".$MySafeModeUid);
                move_uploaded_file ($_FILES[$name]["tmp_name"], $destination.$_FILES[$name]["name"]);
                @chmod($destination.$_FILES[$name]["name"],0660);
            }
            $array["name"] = $_FILES[$name]["name"];

        } else {
            $array["returncode"] = 11;
        }
        return $array;
    }


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
