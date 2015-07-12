<?php

/* Version check */
if (version_compare(PHP_VERSION, '5.3.0') < 0) {
  add_action('admin_notices', 'recast_bad_version');
  function recast_bad_version()
  {
    ?>
    <div class="error">
        <p><?php _e( 'PHP 5.3 is required to run Recast.', 'recast' ); ?></p>
    </div>
    <?php
  }
  return;
}

require(dirname(__FILE__)."/../vendor/autoload.php");
require_once('lib/compat.php');
require_once('lib/debug.php');
require_once('lib/cpt.php');
require_once('lib/metaboxes.php');

if ( file_exists( dirname( __FILE__ ) . '/../cmb2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/../cmb2/init.php';
}


new Recast();
