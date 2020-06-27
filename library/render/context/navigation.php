<?php

namespace q\render;

use q\core\helper as h;
use q\ui;
use q\get;
use q\render;

class navigation extends \q\render {

	public static function __callStatic( $function, $args ) {

        return self::run( $args ); 
	
	}

	public static function run( $args = null ){

		// validate passed args ##
        if ( ! render\args::validate( $args ) ) {

            render\log::set( $args );

            return false;

		}

		// build $args['fields'] -- @todo -- this can be moved to a pre-function call ##
		// self::$args['fields'] = [];

		// h::log( 'd:>markup: '.$args['markup'] );
		// h::log( 'd:>field: '.$args['field'] );

		// build fields array with default values ##
		// render\method::set_fields([
		// 	$args['field'] => get\post::field( $args )
		// ]);

		// empty ##
		// $array = [];

		// field ##
		// $array['field'] = $args['field'];

        // grab classes ##
		// $array['value'] = get\post::field( $args );

		// @todo -- pass field by render/format ##
		// h::log( 't:>pass field by render/format' );
		// $array['value'] =  render\format::field( $field, $value );

		// h::log( $array );

        // return ##
		// return ui\method::prepare( $args, $array );

		// filter field data ##
		// self::$fields = \apply_filters( 'q/render/field/'.$args['field'], self::$fields, self::$args );

		// h::log( self::$fields );

		// check each field data and apply numerous filters ##
		render\fields::prepare();

		// h::log( self::$fields );

		// Prepare template markup ##
		render\markup::prepare();

		// h::log( 'd:>markup: '.$args['markup'] );

        // optional logging to show removals and stats ##
        // render\log::set( $args );

        // return or echo ##
        return render\output::return();

    }


}
