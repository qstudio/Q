<?php

namespace q\extension\device;

// Q ##
use q\core;

// Q Theme
use q\theme\core\helper as h;

// load it up ##
\q\extension\device\option::run();

class option extends \Q {

    public static function run()
    {

		// add extra options in extension select API ##
		\add_filter( 'acf/load_field/name=q_option_extension', [ get_class(), 'filter_acf_extension' ], 10, 1 );
		
    }




	/**
     * Add new libraries to Q Settings via API
     * 
     * @since 2.3.0
     */
    public static function filter_acf_extension( $field )
    {

        // h::log( $field['choices'] );
        // h::log( $field['default_value'] );

		// pop on a new choice ##
		$field['choices']['device'] = 'Device Detection';
		// $field['choices']['banner'] = '@todo - News Banner';

		// make it selected ##
		$field['default_value'][0] = 'device';
		
        // h::log( $field['choices'] );
        // h::log( $field['default_value'] );

         return $field;

	}
	


}
