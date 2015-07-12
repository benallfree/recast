<?php
  
class Recast
{
  function __construct()
  {
    add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'plugin_action_links' );
    add_action('wp_insert_post', array($this, 'refresh_podcast'),10,2);
//    add_action('all', array($this, 'refresh_podcast'));
    add_shortcode('recast', array($this, 'recast_shortcode'));
    add_action('recast_process_episode', array('PublisherUpdater', 'process_epsiode'));
    $this->is_refreshing = false;
  }
  
  function recast_shortcode($attrs, $content='')
  {
    $content .= 'hello world';
    return $content;
  }
  
  static function log($s)
  {
    $json = json_encode($s);
    error_log("Recast: {$json}");
  }
  
  function cron_refresh($action_name)
  {
    self::log($action_name);
    list($junk, $junk, $post_id) = explode('_', $action_name);
    $post = get_post($post_id);
    $this->refresh_podcast($post_id, $post);
  }
  
  function refresh_podcast($post_id, $post)
  {
    if($this->is_refreshing || $post->post_type!='podcast') return;
    $this->is_refreshing = true;
    $pu = new PublisherUpdater();
    $pu->update($post_id);
    $this->is_refreshing = false;
    
    /* Schedule auto-refresh */
    $action_name = 'recast_cron_'.$post_id;
    if (wp_next_scheduled($action_name))
    {
      wp_unschedule_event(wp_next_scheduled($action_name), $action_name);
    }
    wp_schedule_event( time()+3600, 'daily', $action_name ); // Run again in an hour
  }
  
  function plugin_action_links( $links ) {
     $links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=recast_instruction_page') ) .'">Settings</a>';
     return $links;
  }
}
