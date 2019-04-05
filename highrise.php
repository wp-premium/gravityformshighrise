<?php
/**
Plugin Name: Gravity Forms Highrise Add-On
Plugin URI: https://www.gravityforms.com
Description: Integrates Gravity Forms with Highrise, allowing form submissions to be automatically sent to your Highrise account.
Version: 1.3
Author: rocketgenius
Author URI: https://www.rocketgenius.com
License: GPL-2.0+
Text Domain: gravityformshighrise
Domain Path: /languages

------------------------------------------------------------------------
Copyright 2009 - 2019 rocketgenius
last updated: October 20, 2010

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */

define( 'GF_HIGHRISE_VERSION', '1.3' );

// If Gravity Forms is loaded, bootstrap the Highrise Add-On.
add_action( 'gform_loaded', array( 'GF_Highrise_Bootstrap', 'load' ), 5 );

/**
 * Class GF_Highrise_Bootstrap
 *
 * Handles the loading of the Highrise Add-On and registers with the Add-On Framework.
 */
class GF_Highrise_Bootstrap {

	/**
	 * If the Feed Add-On Framework exists, Highrise Add-On is loaded.
	 *
	 * @access public
	 * @static
	 */
	public static function load() {

		require_once( 'class-gf-highrise.php' );

		GFAddOn::register( 'GFHighrise' );

	}

}

/**
 * Returns an instance of the GFHighrise class
 *
 * @see    GFHighrise::get_instance()
 * @return object GFHighrise
 */
function gf_highrise() {
	return GFHighrise::get_instance();
}
