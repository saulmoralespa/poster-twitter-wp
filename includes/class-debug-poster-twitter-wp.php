<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 6/06/18
 * Time: 10:17 AM
 */

class Debug_Poster_Twitter_WP_PTWP
{
    public function add($log, $empty = false)
    {

        if ($empty) {
            $mode = "w";
            $date = "";
        }else{
            $mode = "a+";
            $date = date('l jS F Y');
        }
        $fp = fopen(poster_twitter_wp_ptwp()->file_debug, $mode);
        fwrite($fp, $log . "\t$date\r\n");
        fclose($fp);

        $fp = fopen(poster_twitter_wp_ptwp()->file_debug, $mode);
        fwrite($fp, $log . "\t$date\r\n");
        fclose($fp);
    }
}