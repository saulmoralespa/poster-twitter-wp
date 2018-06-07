<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 4/06/18
 * Time: 10:31 PM
 */

class Menu_Role_Config_Poster_Twitter_WP_PTWP
{
    public function content()
    {
        $id = get_current_user_id();
        $token = get_post_meta($id, 'poster-twitter-wp-ptwp-token-user', true);
        $message = get_post_meta($id, 'poster-twitter-wp-ptwp-tweet', true);
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
            <div id="modal2" class="modal" data-video="https://www.youtube.com/embed/UVk20msfcAg">
                <div class="modal-content">
                    <iframe width="100%" height="350" src="" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        <?php
        if (empty($token)) {
            ?>
        <div id="connect-twitter">
            <h3><?php _e('Necesitas autorizarnos para publicar', poster_twitter_wp_ptwp()->nameClean()); ?><a class="large material-icons tooltipped" data-position="right" data-tooltip="<?php _e('Vas a ser redirigido a twitter donde tendras que iniciar sesión y autorizar el app', poster_twitter_wp_ptwp()->nameClean(true)); ?>">help</a></h3>
            <button class="button-primary center"
                    id="poster-twitter-wp-login-twitter" data-id="<?php echo $id; ?>"><?php echo __('Iniciar sesión con Twitter'); ?></button>
        </div>
        <?php
    }else{
           $user = poster_twitter_wp_ptwp()->conn->getCredentials($id);
           $userName = (isset($user->screen_name)) ? "$user->screen_name": '';
          ?>
        <div class="container">
            <h3><?php echo sprintf(__( 'Mensaje a publicar de @%s', poster_twitter_wp_ptwp()->nameClean(true) ), $userName ); ?><a class="large material-icons tooltipped" data-position="right" data-tooltip="<?php _e('El contenido de los tweets', poster_twitter_wp_ptwp()->nameClean(true)); ?>">help</a></h3>
            <form id="poster-twitter-wp-tweet-ptwp" class="col s12" novalidate="novalidate">
                <div id="card-alert" class="card pink lighten-5 messageText" style="display:none;">
                    <div class="card-content pink-text darken-1">
                        <p></p>
                        <a class="btn modal-trigger" data-target="modal2" href="#modal2">Ver video tutorial</a>
                    </div>
                </div>
                <div class="input-field">
                    <i class="material-icons prefix">mode_edit</i>
                    <textarea name="postertwitterwpmessage" id="icon_prefix2" class="materialize-textarea validate" aria-required="true"><?php echo $message; ?></textarea>
                    <label for="icon_prefix2"><?php _e('Mensaje', poster_twitter_wp_ptwp()->nameClean(true)); ?></label>
                    <input type="hidden" name="iduser" value="<?php echo $id; ?>">
                </div>
                <div class="input-field">
                    <a id="poster-twitter-wp-reset" data-id="<?php echo $id; ?>" class="waves-effect waves-light btn tooltipped" data-position="right" data-tooltip="<?php _e('Reinicia el proceso al inicio de login', poster_twitter_wp_ptwp()->nameClean(true)); ?>"><i class="material-icons left"></i><?php _e('Resetear', poster_twitter_wp_ptwp()->nameClean(true)); ?></a>
                </div>
                <button class="btn waves-effect waves-light" type="submit" name="action">Guardar Cambios
                    <i class="material-icons right">save</i>
                </button>
            </form>
        </div>
            <?php
        }
    ?>
        </div>
        <?php
    }
}