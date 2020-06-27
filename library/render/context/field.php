<?php

namespace q\render;

use q\core\helper as h;
use q\ui;
use q\get;
use q\render;

class field extends \q\render {

	public static function __callStatic( $function, $args ) {

        return self::run( $args ); 
	
	}

	public static function run( $args = null ){

		// validate passed args ##
        if ( ! render\args::validate( $args ) ) {

            render\log::set( $args );

            return false;

		}

		// build $args['field'] -- 
		$args['field'] = $args['task'];

		// h::log( 'd:>markup: '.$args['markup'] );
		// h::log( 'd:>field: '.$args['field'] );

		// build fields array with default values ##
		render\fields::define([
			$args['task'] => get\meta::field( $args )
		]);

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