<?php

namespace q\ui\field;

use q\core\helper as h;
use q\ui;
use q\ui\field;
use q\ui\wordpress as wp;

class type extends ui\field {

    
    /**
     * Image type handler 
     *  
     * 
     * @todo - add srcset check
     * @todo - placeholder fallback
     * @todo - what about different image methods ??
     **/ 
    public static function img( $value = null, $field = null ){

        // check and assign ##
        $handle = 
            isset( self::$args['img']['handle'][$field] ) ?
            self::$args['img']['handle'][$field] :
            \apply_filters( 'q/field/format/img/handle', 'medium' ); ;

        // helper::log( 'Image handle: '.$handle );

        $string = '';

        // helper::log( 'Image ID: '.$value );

        // get image ##
        $src =  \get_the_post_thumbnail_url( $value, $handle );

        $string = $src;

        // conditional -- add img meta values and srcset ##
        if ( 
            isset( self::$args['filter']['img'] )
            && 'srcset' == self::$args['filter']['img'] 
        ) {

            // $id = \get_post_thumbnail_id( $value );
            $srcset =  \wp_get_attachment_image_srcset( $value, $handle );
            $sizes =  \wp_get_attachment_image_sizes( $value, $handle );
            $alt = 
                \get_post_meta( $value, '_wp_attachment_image_alt', true ) ?
                \get_post_meta( $value, '_wp_attachment_image_alt', true ) :
                wp\get::the_excerpt_from_id( $value, 100 );

            // markup tag attributes ##
            $srcset = '" srcset="'.\esc_attr($srcset).'"'; 
            $sizes = ' sizes="'.\esc_attr($sizes).'"'; 
            $alt = ' alt="'.\esc_attr($alt).'"'; 

            $string = $src.$srcset.$sizes.$alt;

        }

        // helper::log( 'Image string: '.$string );

        // kick back ##
        return $string;

    }



    /**
     * Get allowed fomats with filter ##
     * 
     */
    public static function get_allowed()
    {

        return \apply_filters( 'q/field/formats/get', self::$formats );

    }



}