<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 6/06/18
 * Time: 01:57 AM
 */

class Spintax_Poster_Twitter_WP_PTWP
{
    public function process($text)
    {
        return preg_replace_callback(
            '/\{(((?>[^\{\}]+)|(?R))*)\}/x',
            array($this, 'replace'),
            $text
        );
    }
    public function replace($text)
    {
        $text = $this->process($text[1]);
        $parts = explode('|', $text);
        return $parts[array_rand($parts)];
    }
}