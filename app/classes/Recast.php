<?php
  
class Recast
{
  function __construct()
  {
    add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'plugin_action_links' );
    add_action('wp_insert_post', array($this, 'schedule_refresh'),10,3);
    add_action('all', array($this, 'refresh_podcast'));
    add_shortcode('recast', array($this, 'recast_shortcode'));
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
  
  function refresh_podcast($action_name)
  {
    if(!preg_match("/recast_cron_/", $action_name)) return;
    self::log($action_name);
    self::log(func_get_args());
    list($junk, $junk, $post_id) = explode('_', $action_name);
    self::log($post_id);
    $pu = new PublisherUpdater();
    $pu->update($post_id);
  }
  
  function plugin_action_links( $links ) {
     $links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=recast_instruction_page') ) .'">Settings</a>';
     return $links;
  }
  
  function schedule_refresh($post_id, $post, $is_update)
  {
    if(!$post->post_type=='podcast') return;
    $action_name = 'recast_cron_'.$post_id;
    if (wp_next_scheduled($action_name))
    {
      wp_unschedule_event(wp_next_scheduled($action_name), $action_name);
    }
    self::log("Scheduling");
    wp_schedule_event( time(), 'daily', $action_name );
    $this->refresh_podcast($action_name);
  }
}
