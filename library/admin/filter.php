<?php

namespace q\admin;

use q\core\helper as h;

class filter {

	function __construct(){}

    function hooks(){

        if ( \is_admin() ) {

            // filter admin preview link ##
            \add_filter( 'preview_post_link', [ $this, 'preview_post_link' ], 10, 2 );

            // Add Filter Hook
            \add_filter( 'post_mime_types', array( $this, 'post_mime_types' ) );

        }

    }

    /**
     * Fix for broken preview link in admin - link to default url ?p=ID
     * 
     * @since       0.1.0
     */
    function preview_post_link( $preview_link, $post ){

        if ( 
            \get_post_status ( $post->ID ) != 'draft' 
            && \get_post_status ( $post->ID ) != 'auto-draft' 
        ) {
      
            // preview URL for all published posts ##
            return \home_url()."?p=".$post->ID; 
      
         } else {
            
            // preview URL for all posts which are in draft ##
            return \home_url()."?p=".$post->ID; 

        }

    }
    
    /**
     * Add filters to WP Media Library
     *
     * @since       1.4.2
     * @return      Array
     */
    function post_mime_types( $post_mime_types ){

        // select the mime type, here: 'application/pdf'
        // then we define an array with the label values
        $post_mime_types['application/pdf'] = array(
            __( 'PDF' ),
            __( 'Show PDF' ),
            \_n_noop( 'PDF <span class="count">(%s)</span>', 'PDFs <span class="count">(%s)</span>' )
        );

        // then we return the $post_mime_types variable
        return $post_mime_types;

    }

}
