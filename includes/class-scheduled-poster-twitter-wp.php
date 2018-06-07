<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 6/06/18
 * Time: 01:34 AM
 */

class Scheduled_Poster_Twitter_WP_PTWP
{
    public function __construct()
    {
        add_filter('cron_schedules',array($this, 'cron_schedules_social_poster_wp'));
        if (!wp_get_schedule('cron_time_poster_twitter_wp')) {
            wp_schedule_event(time(), 'poster_twitter_wp_ptwp', 'cron_time_poster_twitter_wp');
        }
        add_action('cron_time_poster_twitter_wp',array($this,'poster_twitter_wp_ptwp_cron_task'));
    }

    public function cron_schedules_social_poster_wp()
    {
        $seg = 30;
        if(!isset($schedules["poster_twitter_wp_ptwp"])){
            $schedules["poster_twitter_wp_ptwp"] = array(
                'interval' => $seg*60,
                'display' => __("Once every $seg minutes"));
        }
        return $schedules;
    }

    public function poster_twitter_wp_ptwp_cron_task()
    {
        $users = $this->getUsersCap();
        if (!empty($users)){
            foreach ($users as $user){
                $id = $user->data->ID;
                $message = get_post_meta($id, 'poster-twitter-wp-ptwp-tweet', true);
                if (empty($message))
                    continue;
                poster_twitter_wp_ptwp()->conn->postTweet($id);
            }
        }
    }


    public function getUsersCap()
    {
        $all_users = get_users();
        $specific_users = array();

        foreach($all_users as $user){

            if($user->has_cap('cap_poster_twitter_wp')){
                $specific_users[] = $user;
            }
        }

        return $specific_users;
    }
}