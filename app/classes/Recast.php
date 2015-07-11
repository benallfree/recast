<?php
  
class Recast
{
  function __construct()
  {
    add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'plugin_action_links' );
  }
  
  function plugin_action_links( $links ) {
     $links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=recast_instruction_page') ) .'">Settings</a>';
     return $links;
  }
}
new Recast();