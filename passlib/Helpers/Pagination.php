<?php

	namespace PassMan\Helpers;

    /**
     * Pagination class.
     *
     * Based on integer values - e.g. a number of page to display.
     * Currently hard-coded to return links as /home/index/N where N is a number
     * 
     * @param int $items Total number of items to display.
     * @param int $current Page being displayed - auto reduced to in between 1 and maximum
     * @param int $rows_per_page Number of records from database shown per page - default 5
     *
     * @return string Returns a HTML <nav> tag with an unordered list of Bootstrap 4 pagination buttons
     */
    final class Pagination {

        public static function showPagination($items, $current, $rows_per_page = 5) {

            $pages = ceil($items/$rows_per_page);
            $max_btns = 9; // needs odd number
            $offset = 5;
            
            // preapring result:
            $result =  "<nav aria-label=\"Password pagination\">\n";
            $result .= "<ul class=\"pagination justify-content-center\">\n";

            // Calculations
            if ( $current < 1 ) { // preventing manual input error
                $current = 1;
            }
            if ( $current > $pages ) { // preventing manual input error
                $current = $pages;
            }

            if ( $pages <= $max_btns ) { // less pages than max number of buttons
                for ($i=1; $i<= $pages; $i++) {
                    $result .= "<li class=\"page-item".  ($i == $current ? ' active' : '') ."\"><a class=\"page-link\" href=\"/home/index/$i\">$i</a></li>\n";
                }
            }
            else {
                //we have more pages than 9 buttons
                if ( $current < ($max_btns-1) ) { // current page is one of 1-7 pages
                    for ( $i=1; $i<($max_btns-1); $i++ ) {
                        $result .= "<li class=\"page-item".  ($i == $current ? ' active' : '') ."\"><a class=\"page-link\" href=\"/home/index/$i\">$i</a></li>\n";
                    }
                    $result .= "<li class=\"page-item\"><a class=\"page-link\" href=\"/home/index/".($max_btns+1)."\">...</a></li>\n"; // towards last
                    $result .= "<li class=\"page-item\"><a class=\"page-link\" href=\"/home/index/".$pages."\">Last</a></li>";
                }
                elseif ( ($pages-$current)<($max_btns-2) ) { // page in between last-7 and last
                    $result .= "<li class=\"page-item\"><a class=\"page-link\" href=\"/home\">First</a></li>\n";
                    $result .= "<li class=\"page-item\"><a class=\"page-link\" href=\"/home/index/".($pages-($max_btns))."\">...</a></li>\n"; // towards first
                    for ( $i=$pages-($max_btns-2); $i<$pages; $i++ ) {
                        $result .=  "<li class=\"page-item".  ($i+1 == $current ? ' active' : '') ."\"><a class=\"page-link\" href=\"/home/index/".($i+1)."\">".($i+1)."</a></li>\n";
                    }
                }
                else { //page link somewhere in the middle
                    $result .= "<li class=\"page-item\"><a class=\"page-link\" href=\"/home\">First</a></li>\n";
                    $result .= "<li class=\"page-item\"><a class=\"page-link\" href=\"/home/index/".($current-$offset)."\">...</a></li>\n"; // towards first

                    for ( $i=-2; $i<3; $i++) {
                        $result .=  "<li class=\"page-item".  ($i == 0 ? ' active' : '') ."\"><a class=\"page-link\" href=\"/home/index/".($current+$i)."\">".($current+$i)."</a></li>\n";
                    }

                    $result .= "<li class=\"page-item\"><a class=\"page-link\" href=\"/home/index/".($current+$offset)."\">...</a></li>\n"; // towards last
                    $result .= "<li class=\"page-item\"><a class=\"page-link\" href=\"/home/index/".$pages."\">Last</a></li>";
                }
            }

            $result .= "</ul>\n";
            $result .= "</nav>";

            return $result;
        }

    }