<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: menu-convert.inc.php 311 2005-03-12 21:46:39Z chaot $";
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

    86343 Königsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

     function show_blog($url,$tags,$right="",$wizard="",$limit="",$sort="",$kategorie="") {
        global $db,$pathvars,$ausgaben,$mapping,$hidedata,$environment;

        // parameter-erklaerung
        // 1: vorgesehen fuer inhalt_selector
        // 2: aufruf eines einzigen contents
        // 3: faqlink

        // aus der url eine id machen
        $id = make_id($url);
        $new = $id["mid"];
        $where = "";

        // falls kategorie , werden nur diese angezeigt
        if ( $kategorie != "" ) {
            $where = "  AND SUBSTR(content,POSITION('[KATEGORIE]' IN content),POSITION('[/KATEGORIE]' IN content)-POSITION('[KATEGORIE]' IN content)) ='[KATEGORIE]".$kategorie."'";
        }

        $erlaubnis = "";
        // hier erfolgt der rechte-check, um den new-link einzublenden
        if ( $right == "" || ( priv_check($url,$right) || ( function_exists(priv_check_old) && priv_check_old("",$right) ) ) ) {
            $erlaubnis = -1;
            $hidedata["new"]["link"] = $url;
            $hidedata["new"]["kategorie"] = $kategorie;
        }

        // erster test einer suchanfrage per kalender
        //

        if ( $_GET["year"] || $_GET["month"] || $_GET["day"] ) {
            $heute = getdate(mktime(0, 0, 0, ($_GET["month"])+1, 0, $_GET["year"]));
            if ( !$_GET["day"] ) {  
                $day1 = $heute["mday"];
                $day2 = "1";
            } else {
                $day1 = $_GET["day"];
                $day2 = $_GET["day"];
            }
            $where = "AND ( Cast(SUBSTR(content,6,19) as DATETIME) < '".$_GET["year"]."-".$_GET["month"]."-".$day1." 23:59:59' AND Cast(SUBSTR(content,6,19) as DATETIME) > '".$_GET["year"]."-".$_GET["month"]."-".$day2." 00:00:00'    )";
        }
        //
        // erster test einer suchanfrage per kalender

        $tname = eCRC($url).".%";

        if ( $environment["parameter"][2] != "" ) {
            $tname = eCRC($url).".".$environment["parameter"][2];
        }

        if ( $sort == "-1" ) {
            $art = "SIGNED";
        } else {
            $art = "DATETIME";
        }

        $sql = "SELECT Cast(SUBSTR(content,POSITION('[SORT]' IN content)+6,POSITION('[/SORT]' IN content)-POSITION('[SORT]' IN content)-6) AS ".$art.") AS date,content,tname from site_text WHERE status = 1".$where." AND tname like '".$tname."' order by date DESC";

        if ( strpos($limit,"," ) ){
            $sql = $sql." LIMIT ".$limit;
        } else {
            if ( $limit != "" ) {
                $p=$environment["parameter"][1]+0;
                // seiten umschalter
                $inhalt_selector = inhalt_selector( $sql, $p, $limit, $parameter, 1, 10, $getvalues );
                $ausgaben["inhalt_selector"] = $inhalt_selector[0]."<br />";
                $sql = $inhalt_selector[1];
                $ausgaben["anzahl"] = $inhalt_selector[2];
            }
        }

        $counter = 0;
        $result = $db -> query($sql);
        $preg1 = "\.([0-9]*)$";

        // evtl wizard einbinden
        if ( $wizard != "" ) {
            $editlink = "/wizard/show,";
        } else {
            $editlink = "/admin/contented/edit,";
        }

        while ( $data = $db -> fetch_array($result,1) ) {
            $counter++;
            $test = preg_replace("|\r\n|","\\r\\n",$data["content"]);
            foreach ( $tags as $key => $value ) {
                if (is_array($value)) {
                    $tag_parameter= $value["parameter"];
                    $value = $value["tag"];
                }
                if (strpos($value,"=")) {
                     $endtag= substr($value,0,strpos($value,"="));
                    if ( $value == "IMG=") {
                        $value .= ".*";
                    } else {
                        $value = $value.$tag_parameter;
                    }
                } else {
                    $endtag=$value;
                }

                $preg = "(\[".addcslashes($value,"/")."\])(.*)\[\/".$endtag."\]";
                if ( preg_match("/$preg/U",$test,$regs) ) {
                    $rep_tag = str_replace('\r\n',"<br>",$regs[0]);
                    $org_tag = str_replace('\r\n',"<br>",$regs[2]);
                } else {
                    $rep_tag = "";
                    $org_tag = "";
                }
                $array[$counter][$key."_org"] = $org_tag;
                $array[$counter][$key] = tagreplace($rep_tag);
                if ( $org_tag == "" ) $array[$counter][$key] = "";
                if ( preg_match("/^\[IMG/",$rep_tag,$regs) ) {
                    $image_para = explode("/",$rep_tag);
                    $array[$counter][$key."_img_art"] = $image_para[2];
                    $array[$counter][$key."_img_id"] = $image_para[3];
                }
                $array[$counter][$key."_org"] = $org_tag;
            }

            preg_match("/$preg1/",$data["tname"],$regs);

            $array[$counter]["datum"] = substr($data["date"],8,2).".".substr($data["date"],5,2).".".substr($data["date"],0,4);
            $array[$counter]["detaillink"] = $pathvars["virtual"].$url."/".$regs[1].".html";
            if ( $kategorie != "" ) {
                if ( $environment["ebene"] == "" ) {
                    $url = "/".$environment["kategorie"];
                } else {
                    $url = $environment["ebene"]."/".$environment["kategorie"];
                }
            }
            $array[$counter]["faqlink"] = $pathvars["virtual"].$url.",,,".$regs[1].".html";
            $array[$counter]["id"] = $regs[1];
            if ( $sort == "-1" && $erlaubnis == -1) {
                $array[$counter]["sort"] = "<a href=\"".$pathvars["virtual"]."/admin/bloged/sort,up,".$regs[1].",,".$new.".html\">nach oben</a>";
                $array[$counter]["sort"] .= " <a href=\"".$pathvars["virtual"]."/admin/bloged/sort,down,".$regs[1].",,".$new.".html\">nach unten</a>";
            } else {
                $array[$counter]["sort"] = "";
            }
            if ( $environment["parameter"][3] == $regs[1] ) {
                $array[$counter]["faqcontent"] = $array[$counter]["faq"];
            } else {
                $array[$counter]["faqcontent"] = "";
            }

            if ( $right == "" || 
            ( priv_check($url,$right) || ( function_exists(priv_check_old) && priv_check_old("",$right) ) )
            ) {
                $array[$counter]["deletelink"] = "<a href=\"".$pathvars["virtual"]."/admin/bloged/delete,,".$regs[1].",,".$new.".html\">delete</a>";
                $array[$counter]["editlink"] = "<a href=\"".$pathvars["virtual"].$editlink.DATABASE.",".$data["tname"].",inhalt.html\">edit</a>";
            } else {
                $array[$counter]["editlink"] = "";
                $array[$counter]["deletelink"] = "";
            }
        }
        return $array;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
