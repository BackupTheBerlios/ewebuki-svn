<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "file_verarbeitung";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2006 Werner Ammon ( wa<at>chaos.de )

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


    function file_verarbeitung($destination, $name, $limit, $valid, $root = "", $manual_check = "") {

        global $pathvars, $debugging;

        // hauptverzeichnis kann geaendert werden (wird von fileed-describe genutzt)
        if ( $root != "" ) {
            $document_root = $root;
        } else {
            $document_root = $pathvars["fileroot"];
        }

        // historisch: bei $root fehlte der "/" evtl.
        $document_root = rtrim($document_root,"/")."/";

        // historisch: $destination beginnt oder endet evtl. mit einem "/"
        $destination = trim($destination,"/");
        $destination = $document_root.$destination."/";

        $phpversion = explode(".",PHP_VERSION);
        // php major version muss mindestens 4 sein!
        if ( $phpversion[0] >= 4 ) {

            // quelle der datei-informationen
            if ( $manual_check == "" ) {
                $file = $_FILES[$name];
            } else{
                $file = $manual_check;
            }

            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "version: ".$_SERVER["SERVER_SOFTWARE"].$debugging["char"];
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "file destination: ".$destination.$debugging["char"];
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "file name tmp: ".$file["tmp_name"].$debugging["char"];
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "file name: ".$file["name"].$debugging["char"];
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "file mime type: ".$file["type"].$debugging["char"];
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "file size: ".$file["size"].$debugging["char"];

            // find file basename, extension
            $file_extension = strtolower(substr(strrchr($file["name"],"."),1));
            $file_basename = basename($file["name"],".".$file_extension);

            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "file basename: ".$file_basename.$debugging["char"];
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "file extension: ".$file_extension.$debugging["char"];

            // i don't want jpeg files
            if ( $file_extension == "jpeg" ) $file_extension = "jpg";

            // internal file name
            $file_name = $file_basename.".".$file_extension;

            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "internal file name: ".$file_name.$debugging["char"];

            // php version checken, error-codes seit 4.2.0
            if ( ( $phpversion[0] == 4 && $phpversion[1] >= 2 ) || $phpversion[0] > 4 ) {
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "file error: ".$file["error"].$debugging["char"];
                $array["returncode"] = $file["error"];
            }

            // php 4.2.x fehler ueberschreiben php 4.1.x fehler
            if ( $array["returncode"] == 0 ) {
                if ( is_null($file["name"]) && is_null($file["size"]) ) {
                    $array["returncode"] = 13;
                } elseif ( $file["size"] >= $limit ) {
                    $array["returncode"] = 10;
                } elseif ( !in_array($file_extension, $valid) && !array_key_exists($file_extension, $valid) ) {
                    $array["returncode"] = 11;
                } elseif ( file_exists($destination.$file_name) && $manual_check == "" ) {
                    $array["returncode"] = 12;
                }
            }

            $images = array("gif"  => 1, "jpg"  => 2, "jpeg" => 2, "png"  => 3);
            $weitere = array("pdf" => "%PDF", "zip" => "PK", "odt" => "PK", "ods" => "PK", "odp" => "PK", "bz2" => "BZ", "gz" => chr(hexdec("1F")).chr(hexdec("8B")).chr(hexdec("08")).chr(hexdec("08")));
            // grafik formate testen
            if ( $images[$file_extension] != "" && $array["returncode"] == 0 ) {
                /*
                1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP,
                7 = TIFF(intel byte order), 8 = TIFF(motorola byte order),
                9 = JPC, 10 = JP2, 11 = JPX, 12 = JB2, 13 = SWC, 14 = IFF
                */
                $imgsize = getimagesize($file["tmp_name"]);
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "chk type soll: ".$images[$file_extension].$debugging["char"];
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "chk type ist: ".$imgsize[2].$debugging["char"];
                if ( $images[$file_extension] != $imgsize[2] ) {
                    $array["returncode"] = 11;
                }
            // weitere formate testen
            } elseif ( $weitere[$file_extension] != "" && $array["returncode"] == 0 ) {
                $fp = fopen($file["tmp_name"], "r");
                $buffer = fgets($fp, 5);
                if ( strpos($buffer,$weitere[$file_extension]) === false ) {
                    $array["returncode"] = 11;
                }
                unset($buffer);
                fclose($fp);
            // sonstiges ablehnen
            } elseif ( $array["returncode"] == 0 ) {
                $array["returncode"] = 11;
            }

            if ( $array["returncode"] == 0 ) {
                $MySafeModeUid = getmyuid();
                passthru ("chuid ".$file["tmp_name"]." ".$MySafeModeUid);
                if ( $manual_check == "" ) {
                    move_uploaded_file ($file["tmp_name"], $destination.$file_name);
                } else{
                    copy ($file["tmp_name"], $destination.$file_name);
                }
                chmod($destination.$file_name,0664);
            }
            $array["name"] = $file_name;

        } else {
            $array["returncode"] = 14;
        }
        return $array;
    }

    function file_error($error) {
        if ( $error == 0 ) {
            $meldung .= "There is no error, the file uploaded with success.";
        } elseif ( $error == 1 ) {
            $meldung .= "The uploaded file exceeds the upload_max_filesize directive ( ".get_cfg_var(upload_max_filesize)." ) in php.ini.";
        } elseif ( $error == 2 ) {
            $meldung .= "The uploaded file exceeds the MAX_FILE_SIZE ( ".$_POST["MAX_FILE_SIZE"]."kb ) directive that was specified in the html form.";
        } elseif ( $error == 3 ) {
            $meldung .= "The uploaded file was only partially uploaded.";
        } elseif ( $error == 4 ) {
            $meldung .= "No file was uploaded.";
        } elseif ( $error == 6 ) {
            $meldung .= "Missing a temporary folder.";
        } elseif ( $error == 7 ) {
            $meldung .= "Failed to write file to disk.";
        } elseif ( $error == 8 ) {
            $meldung .= "File upload stopped by extension.";
        } elseif ( $error == 10 ) {
            $meldung .= "File Size to big.";
        } elseif ( $error == 11 ) {
            $meldung .= "File Type not valid.";
        } elseif ( $error == 12 ) {
            $meldung .= "File Name already exists.";
        } elseif ( $error == 13 ) {
            $meldung .= "Unknown Error. Maybe post_max_size directive ( ".get_cfg_var(post_max_size)." ) in php.ini. Please do not try again.";
        } elseif ( $error == 14 ) {
            $meldung .= "Sorry, you need minimal PHP/4.x.x to handle uploads!";
        }
        return $meldung;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
