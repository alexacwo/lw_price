<?php
//Add support for the dropdown menu
class aw_Walker_Page_Gumby extends Walker_Nav_menu{

        function start_lvl(&$output, $depth = 0, $args = array()){
                $indent = str_repeat("\t", $depth);
                $output .= "\n$indent<div class=\"dropdown\"><ul>\n";
        }

        function end_lvl(&$output , $depth = 0, $args = array()){
                $indent = str_repeat("\t", $depth);
                $output .= "$indent</ul></div>\n";
        }
}
?>
