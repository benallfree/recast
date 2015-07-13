<?php
  

function recast_admin() {
	static $object = null;
	if ( is_null( $object ) ) {
		$object = new RecastAdmin();
		$object->hooks();
	}

	return $object;
}

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string  $key Options array key
 * @return mixed        Option value
 */
function recast_get_option( $key = '' ) {
	return cmb2_get_option( recast_admin()->key, $key );
}


recast_admin();