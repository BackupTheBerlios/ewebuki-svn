<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $script_name = "$Id$";
    $Script_desc = "design selector";
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

    if ( $debugging[html_enable] ) $debugging[ausgabe] .= "[ ** $script_name ** ]".$debugging[char];

    $links = "";
    if ( $environment[design] != device ) $links .= "<a href=\"".$pathvars[webroot]."/device/".$environment[language]."/".$environment[katid]."/\" class=\"menu_punkte\">device</a><br>";
    #if ( $environment[design] != hase ) $links .= "<a href=\"".$pathvars[webroot]."/hase/".$environment[language]."/".$environment[katid]."/\" class=\"menu_punkte\">hase</a><br>";
    #if ( $environment[design] != classic ) $links .= "<a href=\"".$pathvars[webroot]."/classic/".$environment[language]."/".$environment[katid]."/\" class=\"menu_punkte\">classic</a><br>";
    if ( $environment[design] != business ) $links .= "<a href=\"".$pathvars[webroot]."/business/".$environment[language]."/".$environment[katid]."/\" class=\"menu_punkte\">business</a><br>";
    #if ( $environment[design] != creative ) $links .= "<a href=\"".$pathvars[webroot]."/creative/".$environment[language]."/".$environment[katid]."/\" class=\"menu_punkte\">creative</a>";

    $ausgaben[design] = parser( "design", "");

    if ( $debugging[html_enable] ) $debugging[ausgabe] .= "[ ++ $script_name ++ ]".$debugging[char];
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
