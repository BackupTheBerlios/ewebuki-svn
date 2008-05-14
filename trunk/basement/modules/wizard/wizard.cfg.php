<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: leer.cfg.php 1131 2007-12-12 08:45:50Z chaot $";
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
////+///////+///////+///////+///////+///////+///////+///////////////////////////////////////////////////////////

    $cfg["wizard"] = array(
           "subdir" => "wizard",
             "name" => "wizard",
            "basis" => $pathvars["virtual"]."/wizard", # crc = -1468826685 *
         "iconpath" => "", # leer: /images/default/; automatik: $pathvars["images"]
          "archive" => array( "inhalt" ),
            "color" => array(
                        "a" => "#eeeeee",
                        "b" => "#ffffff",
                       ),
         "function" => array(
                     "show" => array("makece"),
              "show,shared" => array("menu_convert","filelist"),
                      "add" => array(""),
              "add,shared" => array("menu_convert"),
                   "editor" => array("makece"),
            "editor,shared" => array("filelist","phpdiff","zip_handling"),
                   "modify" => array("makece"),
                  "release" => array("makece"),
           "release,shared" => array("menu_convert"),
              #"edit,shared" => array("shared1", "shared2"),
              #"edit,global" => array("global1", "global2"),
                       ),
               "db" => array(
                     "leer" => array(
                          "entries" => "db_leer",
                              "key" => "id",
                            "order" => "sort, label",
                            "rows"  => 4,
                     ),
              ),
            "tagjs" => "-141347382.modify.js-nextgen",
             "tags" => $cfg["contented"]["tags"],
    "default_label" => "inhalt",
         "ed_boxed" => array(
                    "H"    => array(                                    // ueberschriften
                                    array("[H"),
                                    "block",
                                    array("edit","del")
                            ),
                    "P"    => array(                                    // absaetze
                                    array("[P"),
                                    "block",
                                    array("edit","del")
                            ),
                    "DIV"  => array(                                    // div-block
                                    array("[DIV"),
                                    "hide",
                                    array()
                            ),
                    "LINK" => array(                                    // links
                                    array("[LINK"),
                                    "inline",
                                    array("edit","rip")
                            ),
                    "IMG"  => array(                                    // bilder
                                    array("[IMG"),
                                    "block",
                                    array("edit","del")
                            ),
                    "SEL"  => array(                                    // gruppierungen
                                    array("[SEL"),
                                    "block",
                                    array("edit","del")
                            ),
                    "TAB"  => array(                                    // tabellen
                                    array("[TAB"),
                                    "block",
                                    array("edit","del")
                            ),
                    "LIST" => array(                                    // listen
                                    array("[LIST"),
                                    "block",
                                    array("edit","del")
                            ),
                    "B" => array(                                       // fett
                                    array("[B"),
                                    "inline",
                                    array("edit","rip")
                            ),
                    "I" => array(                                       // kursiv
                                    array("[I"),
                                    "inline",
                                    array("edit","rip")
                            ),
                    "!" => array(                                       // kommentar
                                    array("[!"),
                                    "hide",
                                    array()
                            ),
                ),
     "allowed_tags" => array(
                           "H" => array(),
                           "P" => array("link","b","i","acr"),
                    "P=teaser" => array("link","b","file"),
                        "LIST" => array("link","b","i","acr"),
                           "B" => array(),
                ),

        // alle elemente die eingefuegt werden koennen
         "add_tags" => array(
                       "H1" => "[H1]&Uuml;berschrift Ebene 1[/H1]",
                       "H2" => "[H2]&Uuml;berschrift Ebene 2[/H2]",
                       "H3" => "[H3]&Uuml;berschrift Ebene 3[/H3]",
                   "Absatz" => "[P]Absatz[/P]",
                   "Teaser" => "[P=teaser]Teaser-Absatz[/P]",
                  "Tabelle" => "[TAB=;100%;;5;]\n[/TAB]",
                    "Liste" => "[LIST]Listeneintrag[/LIST]",
                "Selection" => "[SEL]Gruppierung[/SEL]",
                "3er-Kombi" => "[DIV=present]
                                [IMG=/file/jpg/2/s/dummy.jpg;l;0;l;0;0;5][/IMG]
                                [H2]&Uuml;berschrift[/H2]
                                [P]Beschreibung[/P]
                                [BR=a][/BR]
                                [/DIV]",
                ),

        "wizardtyp" => array(
                  "default" => array(
//                              "add_tags" => array("Absatz"),           // array mit allen tags die eingefuegt werden koennen (leer=alle)
                       "section_block" => array(0,0),           // gibt an, wieviele sichtbaren bereich von oben,unten nicht verschoben werden koennen
                           "def_label" => "inhalt",
//                             "show_add" => False,                // muss false gesetzt sein, wenn es nicht in der add-liste auftauchen soll
                        ),
                  "dummy" => array(
                            "add_tags" => array("H2","Absatz"),
                       "section_block" => array(3,0),
                           "def_label" => "inhalt",
                        ),
                ),

         "img_edit" => array(
             "cb_show_size" => array(
                                  "tn" => "thumb",
                                   "s" => "small",
                                   "m" => "middle",
                                   "b" => "big",
                                   "o" => "original",
                        ),
             "cb_link_size" => array(
                                    "" => "none",
                                  "tn" => "thumb",
                                   "s" => "small",
                                   "m" => "middle",
                                   "b" => "big",
                                   "o" => "original",
                                   "l" => "lightbox",
                        ),
                 "cb_align" => array(
                                    "" => "none",
                                   "l" => "links",
                                   "r" => "rechts",
                        ),
                ),

         "sel_edit" => array(
             "cb_link_size" => array(
                                   "s" => "small",
                                   "m" => "middle",
                                   "b" => "big",
                                   "o" => "original",
                        ),
                ),

        "link_edit" => array(
                "cb_target" => array(
                                    "" => "nope",
                              "_blank" => "_blank",
                               "_self" => "_self",
                        ),
                ),











            "right" => "edit",
    );

    // * tipp: fuer das einfache modul muss der wert $cfg["basis"] natuerlich
    // "/my" lauten. es funktioniert im beispiel nur ohne aenderung, da das
    // einfache script $cfg["basis] nicht nutzt.

////+///////+///////+///////+///////+///////+///////+///////////////////////////////////////////////////////////
?>
