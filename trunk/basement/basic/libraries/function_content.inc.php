<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "content sprachabhaengig holen";
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

    function content($line, $tname) {

        global $db, $debugging, $pathvars, $specialvars, $environment, $defaults, $ausgaben, $rechte, $eWeBuKi, $RightConcept;

        if ( $specialvars["crc32"] == -1 ) {
            if ( $environment["ebene"] != "" && $tname == $environment["kategorie"] ) {
                $tname = eCRC($environment["ebene"]).".".$tname;
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "crc32 tname \"".$tname."\" forced!!!".$debugging["char"];
            }
        } else {
            // ist das eine sub kategorie ?
            if ( $environment["subkatid"] != "" && $tname == $environment["katid"] ) {
                $tname = $tname.".".$environment["subkatid"];
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sub tname \"".$tname."\" forced!!!".$debugging["char"];
                #$dbtname = $tname;
            }
        }


        while ( strpos($line, "#(") !== false || strpos($line, "g(") !== false ) {

            // wo beginnt die marke
            $labelbeg = strpos($line,"#(");
            $art = "#(";
            $bez = "# (";
            $dbtname = $tname;
            if ( $labelbeg === false ) {
                $labelbeg = strpos($line,"g(");
                $art = "g(";
                $bez = "g (";
                $dbtname = "global";
            }

            // wo endet die marke (wichtig der offset!)
            $labelend = strpos($line,")",$labelbeg);
            // wie lang ist die marke
            $labellen = $labelend-$labelbeg;
            // token name extrahieren
            $label = substr($line,$labelbeg+2,$labellen-2);

            if ( strpos($label,",") !== false ) break; // javascript fix

             if ( preg_match("/^v[0-9]*$/",$environment["parameter"][1],$regs) && ( priv_check($environment["ebene"]."/".$environment["kategorie"],"view") || $rechte["cms_edit"] == -1 ) ) {
                $version_sql = "AND version=".substr($environment["parameter"][1],1);
                $version = substr($environment["parameter"][1],1);
            } else {
                $version = "";
                $version_sql = "";
            }

            if ( $specialvars["content_release"] == -1 && $version == "" ) {
                $content_release = "AND status>0";
            } else {
                $content_release = "";
            }

            $sql = "SELECT html, content
                      FROM ". SITETEXT ."
                     WHERE tname='".$dbtname."'
                       AND lang='".$environment["language"]."'
                       AND label='".$label."'
                        ".$version_sql."
                        ".$content_release."
                  ORDER BY version DESC
                     LIMIT 0,1";
            #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result  = $db -> query($sql);
            $row = $db -> fetch_row($result);

            if ( !is_array($row) ) {
                // wenn "aktuelle sprache" = "default sprache" ueberfluessige fehlermeldung nicht anzeigen!
                if ( $environment["language"] != $specialvars["default_language"] ) {
                    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "Language: \"".$environment["language"]."\" for #(".$label.") in template \"".$dbtname."\" not found using default: \"".$specialvars["default_language"]."\"".$debugging["char"];
                }
                $sql = "SELECT html, content
                          FROM ". SITETEXT ."
                         WHERE tname='$dbtname'
                           AND lang='".$specialvars["default_language"]."'
                           AND label='$label'
                            ".$version_sql."
                            ".$content_release."
                      ORDER BY version DESC
                         LIMIT 0,1";
                $result  = $db -> query($sql);
                $row = $db -> fetch_row($result);
            }

            if ( $row[1] == "" ) {
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "Language: Uuuuups no default language \"".$specialvars["default_language"]."\" for #(".$label.") in template \"".$dbtname."\" found. Giving up!".$debugging["char"];
            }

            $database = $db->getDb();

            $replace = $row[1];

            // wenn content nicht in html ist und deaktiviert wurde
            if ( $row[0] != -1 && $specialvars["denyhtml"] == -1 ) {
                // html killer :)
                $pattern = "<[\!\/a-zA-Z].{0,}>";
                while ( preg_match("/".$pattern."/", $replace, $tag) ) {
                    $replace = str_replace($tag[0]," - html gel&ouml;scht! -",$replace);
                }
            }

            // eWeBuKi tag schutz part 1 (siehe part 2 (weiter unten), part 3 (cms.in.php), part 4 (function_rparser.inc.php))
            if ( strpos( $replace, "[/E]") !== false ) {
                $preg = "|\[E\](.*)\[/E\]|Us";
                preg_match_all($preg, $replace, $match, PREG_PATTERN_ORDER );
                $mark_l = array( "[/", "["  );
                $hide_l = array( "++", "**" );
                $mark_o = array( "#(", "g(", "#{", "!#" );
                $hide_o = array( "::1::", "::2::", "::3::", "::4::" );
                foreach ( $match[0] as $key => $value ) {
                    $escape = str_replace( $mark_l, $hide_l, $match[1][$key]);
                    $escape = str_replace( $mark_o, $hide_o, $escape);
                    $replace = str_replace( $value, "[E]".$escape."[/E]", $replace);
                }
            }

            // cms edit link einblenden
            $check = "";
            if ( $specialvars["editlock"] == False && $tname != "auth" ) {
                if ( $specialvars["security"]["new"] == -1 ) {
                    $check = priv_check('', $specialvars["security"]["content"],$specialvars["dyndb"]);
                } else {
                    if ( $rechte["cms_edit"] == -1 ) $check = True;
                }

                if ( $check == True ) {

                    if ( $defaults["section"]["label"] == "" ) $defaults["section"]["label"] = "inhalt";
                    if ( $defaults["section"]["tag"] == "" ) $defaults["section"]["tag"] = "[H";

                    if ( $specialvars["nosections"] != True && $label == $defaults["section"]["label"] ) {

                        if ( is_array($defaults["section"]["tag"]) ) {
                            // neue section-edit-marken
                            $preg_search = str_replace(
                                            array("[", "]", "/"),
                                            array("\[","\]","\/"),
                                            implode("|",$defaults["section"]["tag"])
                            );
                            $allcontent = preg_split("/(".$preg_search.")/",$replace,-1,PREG_SPLIT_DELIM_CAPTURE);
                            $i = 0;
                            foreach ( $allcontent as $key=>$value ) {
                                if ( in_array($value,$defaults["section"]["tag"]) ) {
                                    $join[$i] = "{".$i."}".$value;
                                } else {
                                    $join[$i] .= $value;
                                    $i++;
                                }
                            }
                            $replace = implode("",$join);
                        } else {
                            $allcontent = explode($defaults["section"]["tag"], $replace);
                            foreach ($allcontent as $key => $value) {
                                if ( $key == 0 ) {
                                    $join[] = $value;
                                } else {
                                    $parts = explode( "]", $value, 2);
                                    $join[] = $parts[0]."]{".$key."}".$parts[1];
                                }
                            }
                            $replace = implode($defaults["section"]["tag"], $join);
                        }
                    }

                    // konvertieren ?
                    if ( $specialvars["wysiwyg"] == "" && $row[0] == -1 ) {
                        $convert = ",,tag";
                        $signal = "c";
                    } elseif ( $specialvars["wysiwyg"] != "" && $row[0] != -1 ) {
                        $convert = ",,html";
                        $signal = "c";
                    } else {
                        $convert = "";
                        $signal = "e";
                    }
                    if ( $specialvars["old_contented"] == True ) {
                        $editurl = $pathvars["virtual"]."/cms/edit,".$db->getDb().",".$dbtname.",".$label;
                    } else {
                        $editurl_key = $pathvars["virtual"]."/admin/contented/edit,".$db->getDb().",".$dbtname.",".$label;
                        $editurl = $pathvars["virtual"]."/admin/contented/edit,".$db->getDb().",".$dbtname.",".$label.",,,".$version;
                    }

                    if ( $defaults["cms-tag"]["link"] == "" ) {
                        $defaults["cms-tag"]["link"] = "<a href=\"##url##\"><img src=\"/images/default/cms-tag-##signal##.png\" width=\"4\" height=\"4\" alt=\"Bearbeiten\" /></a>";
                    }

                    // section verarbeitung
                    if ( $specialvars["nosections"] != True && $label == $defaults["section"]["label"] ) {
                        foreach ( $allcontent as $key => $value ) {
                            $marken = array("##url##", "##signal##");
                            $ersatz = array($editurl_key.",".$key.",,".$version.",.html", $signal);
                            $replace = str_replace( "{".$key."}",str_replace($marken,$ersatz,$defaults["cms-tag"]["link"]), $replace);
                        }
                    }

                    // wenn es kein value, alt, title und status in der zeile gibt
                    $vorher = substr($line,$labelbeg-20,20);
                    if ( !strpos($vorher,"value=\"")
                      && !strpos($vorher,"alt=\"")
                      && !strpos($vorher,"title=\"")
                      && !strpos($vorher,"status='") ) {
                        $marken = array("##url##", "##signal##");
                        $ersatz = array($editurl.$convert.".html", $signal);
                        $replace .= str_replace($marken,$ersatz,$defaults["cms-tag"]["link"]);
                    } else {
                        $ausgaben["inaccessible"] .= $bez.$label.")&nbsp;".$art.$label.")<br />\n";
                    }
                }
            }

            // wenn content nicht in html ist
            if ( $row[0] != -1 ) {
                // intelligenten link tag bearbeiten
                $replace = intelilink($replace);
                // neues generelles tagreplace
                $replace = tagreplace($replace);

                // eWeBuKi tag schutz part 2
                $replace = str_replace( $hide_l, $mark_l, $replace);

                // newlines nach br wandeln (muss zuletzt gemacht werden)
                if ( $specialvars["newbrmode"] != True ) {
                    $replace = nlreplace($replace);
                }

            }

            // marke ersetzen
            if ( strpos($line,$art) !== false ) {
                $line = str_replace($art.$label.")",$replace,$line);
            }
        }
        return($line);
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
