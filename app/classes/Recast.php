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
  
  function make($fname, $args)
  {
    $s = file_get_contents($fname);
    $s = preg_replace('/@foreach(.*?)\n/', "<?php foreach\$1: ?>\n" , $s);
    $s = preg_replace('/@endforeach\n/', "<?php endforeach; ?>\n" , $s);
    $s = preg_replace('/@if(.*?)\n/', "<?php if\$1: ?>\n" , $s);
    $s = preg_replace('/@else\n/', "<?php else: ?>\n" , $s);
    $s = preg_replace('/@endif\n/', "<?php endif; ?>\n" , $s);
    $s = preg_replace("/{{{(.*?)}}}/", '<?php echo(htmlentities($1)) ?>', $s);
    $s = preg_replace("/{{(.*?)}}/", '<?php echo($1) ?>', $s);
    $tmpfname = tempnam(sys_get_temp_dir(), "recast_");
    file_put_contents($tmpfname, $s);
    add_filter( 'wp_audio_extensions', array($this, 'trust_audio_extensions'));
    $content = $this->sandbox($tmpfname, $args);
    unlink($tmpfname);
    return do_shortcode($content);
  }
  
  function trust_audio_extensions($ext)
  {
    remove_filter( current_filter(), __FUNCTION__ );
    $ext[] = '';
    return $ext;
  }
  
  function sandbox($__fname, $__vars)
  {
    extract($__vars);
    ob_start();
    require($__fname);
    return ob_get_clean();
  }
  
  function url($commands = array())
  {
    $parts = parse_url($_SERVER['REQUEST_URI']);
    parse_str($parts['query'], $vars);
    $vars[RECAST_QS_SCOPE] = $commands;
    $qs = http_build_query($vars);
    if($qs) $qs = "?{$qs}";
    return $parts['path'].$qs;
  }
  
  function q($name, $default=null)
  {
    if(!isset($_GET[RECAST_QS_SCOPE])) return $default;
    if(!isset($_GET[RECAST_QS_SCOPE][$name])) return $default;
    return $_GET[RECAST_QS_SCOPE][$name];
  }
  
  function recast_shortcode($attrs, $content='')
  {
    if($this->q('p'))
    {
      $podcast = Podcast::find($this->q('p'));
      $fname = dirname(__FILE__)."/../views/podcasts/view.php";
      return $this->make($fname, array('podcast'=>$podcast));
    } else {
      $args = array(
        'meta_key'=>'episode_count',
        'orderby'=>'meta_value_num desc'
      );
      $podcasts = Podcast::all($args);
      $fname = dirname(__FILE__)."/../views/podcasts/list.php";
      return $this->make($fname, array('podcasts'=>$podcasts));
    }
    
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
