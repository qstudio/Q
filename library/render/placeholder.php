<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\ui;
use q\render;

class placeholder extends \q\render {


    /**
     * Get all placeholders from passed string value 
     *  
     */
    public static function get_all( string $string = null ) {
        
        // sanity ##
        if (
            is_null( $string ) 
        ) {

			// log ##
			h::log( self::$args['task'].'~>e:>No string value passed to method' );

            return false;

        }

		$regex_find = \apply_filters( 'q/render/markup/placeholders/get', '~\{{\s(.*?)\s\}}~' );
		// $regex_find = \apply_filters( 'q/render/markup/placeholders/get', '~\%(.*?)\%~' );
		// if ( ! preg_match_all('~\%(\w+)\%~', $string, $matches ) ) {
        if ( ! preg_match_all( $regex_find, $string, $matches ) ) {

			// log ##
			h::log( self::$args['task'].'~>n:>No extra placeholders found in string to clean up - good!' );

            return false;

        }

        // test ##
        // h::log( $matches[0] );

        // kick back placeholder array ##
        return $matches[0];

    }



    /**
     * Check if single placeholder exists 
     * @todo - work on passed params 
     *  
     */
    public static function get( string $placeholder = null, $field = null ) {
		
		// if $markup template passed, check there, else check self::$markup ##
		$markup = is_null( $field ) ? self::$markup['template'] : self::$markup[$field] ;

        if ( ! substr_count( $markup, $placeholder ) ) {

            return false;

        }

        // good ##
        return true;

	}



	/**
     * Edit {{ placeholder }} in self:$args['markup']
     * 
     */
    public static function edit( string $placeholder = null, $new_placeholder = null ) {

        // sanity ##
        if (
			is_null( $placeholder ) 
			|| is_null( $new_placeholder )
		) {

			// log ##
			h::log( self::$args['task'].'~>e:>No placeholder or new_placeholder value passed to method' );

            return false;

		}
		
        // check if placeholder is correctly formatted --> {{ STRING }} ##
		$needle_start = '{{ ';
		$needle_end = ' }}';
		if (
			! render\method::starts_with( $placeholder, $needle_start ) 
			|| ! render\method::ends_with( $placeholder, $needle_end ) 
			|| ! render\method::starts_with( $new_placeholder, $needle_start ) 
			|| ! render\method::ends_with( $new_placeholder, $needle_end ) 
        ) {

			// log ##
			h::log( self::$args['task'].'~>e:>Placeholder is not correctly formatted - missing {{ at start or }} at end.' );
			// h::log( 'd:>Placeholder is not correctly formatted - missing {{ at start or end }}.' );

            return false;

		}
		
		// ok - we should be good to search and replace old for new ##
		$string = str_replace( $placeholder, $new_placeholder, self::$markup['template'] );

		// test new string ##
		// h::log( 'd:>'.$string );

		// overwrite markup property ##
		self::$markup['template'] = $string;

		// kick back ##
		return true;

	}
	
	

	/**
     * Set {{ placeholder }} in self:markup['template'] at defined position
     * 
     */
    public static function set( string $placeholder = null, $position = null ) { // , $markup = null

        // sanity ##
        if (
			is_null( $placeholder ) 
			// || is_null( $markup )
			|| is_null( $position )
		) {

			// log ##
			h::log( self::$args['task'].'~>e:Error in data passed to method' );

            return false;

		}
		
		// where are we replacing ##
		// $markup = ! \is_null( $markup ) ? $markup : self::$markup ;

		// h::log( $markup );

        // check if placeholder is correctly formatted --> {{ STRING }} ##
		$needle_start = '{{ ';
		$needle_end = ' }}';
        if (
            ! render\method::starts_with( $placeholder, $needle_start ) 
			|| ! render\method::ends_with( $placeholder, $needle_end ) 
        ) {

			// log ##
			h::log( self::$args['task'].'~>e:>Placeholder: "'.$placeholder.'" is not correctly formatted - missing {{ at start or }} at end.' );

            return false;

		}
		
		// h::log( 'd:>Adding placeholder: "'.$placeholder.'"' );

		// use strpos to get location of {{ placeholder }} ##
		// $position = strpos( self::$markup, $placeholder );
		// helper::log( 'Position: '.$position );

		// add new placeholder to $template as defined position - don't replace {{ placeholder }} yet... ##
		$new_template = substr_replace( self::$markup['template'], $placeholder, $position, 0 );

		// test ##
		// h::log( 'd:>'.$new_template );

		// push back into main stored markup ##
		self::$markup['template'] = $new_template;
		
		// h::log( 'd:>'.$markup );

		// log ##
		// h::log( self::$args['task'].'~>placeholder_added:>"'.$placeholder.'" @position: "'.$position.'" by "'.core\method::backtrace([ 'level' => 2, 'return' => 'function' ]).'"' );

        // positive ##
        return true; #$markup['template'];

    }



    /**
     * Remove {{ placeholder }} from self:$args['markup'] array
     * 
     */
    public static function remove( string $placeholder = null, $markup = null ) {

        // sanity ##
        if (
			is_null( $placeholder ) 
			|| is_null( $markup )
		) {

			// log ##
			h::log( self::$args['task'].'~>e:>No placeholder or markkup value passed to method' );

            return false;

		}
		
		// where are we replacing ##
		// $markup = ! \is_null( $markup ) ? $markup : self::$markup ;

		// h::log( $markup );
		// h::log( 'remove: '.$placeholder );

        // check if placeholder is correctly formatted --> {{ STRING }} ##
		$needle_start = '{{ ';
		$needle_end = ' }}';
        if (
            ! render\method::starts_with( $placeholder, $needle_start ) 
            || ! render\method::ends_with( $placeholder, $needle_end ) 
        ) {

			// log ##
			h::log( self::$args['task'].'~>e:>Placeholder: "'.$placeholder.'" is not correctly formatted - missing "{{ " at start or " }}" at end.' );

            return false;

		}
		
		// h::log( 'Removing placeholder: "'.$placeholder.'"' );

        // remove placeholder from markup ##
		$markup = 
			str_replace( 
            	$placeholder, 
            	'', // nada ##
            	$markup
			);
		
		// h::log( 'd:>'.$markup );

		// log ##
		h::log( self::$args['task'].'~>placeholder_removed:>"'.$placeholder.'" by "'.core\method::backtrace([ 'level' => 2, 'return' => 'function' ]).'"' );

        // positive ##
        return $markup;

    }



}
