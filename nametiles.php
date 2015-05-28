<?php
/*
Plugin Name: Nametiles
Plugin URI: http://wordpress.org/extend/plugins/nametiles/
Description: Enables <a href="https://nametiles.co">Nametiles</a> & <a href="https://passcard.info">Passcard</a> support for your blog.
Version: 1.2.0
Author: Larry Salibra
Author URI: https://www.larrysalibra.com/
*/





if (!class_exists("Passcard")) {
  class Passcard {
    private $passcard_endpoint;
    private $json;
    private $loaded = false;
    public static $default_endpoint = 'https://api.nametiles.co/v1/users/';

    function __construct($passname) {
      $this->$passname = $passname;
      $this->passcard_endpoint = get_option('passcard_endpoint');
      $url = $this->passcard_endpoint.$passname.".json";

      $results = wp_remote_get( $url, array( 'timeout' => -1 ) );
      // Checking for WP Errors
      if ( is_wp_error( $results ) || wp_remote_retrieve_response_code($results) != 200) {
        throw new Exception("There was an error loading the Passcard: '".$passname."'");
      }
      $this->json = json_decode( $results['body'], true);
      if($this->json["v"] != "0.2") {
        error_log(print_r("The Nametiles plugin only current supports BNS schema v0.2.",false));
      }
      $this->loaded = true;

    }

    function passname() {
      return $this->$passname;
    }


    function angellist_username() {
      return sanitize_text_field($this->json["angelist"]["username"]);
    }

    function avatar_url() {
      return sanitize_text_field($this->json["avatar"]["url"]);
    }

    function bio() {
      return sanitize_text_field($this->json["bio"]);
    }

    function bitcoin_address() {
      return sanitize_text_field($this->json["bitcoin"]["address"]);
    }

    function cover_url() {
      return sanitize_text_field($this->json["cover"]["url"]);
    }

    function facebook_username() {
      return sanitize_text_field($this->json["facebook"]["username"]);
    }

    function github_username() {
      return sanitize_text_field($this->json["github"]["username"]);
    }

    function instagram_username() {
      return sanitize_text_field($this->json["instagram"]["username"]);
    }

    function linkedin_url() {
      return sanitize_text_field($this->json["linkedin"]["url"]);
    }

    function location_formatted() {
      return sanitize_text_field($this->json["location"]["formatted"]);
    }

    function name_formatted() {
      return sanitize_text_field($this->json["name"]["formatted"]);
    }

    function pgp_fingerprint() {
      return sanitize_text_field($this->json["pgp"]["fingerprint"]);
    }

    function pgp_url() {
      return sanitize_text_field($this->json["pgp"]["url"]);
    }

    function twitter_username() {
      return sanitize_text_field($this->json["twitter"]["username"]);
    }

    function website() {
      return sanitize_text_field($this->json["website"]);
    }

    function schema_version() {
      return sanitize_text_field($this->json["v"]);
    }
  }
}




function activate_nametiles() {
  add_option('passcard_endpoint', Passcard::$default_endpoint);
  add_option('nametiles_api_key', '');
}

function deactive_nametiles() {
  delete_option('passcard_endpoint');
  delete_option('nametiles_api_key');
}

function admin_init_nametiles() {
  register_setting('nametiles', 'passcard_endpoint', 'passcard_endpoint_validate');
  register_setting('nametiles', 'nametiles_api_key',"nametiles_api_key_validate");
}

function admin_menu_nametiles() {
  add_options_page('Nametiles', 'Nametiles', 'manage_options', 'nametiles', 'options_page_nametiles');
}

function options_page_nametiles() {
  include(WP_PLUGIN_DIR.'/nametiles/options.php');
}


register_activation_hook(__FILE__, 'activate_nametiles');
register_deactivation_hook(__FILE__, 'deactive_nametiles');

if (is_admin()) {
  add_action('admin_init', 'admin_init_nametiles');
  add_action('admin_menu', 'admin_menu_nametiles');
}


function passcard_endpoint_validate($input) {
  $newinput = esc_url_raw($input);
  $newinput = empty($newinput) ? Passcard::$default_endpoint : $newinput;
  return $newinput;
}

function nametiles_api_key_validate($input) {
  $newinput = sanitize_text_field($input);
  return $newinput;
}


/**
* Adds the Passcard section in the user profile page.
*
* @param object $profileuser Contains the details of the current profile user
*
* @return string $html Passcard section in the user profile page
*/
function nametiles_add_extra_profile_fields( $profileuser ) {

  // Getting the usermeta
  $passname = get_user_meta( $profileuser->ID, 'passname', true );
  $passcard_avatar_enabled = get_user_meta( $profileuser->ID, 'passcard_avatar_enabled', true );

  // Passcard section html in the user profile page.
  $html  = '';
  $html .= '<h3>Passcard</h3>';
  $html .= '<table class="form-table">';
  $html .= '<tr><th><label for="passname">Your Passcard</label></th>';
  $html .= '<td><input type="text" name="passname" id="passname" value="' . $passname . '" class="regular-text" required pattern="[a-z0-9_]{1,60}" /></td>';
  $html .= '<tr><th><label for="passcard_avatar_enabled">Use Passcard Avatar as Avatar</label></th>';
  $html .= '<td><input id="passcard_avatar_enabled" type="checkbox" name="passcard_avatar_enabled" value="passcard_avatar_enabled" ' . checked( $passcard_avatar_enabled, TRUE, false ) . '></td></tr>';
  $html .= '</table>';

  echo $html;
}
add_action( 'show_user_profile', 'nametiles_add_extra_profile_fields' );
add_action( 'edit_user_profile', 'nametiles_add_extra_profile_fields' );

/**
* Save Passcard details in the wp usermeta table.
*
* @param int $user_id id of the current user.
*
* @return void
*/
function nametiles_save_extra_profile_fields( $user_id ) {
  $safe_passname =  sanitize_text_field($_POST['passname']);
  $safe_passcard_avatar_enabled = $_POST['passcard_avatar_enabled'] == null ? FALSE : TRUE;

  if(preg_match("/^[a-z0-9_]{1,60}$/", $safe_passname)) {
    update_usermeta( $user_id, 'passname', $safe_passname );
    update_usermeta( $user_id, 'passcard_avatar_enabled', $safe_passcard_avatar_enabled);
    delete_transient( "passcard_avatar_url_{$user_id}" );
  }
}
add_action( 'personal_options_update', 'nametiles_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'nametiles_save_extra_profile_fields' );



/**
* Replaces the default avatar with Passcard avatar
*
* @param string $avatar The default avatar
*
* @param int $id_or_email The user id
*
* @param int $size The size of the avatar
*
* @param string $default The url of the Wordpress default avatar
*
* @param string $alt Alternate text for the avatar.
*
* @return string $avatar The modified avatar
*/
function passcard_avatar( $avatar, $id_or_email, $size, $default, $alt ) {

  // Getting the user id.
  if ( is_int( $id_or_email ) )
  $user_id = $id_or_email;

  if ( is_object( $id_or_email ) )
  $user_id = $id_or_email->user_id;

  if ( is_string( $id_or_email ) ) {
    $user = get_user_by( 'email', $id_or_email );
    if ( $user )
    $user_id = $user->ID;
    else
    $user_id = $id_or_email;
  }



  // Getting the user details
  $passcard_avatar_enabled    = get_user_meta( $user_id, 'passcard_avatar_enabled', true );
  $passname    = get_user_meta( $user_id, 'passname', true );
  if ( "1" == $passcard_avatar_enabled && ! empty( $passname ) ) {
    if ( false === ( $passcard_avatar_url = get_transient( "passcard_avatar_url_{$user_id}" ) ) ) {

      try {
        $passcard = new Passcard($passname);
      } catch(Exception $e) {
        error_log(print_r($e->getMessage(),true));
        add_action( 'admin_notices', 'passcard_cant_be_loaded' );
        return $avatar;
      }

      $passcard_avatar_url = $passcard->avatar_url();

      // Setting Gplus url for 48 Hours
      set_transient( "passcard_avatar_url_{$user_id}", $passcard_avatar_url, 1 * HOUR_IN_SECONDS );



      $avatar = "<img alt='+{$passname}'s avatar'' src='{$passcard_avatar_url}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";

    } else {
      $avatar = "<img alt='+{$passname}'s avatar'' src='{$passcard_avatar_url}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
    }
    return $avatar;
  } else {
    return $avatar;
  }
}

add_filter( 'get_avatar', 'passcard_avatar', 10, 5 );

function passcard_cant_be_loaded() {
  echo "<div class=\"error\"><p>The Passcard passname you've entered either doesn't exist or there's a problem with the endpoint.</p></div>";
}

function nametiles_header() {
    ?>
    <script type="text/javascript">
    <?php
     $nametiles_api_key = get_option('nametiles_api_key');
      if(!empty($nametiles_api_key)) {
        ?>

    NametilesConfig = {
      apiKey: "<?php echo $nametiles_api_key ?>"
    };

    <?php } ?>
    (function() {
      var nt = document.createElement('script');
      nt.src = 'https://js.nametiles.co/v1/nt.js';
      nt.type = 'text/javascript';
      nt.async = 'true';
      var s = document.getElementsByTagName('script')[0];
      s.parentNode.insertBefore(nt, s);
    })();
    </script>
    <?php
}
if (!is_admin()) {
  add_action('wp_head', 'nametiles_header');
}


/**
 * Display an HTML link to the author page of the author of the current post.
 *
 * Does just echo get_author_posts_url() function, like the others do. The
 * reason for this, is that another function is used to help in printing the
 * link to the author's posts.
 *
 * @link http://codex.wordpress.org/Template_Tags/the_author_posts_link
 * @since 1.2.0
 * @param string $deprecated Deprecated.
 */
function nametiles_the_author_posts_link($deprecated = '') {
	if ( !empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '2.1' );

	global $authordata;
	if ( !is_object( $authordata ) )
		return false;

    $link = "";
    $passname     = get_user_meta( $authordata->ID, 'passname', true );
    if ( ! empty( $passname ) ) {

        $link = sprintf(
            '<a href="%1$s" title="%2$s" rel="author" data-passname="%4$s">%3$s</a>',
    		esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ),
    		esc_attr( sprintf( __( 'Posts by %s' ), get_the_author() ) ),
    		get_the_author(),
            $passname
    	);
    } else {
        $link = sprintf(
            '<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
    		esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ),
    		esc_attr( sprintf( __( 'Posts by %s' ), get_the_author() ) ),
    		get_the_author()
    	);
    }



	/**
	 * Filter the link to the author page of the author of the current post.
	 *
	 * @since 2.9.0
	 *
	 * @param string $link HTML link.
	 */
	echo apply_filters( 'nametiles_the_author_posts_link', $link );
}

add_filter('the_author_posts_link', 'nametiles_the_author_posts_link');


?>
