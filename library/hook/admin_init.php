<?php

/**
 * WP_Head Function
 *
 * clean up things we don't want
 * add things we do want
 *
 * filters and actions ##
 *
 * @link        http://codex.wordpress.org/Plugin_API/Action_Reference
 * @since       0.1
 */

namespace q\hook;

class admin_init {

    function __construct(){}
	
	function hooks() {

        \add_action( 'admin_head', array ( $this, 'favicon' ), 9999999 ); // add to backend ##

    }

    /**
     * favicon function ##
     * reference favicon.png in header if found in top directory of child or parent theme ##
     * include favicon.ico on IE if found ##
     */
    function favicon(){

        // h::log( 'Adding favicon...' );

?>
        <link rel="icon" type="image/png" href="<?php echo \get_site_url( '1' ); ?>/favicon.png" /><!-- Major Browsers -->
        <!--[if IE]><link rel="SHORTCUT ICON" href="/favicon.ico" /><![endif]--><!-- Internet Explorer-->
<?php

           # }
    }

}
