<?php

namespace q\context;

use q\core; // core functions, options files ##
use q\core\helper as h; // helper shortcut ##
use q\plugin; // plugins ## 
// use q\ui; // template, ui, markup... ##
use q\get; // wp, db, data lookups ##
use q\context; // self ##
use q\render; 

// Q Theme ##
use q\theme;

class post extends \q\context {


	/**
     * Generic H1 title tag
     *
     * @param       Array       $args
     * @since       1.3.0
     * @return      String
     */
    public static function title( $args = null ) {

		// get title - returns array with key 'title' ##
		render\fields::define(
			get\post::title( $args )
		);

    }



	/**
     * Post Meta details.. 
     *
     * @param       Array       $args
     * @since       1.4.1
     * @return      String
     */
    public static function data( $args = null ) {

		// get title - returns array with key 'title' ##
		render\fields::define(
			get\post::data( $args )
		);

    }

	
    /**
    * Render WP_Query
    *
    * @since       1.0.2
    */
    public static function query( $args = [] )
    {

		// h::log( self::$markup );
		// h::log( render::$args );

		// build fields array with default values ##
		render\fields::define([
			'total' 		=> '0', // set to zero string value ##
			'pagination' 	=> null, // empty field.. ##
			'results' 		=> render::$markup['empty'] // replace results with empty markup ##
		]);

        // pass to get_posts -- and validate that we get an array back ##
		if ( ! $array = get\query::posts( $args ) ) {

			// log ##
			h::log( render::$args['task'].'~>n:query::posts did not return any data');

		}

		// validate what came back - it should include the WP Query, posts and totals ##
		if ( 
			! isset( $array['query'] ) 
			|| ! isset( $array['query']->posts ) 
			// || ! isset( $array['query']->posts ) 
		){

			// h::log( 'Error in data returned from query::posts' );

			// log ##
			h::log( render::$args['task'].'~>n:Error in data returned from query::posts');

		}
		
		// no posts.. so empty, set count to 0 and no pagination ##
		if ( 
			empty( $array['query']->posts )
			|| 0 == count( $array['query']->posts )
		){

			// h::log( 'No results returned from the_posts' );
			h::log( render::$args['task'].'~>n:No results returned from query::posts');

		// we have posts, so let's add some charm ##
		} else {

			// merge array into args ##
			$args = core\method::parse_args( $array, $args );

			// h::log( $array['query']->found_posts );

			// define all required fields for markup ##
			render::$fields = [
				'total' 		=> $array['query']->found_posts, // total posts ##
				'pagination'	=> get\navigation::pagination( $args ), // get pagination, returns string ##
				'results'		=> $array['query']->posts // array of WP_Posts ##
			];

		}

		// ok ##
		return true;

    }

	


    /**
	 * Helper Method to get parent
	 */
	public static function parent( $args = null ){

		// get parent - returns false OR array with key 'title, slug, permalink' ##
		render\fields::define( 
			get\post::parent( $args ) 
		);

	}



	/**
	 * Helper Method to get the_excerpt
	 */
	public static function excerpt( $args = null ){

		render\fields::define( 
			get\post::excerpt( $args ) 
		);

	}




	/**
	 * Helper Method to get the_content
	 */
	public static function content( $args = null ){

		// get content - returns array with key 'content' ##
		render\fields::define( 
			get\post::content( $args ) 
		);

	}


	/**
	 * Helper Method to get_the_date
	 */
	public static function date( $args = null ){

		// get content - returns array with key 'content' ##
		render\fields::define( 
			get\post::date( $args ) 
		);

	}


}
