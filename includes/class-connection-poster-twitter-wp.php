<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 5/06/18
 * Time: 12:34 PM
 */

use Abraham\TwitterOAuth\TwitterOAuth;

class Connection_Poster_Twitter_WP_PTWP
{
    private $_dataTable;

    public function __construct()
    {
        if(!session_id()) {
            session_start();
        }
        $this->_dataTable = $this->_DataTable();
    }

    public function instanceTW()
    {
        $data = $this->_DataTable();
        $conn = new TwitterOAuth($data->consumer_key, $data->consumer_key_secret);
        return $conn;
    }


    public function requestToken($id, $user=false)
    {

        $callback = ($user) ? admin_url().'admin.php?page=role-' . poster_twitter_wp_ptwp()->nameClean() : admin_url().'admin.php?page=config-' . poster_twitter_wp_ptwp()->nameClean();
        $request_token = $this->instanceTW()->oauth('oauth/request_token', array('oauth_callback' => $callback));
        update_post_meta($id, 'poster-twitter-wp-ptwp-oauth-token', $request_token['oauth_token']);
        update_post_meta($id, 'poster-twitter-wp-ptwp-oauth-token-secret', $request_token['oauth_token_secret']);
        $url = $this->instanceTW()->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

        return $this->shortenURL($url);
    }

    public function saveTokens($id)
    {
        if(isset($_REQUEST['oauth_token']) && isset($_REQUEST['oauth_verifier'])){
            $data = $this->_DataTable();
            $oauth_verifier = sanitize_text_field($_REQUEST['oauth_verifier']);
            $oauth_token = get_post_meta($id, 'poster-twitter-wp-ptwp-oauth-token', true);
            $oauth_token_secret = get_post_meta($id, 'poster-twitter-wp-ptwp-oauth-token-secret', true);
            $connection = new TwitterOAuth($data->consumer_key, $data->consumer_key_secret, $oauth_token, $oauth_token_secret);
            $access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => $oauth_verifier));
            update_post_meta($id, 'poster-twitter-wp-ptwp-token-user', $access_token);
        }
    }

    public function postTweet($id)
    {


        try{
            $conn = $this->getInstanceUser($id);
            if(empty($this->getCredentials($id)))
                update_post_meta($id, 'poster-twitter-wp-ptwp-token-user', '');

            $message = get_post_meta($id, 'poster-twitter-wp-ptwp-tweet', true);
            $spintax = new Spintax_Poster_Twitter_WP_PTWP();
            $message = $spintax->process($message);
            $conn->post('statuses/update', array('status' => $message));

        }catch (Exception $e){
            poster_twitter_wp_ptwp()->debug->add($e->getMessage());
        }
    }


    public function getCredentials($id)
    {

        try{
            $user = $this->getInstanceUser($id)->get("account/verify_credentials");
            return $user;
        }catch (Exception $e){
            poster_twitter_wp_ptwp()->debug->add($e->getMessage());

        }

    }

    public function getInstanceUser($id)
    {
        try{
            $data = $this->_DataTable();
            $access_token = get_post_meta($id, 'poster-twitter-wp-ptwp-token-user', true);
            $connection = new TwitterOAuth($data->consumer_key, $data->consumer_key_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
            return $connection;
        }catch (Exception $e){
            poster_twitter_wp_ptwp()->debug->add($e->getMessage());
        }
    }


    public function shortenURL($url)
    {
        $apiURL = 'http://api.adf.ly/api.php?';

        $query = array(
            'key' => '40ae4b31256e49c5900df8c6408ae96d',
            'uid' => 3963310,
            'advert_type' => 'int',
            'domain' => 'adf.ly',
            'url' => $url
        );

        $apiURL = $apiURL . http_build_query($query);

        if ($data = file_get_contents($apiURL))
            return $data;
        return $url;

    }

    private function _DataTable()
    {
        return poster_twitter_wp_ptwp()->get_data_table();
    }
}