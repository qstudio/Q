<?php

namespace q\get;

// Q ##
use q\core;
use q\core\helper as h;
use q\ui;
use q\render;
use q\get;

// Q Theme ##
use q\theme;

class taxonomy extends \q\get {

	/**
	 * We need a generic get_taxonomy_terms method.. which distributes, based on post type and any passed tax / term ## 
	 */
	public static function terms( $args = null ){

		// global arg validator ##
		if ( ! $args = render\args::prepare( $args ) ){ 
	   
			// h::log( 'Bailing..' ); 
		
			return false; 
		
		}

		// try and get terms ##
		if ( 
			! $terms = \get_terms( $args['args'] )
		){
	
			h::log( 'd:>No terms found for taxonomy: '.$args['args']->taxonomy );
	
			return false;
	
		}

		// to highlight any active term, we get to know the first term->term_id of the current post ##
		$active_term_id = '';
		if ( 
			$object_terms = get\post::object_terms([ 
				'config' 		=> [ 
					'post'		=> $args['config']['post']
				],
				'taxonomy'		=> 'category',
				'args' 			=> [
					'number'	=> 1
				]
			])
				
		){

			// h::log( 'e:>Returned terms good' );

			// we expect an array with 1 key [0] of WP_Term object - validate ##
			if (
				is_array( $object_terms )
				&& isset( $object_terms[0] )
				&& $object_terms[0] instanceof \WP_Term
			){

				// h::log( 'e:>Term object good, getting ID' );

				$active_term_id = $object_terms[0]->term_id; 

			}

		}

		// h::log( $terms );

		// prepare return array ##
		$array = [];

		foreach ( $terms as $term ) {

			if (
				! is_object( $term )
				|| ! $term instanceof \WP_Term
			) {

				h::log( 'e:>Error in returned term' );

				continue;

			}

			// array key ##
			$key = $term->term_id;

			// add values ##
			$array[ $key ]['permalink'] = \get_term_link( $term );
			$array[ $key ]['slug'] = $term->slug;
			$array[ $key ]['active'] = $term->term_id === $active_term_id ? ' active' : '' ; // are we viewing this term ##
			$array[ $key ]['title'] = $term->name;

		}

		// h::log( $array );

		// return ##
		return get\method::prepare_return( $args, $array );

	}



}
