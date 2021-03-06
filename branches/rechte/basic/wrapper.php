<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "file wrapper";
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
    # http://dev2/file/jpg/6/o/filename.extension

    $pathvars["fileroot"] = dirname(dirname(__FILE__))."/";

    require $pathvars["fileroot"]."conf/site.cfg.php";
    require $pathvars["fileroot"]."conf/file.cfg.php";

    // subdir support
    $specialvars["subdir"] = trim(dirname(dirname($_SERVER["SCRIPT_NAME"])),"/");
    if ( $specialvars["subdir"] != "" ) {
        $value = str_replace( $specialvars["subdir"]."/", "", $_SERVER["REQUEST_URI"] );
    } else {
        $value = $_SERVER["REQUEST_URI"];
    }

    $value = explode("/",$value);

    if ( $value[6] == "d" ) {
        echo "<pre>";
        print_r($cfg["file"]["base"]);
        print_r($value);
        echo "</pre>";
    }


    // path finden
    $path["img"] = $cfg["file"]["base"]["pic"]["root"].$cfg["file"]["base"]["pic"][$value[4]];
    if ( $value[4] == "tn" ) {
        $path["img"] = $path["img"]."tn_";
    } else {
        $path["img"] = $path["img"]."img_";
    }

    $path["doc"] = $cfg["file"]["base"]["doc"]."doc_";
    $path["txt"] = $cfg["file"]["base"]["doc"]."txt_";
    $path["arc"] = $cfg["file"]["base"]["arc"]."arc_";

    // filetyp auswerten
    // vollstaendige uebersicht ueber verfuegbare mime-types: http://www.iana.org/assignments/media-types/index.html
    switch( $value[2] ) {
        case "gif":
            $type ="image/gif";
            $filepath = $path["img"];
            break;
        case "jpg":
            $type ="image/jpeg";
            $filepath = $path["img"];
            break;
        case "jpeg":
            $type ="image/jpeg";
            $filepath = $path["img"];
            break;
        case "png":
            $type ="image/png";
            $filepath = $path["img"];
            break;
        case "odp":
            $type ="application/vnd.oasis.opendocument.presentation";
            $filepath = $path["doc"];
            break;
        case "ods":
            $type ="application/vnd.oasis.opendocument.spreadsheet";
            $filepath = $path["doc"];
            break;
        case "odt":
            $type ="application/vnd.oasis.opendocument.text";
            $filepath = $path["doc"];
            break;
        case "pdf":
            $type ="application/pdf";
            $filepath = $path["doc"];
            break;
        case "bz2":
            $type ="application/octet-stream";
            $filepath = $path["arc"];
            break;
        case "gz":
            $type ="application/octet-stream";
            $filepath = $path["arc"];
            break;
        case "zip":
            $type ="application/zip";
            $filepath = $path["arc"];
            break;
        default:
            die("Bad File");
    }

    // filenamen zusammensetzen
    $file = $cfg["file"]["base"]["maindir"].$filepath.$value[3].".".$value[2];

    if ( $value[6] == "d" ) {
        echo $type."<br>";
    } else {
        header("Content-type: ".$type);
    }

    if ( $value[6] == "d" ) {
        echo $file."<br>";
    } else {
        if (($fh = fopen($file, 'rb')) === false) exit;
        while (!feof($fh)) {
            echo fread($fh, (1*(1024*1024)));
            flush();
            @ob_flush();
        }
        fclose($fh);
        exit;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
