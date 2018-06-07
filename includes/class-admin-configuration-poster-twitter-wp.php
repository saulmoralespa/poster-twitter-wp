<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 4/06/18
 * Time: 10:02 PM
 */

class Admin_Configuration_Poster_Twitter_WP_PTWP
{
    public function configInit()
    {
        //social_poster_wp_spwp()->tabs->page();
    }

    public function content()
    {
        $id = get_current_user_id();
        $consumer_key = '';
        $consumer_secret = '';
        if (poster_twitter_wp_ptwp()->get_data_table(true)){
            $data = poster_twitter_wp_ptwp()->get_data_table();
            $consumer_key = $data->consumer_key;
            $consumer_secret = $data->consumer_key_secret;
        }
        poster_twitter_wp_ptwp()->conn->saveTokens($id);
        ?>
        <div class="wrap about-wrap">
            <div id="modal1" class="modal">
                <div class="modal-content">
                    <h3 class="center"></h3>
                    <div class="center">
                        <i class="large material-icons"></i>
                    </div>
                </div>
            </div>
            <form id="poster-twitter-wp-admin">
                <table class="widefat fixed" cellspacing="0">
                    <h3><?php _e('App de twitter', poster_twitter_wp_ptwp()->nameClean(true)); ?><a class="large material-icons tooltipped" data-position="right" data-tooltip="<?php _e('Aplicación creada previamente en apps.twitter.com', poster_twitter_wp_ptwp()->nameClean(true)); ?>">help</a></h3>
                    <tbody>
                    <tr>
                        <th scope='row'><?php _e('Consumer key',  poster_twitter_wp_ptwp()->nameClean(true)); ?><a class="large material-icons tooltipped" data-position="right" data-tooltip="<?php _e('Clave de la aplicación', poster_twitter_wp_ptwp()->nameClean(true)); ?>">help</a></th>
                        <td>
                            <input type='text' name='poster-twitter-wp-consumer-key-ptwp' value='<?php echo $consumer_key; ?>' required>
                        </td>
                    </tr>
                    <tr>
                        <th scope='row'><?php _e('Consumer secret', poster_twitter_wp_ptwp()->nameClean(true)); ?><a class="large material-icons tooltipped" data-position="right" data-tooltip="<?php _e('Clave secreta de la aplicación', poster_twitter_wp_ptwp()->nameClean(true)); ?>">help</a></th>
                        <td>
                            <input type='text' name='poster-twitter-wp-consumer-secret-ptwp' value='<?php echo $consumer_secret; ?>' required>
                        </td>
                    </tr>
                    <tr>
                        <th scope='row'><?php _e('Callback URL', poster_twitter_wp_ptwp()->nameClean(true)); ?><a class="large material-icons tooltipped" data-position="bottom" data-tooltip="<?php _e("Inserte esta url en Callback URLs ", poster_twitter_wp_ptwp()->nameClean(true)); ?>">help</a></th>
                        <td>
                            <input type='text' name='social-poster-wp-app-oauth-ptwp' value='<?php echo admin_url().'admin.php?page=config-' . poster_twitter_wp_ptwp()->nameClean(); ?>' readonly>
                            <input type="hidden" name="poster-twitter-wp-id-ptwp" value="<?php echo $id; ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Comando para la creación del cron job', poster_twitter_wp_ptwp()->nameClean(true)); ?><a class="large material-icons tooltipped" data-position="bottom" data-tooltip="<?php _e("Use este comando para crear un cron job en su servidor, cada 5 minutos como mínimo", poster_twitter_wp_ptwp()->nameClean(true)); ?>">help</a></th>
                        <td>
                            <input type="text" value="wget <?php echo site_url()."/wp-cron.php?doing_wp_cron > /dev/null 2>&1"; ?>" readonly>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}