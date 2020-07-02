<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\ui;
use q\render;

class markup extends \q\render {

	// track wrapping ##
	protected static $wrapped = false;

    /**
     * Apply Markup changes to passed template
     * find all placeholders in self::$markup and replace with matching values in self::$fields
     * 
     */
    public static function prepare(){

		// reset ##
		self::$wrapped = false;

        // sanity checks ##
        if (
            ! isset( self::$fields )
            || ! is_array( self::$fields )
			|| ! isset( self::$markup )
			|| ! is_array( self::$markup )
			|| ! isset( self::$markup['template'] ) // default markup property ##
        ) {

			// log ##
			h::log( self::$args['task'].'~>e:>Error with passed $args');

            return false;

		}
		
        // test ##
        // helper::log( self::$fields );
		// helper::log( self::$markup );
		
		// pre-format markup to extract comments ##
		// self::comments();

        // new string to hold output ## 
		$string = self::$markup['template'];
		
        // loop over each field, replacing placeholders with values ##
        foreach( self::$fields as $key => $value ) {

			// cast booleans to integer ##
			if ( \is_bool( $value ) ) {

				// @todo - is this required ?? ##
				// $value = (int) $value;

			}

            // we only want integer or string values here -- so check and remove, as required ##
            if ( 
				! \is_string( $value ) 
				&& ! \is_int( $value ) 
			) {

				// h::log( 'The value of: '.$key.' is not a string or integer - so we cannot render it' );

				// log ##
				h::log( self::$args['task'].'~>n:>The value of: "'.$key.'" is not a string or integer - so it will be skipped and removed from markup...');

                unset( self::$fields[$key] );

                continue;

            }

			// h::log( 'working key: '.$key.' with value: '.$value );
			
			// markup string, with filter and wrapper lookup ##
			$string = self::string([ 'key' => $key, 'value' => $value, 'string' => $string ]);

		}
		
		// optional wrapper, html passed in markup->wrap with {{ content }} placeholder ##
		$string = self::wrap([ 'string' => $string ]);

        // helper::log( $string );

        // check for any left over placeholders - remove them ##
        if ( 
            $placeholders = placeholder::get( $string ) 
        ) {

			// log ##
			h::log( self::$args['task'].'~>n:>"'.count( $placeholders ) .'" placeholders found in formatted string - these will be removed');

            // h::log( $placeholders );

            // remove any leftover placeholders in string ##
            foreach( $placeholders as $key => $value ) {
            
                $string = placeholder::remove( $value, $string );
            
            }

        }

        // filter ##
        $string = core\filter::apply([ 
            'parameters'    => [ 'string' => $string ], // pass ( $string ) as single array ##
            'filter'        => 'q/render/markup/'.self::$args['task'], // filter handle ##
            'return'        => $string
        ]); 

        // check ##
        // h::log( 'd:>'.$string );

        // apply to class property ##
        return self::$output = $string;

        // return ##
        return true;

	}



	/**
	 * filter passed args for markup
	 * 
	 * @since 4.1.0
	*/
	public static function pre_validate( $args = null ){

		// sanity ##
		if (
			is_null( $args )
		){

			h::log( 'd:>No $args sent from calling method' );

			return false;

		}
		
        // test args sent from view caller ##
		// h::log( $args );

		// empty stored markup ##
		self::$markup = [];

		// convert string passed args, presuming it to be markup...??... ##
		if ( is_string( $args ) ) {

			// create args array ##
			// $args = [];

			// h::log('d:>Using string markup' );

			// add markup->template ##
			return self::$markup = [
				// 'markup' => [
					'template' => $args
				// ]
			];

		} 
		
		// if(
		// 	is_array( $args )
		// 	&& isset( $args['markup'] )
		// ) {

		// 	self::$markup = $args['markup'];

		// }

		// if "markup" set in args, take this ##
		if ( 
			is_array( $args )
			&& isset( $args['markup'] ) 
		){

			// passed markup is an array - so take all values ##
			if ( 
				is_array( $args['markup'] ) 
				// && isset( $args['markup']['template'] ) // we can't validate "template" yet, as it might be pulled from config
			) {

				// h::log('d:>Using array markup' );

				return self::$markup = $args['markup'];

			} else {

				// h::log('d:>Using single markup' );

				return self::$markup['template'] = $args['markup'];

			}

		}

		// kick back ##
		return false;

	}

	

	/**
	 * $markup is set, so now we need to merge in any new markup values returned from get::config()
	 * 
	 * @since 4.1.0
	*/
	public static function merge(){

		// sanity ##
		if (
			is_null( self::$args )
			// || is_array( self::$args )
		){

			h::log( 'd:>No $args available or corrupt' );

			return false;

		}
		
        // test ##
		// h::log( $args );

		// make an array ##
		if (
			! self::$markup
			|| ! isset( self::$markup )
			|| empty( self::$markup )
			|| ! is_array( self::$markup )
		){
			
			// h::log( 'd:>Create markup array...' );

			self::$markup = []; 
	
		}

		// for ##
		$for = ' for: '.method::get_context();

		// we only accept correctly formatted markup from config ##
		if (
			isset( self::$args['markup'] ) 
		) {

			// config has a single markup value, take ##
			if (
				is_string( self::$args['markup'] )
			){

				// h::log( 'adding additional single markup from config'.$for );
				// h::log( self::$args['markup'] );
				// h::log( self::$markup );

				// take as main template ##
				$markup['template'] = self::$args['markup'];

			}

			// config passed an array fo values ##
			if ( is_array( self::$args['markup'] ) ) {

				// h::log( 'adding additional array of markup from config'.$for );
				// h::log( self::$args['markup'] );
				// h::log( self::$markup );

				// take array or markup ##
				$markup = self::$args['markup'];

			}

			// merge into defaults -- view passed markup takes preference ##
			self::$markup = core\method::parse_args( self::$markup, $markup );

			// test ##
			// h::log( self::$markup );

			// return true;

		}

		// @todo no additional markup passes from config.. so we should check if we actually have a markup->template
		if (
			! isset( self::$markup['template'] )
		){

			// default -- almost useless - but works for single values.. ##
			$markup = tag::wrap([ 'open' => 'var_o', 'value' => 'value', 'close' => 'var_c' ]);

			// filter ##
			$markup = \apply_filters( 'q/render/markup/default', $markup );

			// note ##
			// h::log('e:>NOTE: Using default markup'.$for );

			// assign ##
			self::$markup['template'] = $markup;

		}

		// remove markup from args ##
		unset( self::$args['markup'] );

		// kick back ##
		return true;

	}



	

	/**
	 * Scan for functions in markup and convert to placeholders and $fields
	 * 
	 * @since 4.1.0
	*/
	public static function function(){

		// h::log( $args['key'] );

		// sanity -- this requires ##
		if ( 
			! isset( self::$markup )
			|| ! is_array( self::$markup )
			|| ! isset( self::$markup['template'] )
		){

			h::log( 'e:>Error in stored $markup' );

			return false;

		}

		// get markup ##
		$string = self::$markup['template'];

		// sanity ##
		if (  
			! $string
			|| is_null( $string )
		){

			h::log( self::$args['task'].'~>e:>Error in $markup' );

			return false;

		}

		// h::log('d:>'.$string);

		// get all sections, add markup to $markup->$field ##
		// note, we trim() white space off tags, as this is handled by the regex ##
		$open = trim( tag::g( 'fun_o' ) );
		$close = trim( tag::g( 'fun_c' ) );
		// $end = trim( tag::g( 'sec_e' ) );
		// $end_preg = str_replace( '/', '\/', ( trim( tag::g( 'sec_e' ) ) ) );
		// $end = '{{\/#}}';

		// h::log( 'open: '.$open. ' - close: '.$close. ' - end: '.$end );

		$regex_find = \apply_filters( 
			'q/render/markup/function/regex/find', 
			"/$open\s+(.*?)\s+$close/s"  // note:: added "+" for multiple whitespaces.. not sure it's good yet...
			// "/{{#(.*?)\/#}}/s" 
		);

		// h::log( 't:> allow for badly spaced tags around sections... whitespace flexible..' );
		if ( 
			preg_match_all( $regex_find, $string, $matches, PREG_OFFSET_CAPTURE ) 
		){

			// strip all section blocks, we don't need them now ##
			// $regex_remove = \apply_filters( 'q/render/markup/section/regex/remove', "/{{#.*?\/#}}/ms" );
			$regex_remove = \apply_filters( 
				'q/render/markup/function/regex/remove', 
				"/$open.*?$close/ms" 
				// "/{{#.*?\/#}}/ms"
			);
			self::$markup['template'] = preg_replace( $regex_remove, "", self::$markup['template'] ); 
		
			// preg_match_all( '/%[^%]*%/', $string, $matches, PREG_SET_ORDER );
			// h::log( $matches[1] );

			// sanity ##
			if ( 
				! $matches
				|| ! isset( $matches[1] ) 
				|| ! $matches[1]
			){

				h::log( 'e:>Error in returned matches array' );

				return false;

			}

			foreach( $matches[1] as $match => $value ) {

				// position to add placeholder ##
				if ( 
					! is_array( $value )
					|| ! isset( $value[0] ) 
					|| ! isset( $value[1] ) 
					|| ! isset( $matches[0][$match][1] )
				) {

					h::log( 'e:>Error in returned matches - no position' );

					continue;

				}

				// h::log( 'd:>Searching for section field and markup...' );

				$position = $matches[0][$match][1]; // take from first array ##
				// h::log( 'd:>position: '.$position );
				// h::log( 'd:>position from 1: '.$matches[0][$match][1] ); 

				// foreach( $matches[1][0][0] as $k => $v ){
				// $delimiter = \apply_filters( 'q/render/markup/comments/delimiter', "::" );
				// list( $field, $markup ) = explode( $delimiter, $value[0] );
				// $field = method::string_between( $matches[0][$match][0], '{{#', '}}' );
				// $markup = method::string_between( $matches[0][$match][0], '{{# '.$field.' }}', '{{/#}}' );

				$function = method::string_between( $matches[0][$match][0], $open, $close );
				// $markup = method::string_between( $matches[0][$match][0], $close, $end );

				// clean up ##
				$function = trim($function);

				// sanity ##
				if ( 
					! isset( $function ) 
					// || ! strstr( $function, '__' )
					// || ! isset( $markup ) 
				){

					h::log( 'e:>Error in returned match function' );

					continue; 

				}

				// default args ##
				$function_args = [];
				if ( 
					// $config_string = method::string_between( $value, '[[', ']]' )
					$config_string = method::string_between( $function, trim( tag::g( 'arg_o' )), trim( tag::g( 'arg_c' )) )
				){
	
					// store placeholder ##
					// $placeholder = $value;
	
					// $config_string = json_encode( $config_string );
					// h::log( $config_string );

					if ( 
						is_object( json_decode( $config_string ))
					){

						// @todo

					} else {



					}
	
					// // grab config JSON ##
					// $config_string = '{ "handle":{ "all":"square-sm", "lg":"vertical-lg" }, "string": "value" }';
					// $config_array = json_decode( $config_string );
					// $config_object = isset( $config_json[0] ) ? $config_json[0] : false ;
	
					// h::log( 'd:>config: '.$config_string );
					// h::log( $config_json );
					// h::log( $config_array );
	
					// sanity ##
					if ( 
						! $config_string
						// || ! is_array( $config_array )
						// || ! isset( $matches[0] ) 
						// || ! $matches[0]
					){
	
						h::log( self::$args['task'].'~>e:>No valid config found in function: '.$function ); // @todo -- add "loose" lookups, for white space '@s
						// h::log( 'd:>No config in placeholder: '.$placeholder ); // @todo -- add "loose" lookups, for white space '@s''
	
						continue;
	
					}
	
					// h::log( $matches[0] );
	
					// $field = trim( method::string_between( $value, '{{ ', '[[' ) );
					$function = str_replace( trim( tag::g( 'arg_o' )).$config_string.trim( tag::g( 'arg_c' )), '', $function );
	
					// h::log( 'function: '.$function );
						
					if ( is_object( json_decode( $config_string )) ) {
							
						foreach( $config_array as $k => $v ) {
		
							h::log( "d:>config_setting: ".$k );
							h::log( "d:>config_value: ".$v );
							
							$function_args[$k] = $v;
		
						}

					} else {

						// call_user_func_array requires an array, so casting here ##
						$function_args = [ $config_string ];

					}
	
				}

				// hash ##
				$hash = bin2hex( random_bytes(16) );

				// test what we have ##
				// h::log( 'd:>function: "'.$function.'"' );
				
				if ( function_exists( $function ) ) {

					render\fields::define([
						$hash => call_user_func_array( $function, $function_args )
					]);

				}

				// and now we need to add a placeholder "{{ $field }}" before this comment block at $position to markup->template ##
				$placeholder = tag::wrap([ 'open' => 'var_o', 'value' => $hash, 'close' => 'var_c' ]);
				placeholder::set( $placeholder, $position, 'variable' ); // '{{ '.$field.' }}'

			}

		}

	}



	/**
	 * Scan for sections in markup and convert to placeholders and $fields
	 * 
	 * @since 4.1.0
	*/
	public static function section(){

		// sanity -- this requires ##
		if ( 
			! isset( self::$markup )
			|| ! is_array( self::$markup )
			|| ! isset( self::$markup['template'] )
		){

			h::log( 'e:>Error in stored $markup' );

			return false;

		}

		// get markup ##
		$string = self::$markup['template'];

		// sanity ##
		if (  
			! $string
			|| is_null( $string )
		){

			h::log( self::$args['task'].'~>e:>Error in $markup' );

			return false;

		}

		// h::log('d:>'.$string);

		// get all sections, add markup to $markup->$field ##
		// note, we trim() white space off tags, as this is handled by the regex ##
		$open = trim( tag::g( 'sec_o' ) );
		$close = trim( tag::g( 'sec_c' ) );
		$end = trim( tag::g( 'sec_e' ) );
		$end_preg = str_replace( '/', '\/', ( trim( tag::g( 'sec_e' ) ) ) );
		// $end = '{{\/#}}';

		// h::log( 'open: '.$open. ' - close: '.$close. ' - end: '.$end );

		$regex_find = \apply_filters( 
			'q/render/markup/section/regex/find', 
			"/$open\s+(.*?)\s+$end_preg/s"  // note:: added "+" for multiple whitespaces.. not sure it's good yet...
			// "/{{#(.*?)\/#}}/s" 
		);

		// h::log( 't:> allow for badly spaced tags around sections... whitespace flexible..' );
		if ( 
			preg_match_all( $regex_find, $string, $matches, PREG_OFFSET_CAPTURE ) 
		){

			// strip all section blocks, we don't need them now ##
			// $regex_remove = \apply_filters( 'q/render/markup/section/regex/remove', "/{{#.*?\/#}}/ms" );
			$regex_remove = \apply_filters( 
				'q/render/markup/section/regex/remove', 
				"/$open.*?$end_preg/ms" 
				// "/{{#.*?\/#}}/ms"
			);
			self::$markup['template'] = preg_replace( $regex_remove, "", self::$markup['template'] ); 
		
			// preg_match_all( '/%[^%]*%/', $string, $matches, PREG_SET_ORDER );
			// h::debug( $matches[1] );

			// sanity ##
			if ( 
				! $matches
				|| ! isset( $matches[1] ) 
				|| ! $matches[1]
			){

				h::log( 'e:>Error in returned matches array' );

				return false;

			}

			foreach( $matches[1] as $match => $value ) {

				// position to add placeholder ##
				if ( 
					! is_array( $value )
					|| ! isset( $value[0] ) 
					|| ! isset( $value[1] ) 
					|| ! isset( $matches[0][$match][1] )
				) {

					h::log( 'e:>Error in returned matches - no position' );

					continue;

				}

				// h::log( 'd:>Searching for section field and markup...' );

				$position = $matches[0][$match][1]; // take from first array ##
				// h::log( 'd:>position: '.$position );
				// h::log( 'd:>position from 1: '.$matches[0][$match][1] ); 

				// foreach( $matches[1][0][0] as $k => $v ){
				// $delimiter = \apply_filters( 'q/render/markup/comments/delimiter', "::" );
				// list( $field, $markup ) = explode( $delimiter, $value[0] );
				// $field = method::string_between( $matches[0][$match][0], '{{#', '}}' );
				// $markup = method::string_between( $matches[0][$match][0], '{{# '.$field.' }}', '{{/#}}' );

				$field = method::string_between( $matches[0][$match][0], $open, $close );
				$markup = method::string_between( $matches[0][$match][0], $close, $end );

				// sanity ##
				if ( 
					! isset( $field ) 
					|| ! isset( $markup ) 
				){

					h::log( 'e:>Error in returned match key or value' );

					continue; 

				}

				// clean up ##
				$field = trim($field);
				$markup = trim($markup);

				// test what we have ##
				// h::log( 'd:>field: "'.$field.'"' );
				// h::log( "d:>markup: $markup" );

				// so, we can add a new field value to $args array based on the field name - with the markup as value
				// self::$args[$field] = $markup;
				self::$markup[$field] = $markup;

				// and now we need to add a placeholder "{{ $field }}" before this comment block at $position to markup->template ##
				// placeholder::set( "{{ $field }}", $position ); // , $markup
				$placeholder = tag::wrap([ 'open' => 'var_o', 'value' => $field, 'close' => 'var_c' ]);
				placeholder::set( $placeholder, $position, 'variable' ); // '{{ '.$field.' }}'

			}

		}

	}



	
	/**
	 * Scan for functions in markup and convert to placeholders and $fields
	 * 
	 * @since 4.1.0
	*/
	public static function partial(){

		// h::log( $args['key'] );

		// sanity -- this requires ##
		if ( 
			! isset( self::$markup )
			|| ! is_array( self::$markup )
			|| ! isset( self::$markup['template'] )
		){

			h::log( 'e:>Error in stored $markup' );

			return false;

		}

		// get markup ##
		$string = self::$markup['template'];

		// sanity ##
		if (  
			! $string
			|| is_null( $string )
		){

			h::log( self::$args['task'].'~>e:>Error in $markup' );

			return false;

		}

		// h::log('d:>'.$string);

		// get all sections, add markup to $markup->$field ##
		// note, we trim() white space off tags, as this is handled by the regex ##
		$open = trim( tag::g( 'par_o' ) );
		$close = trim( tag::g( 'par_c' ) );
		// $end = trim( tag::g( 'sec_e' ) );
		// $end_preg = str_replace( '/', '\/', ( trim( tag::g( 'sec_e' ) ) ) );
		// $end = '{{\/#}}';

		// h::log( 'open: '.$open. ' - close: '.$close. ' - end: '.$end );

		$regex_find = \apply_filters( 
			'q/render/markup/partial/regex/find', 
			"/$open\s+(.*?)\s+$close/s"  // note:: added "+" for multiple whitespaces.. not sure it's good yet...
			// "/{{#(.*?)\/#}}/s" 
		);

		// h::log( 't:> allow for badly spaced tags around sections... whitespace flexible..' );
		if ( 
			preg_match_all( $regex_find, $string, $matches, PREG_OFFSET_CAPTURE ) 
		){

			// strip all section blocks, we don't need them now ##
			// $regex_remove = \apply_filters( 'q/render/markup/section/regex/remove', "/{{#.*?\/#}}/ms" );
			$regex_remove = \apply_filters( 
				'q/render/markup/partial/regex/remove', 
				"/$open.*?$close/ms" 
				// "/{{#.*?\/#}}/ms"
			);
			self::$markup['template'] = preg_replace( $regex_remove, "", self::$markup['template'] ); 
		
			// preg_match_all( '/%[^%]*%/', $string, $matches, PREG_SET_ORDER );
			// h::log( $matches[1] );

			// sanity ##
			if ( 
				! $matches
				|| ! isset( $matches[1] ) 
				|| ! $matches[1]
			){

				h::log( 'e:>Error in returned matches array' );

				return false;

			}

			foreach( $matches[1] as $match => $value ) {

				// position to add placeholder ##
				if ( 
					! is_array( $value )
					|| ! isset( $value[0] ) 
					|| ! isset( $value[1] ) 
					|| ! isset( $matches[0][$match][1] )
				) {

					h::log( 'e:>Error in returned matches - no position' );

					continue;

				}

				// h::log( 'd:>Searching for section field and markup...' );

				$position = $matches[0][$match][1]; // take from first array ##
				// h::log( 'd:>position: '.$position );
				// h::log( 'd:>position from 1: '.$matches[0][$match][1] ); 

				// get partial data ##
				$partial = method::string_between( $matches[0][$match][0], $open, $close );
				// $markup = method::string_between( $matches[0][$match][0], $close, $end );

				// sanity ##
				if ( 
					! isset( $partial ) 
					|| ! strstr( $partial, '__' )
					// || ! isset( $markup ) 
				){

					h::log( 'e:>Error in returned match function' );

					continue; 

				}

				// clean up ##
				$partial = trim($partial);
				list( $context, $task ) = explode( '__', $partial );
				// $markup = trim($markup);

				// test what we have ##
				h::log( 'd:>context: "'.$context.'"' );
				h::log( 'd:>task: "'.$task.'"' );

				// hash ##
				$hash = bin2hex( random_bytes(16) );

				// @todo -- currently only partials are handled... ##
				switch( $context ) {

					case 'partial' :

						// so, we can add a new field value to $args array based on the field name - with the markup as value
						render\fields::define([
							// $function => render::{$function}()
							$hash => core\config::get([ 'context' => $context, 'task' => $task ])
						]);

					break ;

					default :

						h::log( 'e:>Currently, only partial partials are supported' );

					break ;

				}


				// and now we need to add a placeholder "{{ $field }}" before this block at $position to markup->template ##
				$placeholder = tag::wrap([ 'open' => 'var_o', 'value' => $hash, 'close' => 'var_c' ]);
				placeholder::set( $placeholder, $position, 'variable' ); // '{{ '.$field.' }}'

			}

		}

	}




	/**
	 * Scan for comments in markup and convert to placeholders and $fields and also to error log ##
	 * 
	 * @since 4.1.0
	*/
	public static function comment(){

		// sanity -- this requires ##
		if ( 
			! isset( self::$markup )
			|| ! is_array( self::$markup )
			|| ! isset( self::$markup['template'] )
		){

			h::log( 'e:>Error in stored $markup' );

			return false;

		}

		// get markup ##
		$string = self::$markup['template'];

		// sanity ##
		if (  
			! $string
			|| is_null( $string )
		){

			h::log( self::$args['task'].'~>e:>Error in $markup' );

			return false;

		}

		// h::log('d:>'.$string);

		// get all comments, add markup to $markup->$field ##
		// note, we trim() white space off tags, as this is handled by the regex ##
		$open = trim( tag::g( 'com_o' ) );
		$close = trim( tag::g( 'com_c' ) );

		// h::log( 'open: '.$open. ' - close: '.$close );

		$regex_find = \apply_filters( 
			'q/render/markup/comment/regex/find', 
			"/$open\s+(.*?)\s+$close/s"  // note:: added "+" for multiple whitespaces.. not sure it's good yet...
			// "/{{#(.*?)\/#}}/s" 
		);

		// h::log( 't:> allow for badly spaced tags around sections... whitespace flexible..' );
		if ( 
			preg_match_all( $regex_find, $string, $matches, PREG_OFFSET_CAPTURE ) 
		){

			// strip all section blocks, we don't need them now ##
			// $regex_remove = \apply_filters( 'q/render/markup/section/regex/remove', "/{{#.*?\/#}}/ms" );
			$regex_remove = \apply_filters( 
				'q/render/markup/comment/regex/remove', 
				"/$open.*?$close/ms" 
				// "/{{#.*?\/#}}/ms"
			);
			self::$markup['template'] = preg_replace( $regex_remove, "", self::$markup['template'] ); 
		
			// preg_match_all( '/%[^%]*%/', $string, $matches, PREG_SET_ORDER );
			// h::log( $matches[1] );

			// sanity ##
			if ( 
				! $matches
				|| ! isset( $matches[1] ) 
				|| ! $matches[1]
			){

				h::log( 'e:>Error in returned matches array' );

				return false;

			}

			foreach( $matches[1] as $match => $value ) {

				// position to add placeholder ##
				if ( 
					! is_array( $value )
					|| ! isset( $value[0] ) 
					|| ! isset( $value[1] ) 
					|| ! isset( $matches[0][$match][1] )
				) {

					h::log( 'e:>Error in returned matches - no position' );

					continue;

				}

				// h::log( 'd:>Searching for comments data...' );

				$position = $matches[0][$match][1]; // take from first array ##
				// h::log( 'd:>position: '.$position );
				// h::log( 'd:>position from 1: '.$matches[0][$match][1] ); 
				
				// get a single comment ##
				$comment = method::string_between( $matches[0][$match][0], $open, $close );

				// sanity ##
				if ( 
					! isset( $comment ) 
				){

					h::log( 'e:>Error in returned match function' );

					continue; 

				}

				// clean up ##
				$comment = trim($comment);

				// test what we have ##
				// h::log( 'd:>comment: "'.$comment.'"' );

				// hash ##
				$hash = bin2hex( random_bytes(16) );

				// so, we can add a new field value to $args array based on the field name - with the comment as value
				render\fields::define([
					$hash => '<!-- '.$comment.' -->'
				]);
				
				// also, add a log entry ##
				h::log( 'd:>'.$comment );

				// and now we need to add a placeholder "{{ $field }}" before this comment block at $position to markup->template ##
				$placeholder = tag::wrap([ 'open' => 'var_o', 'value' => $hash, 'close' => 'var_c' ]);
				placeholder::set( $placeholder, $position, 'variable' ); // '{{ '.$field.' }}'

			}

		}

	}



	/**
	 * Scan for config in markup and convert to $fields
	 * 
	 * @since 4.1.0
	*/
	public static function config(){

		// sanity -- this requires ##
		if ( 
			! isset( self::$markup )
			|| ! is_array( self::$markup )
			|| ! isset( self::$markup['template'] )
		){

			h::log( 'e:>Error in stored $markup' );

			return false;

		}

		// h::log( $args['key'] );

		// get markup ##
		$string = self::$markup['template'];

		// sanity ##
		if (  
			! $string
			|| is_null( $string )
			// || ! isset( $args['key'] )
			// || ! isset( $args['value'] )
			// || ! isset( $args['string'] )
		){

			h::log( self::$args['task'].'~>e:>Error in $markup' );

			return false;

		}

		// h::log('d:>'.$string);

		// get all variable placeholders from markup string ##
        if ( 
            ! $placeholders = placeholder::get( $string, 'variable' ) 
        ) {

			// h::log( self::$args['task'].'~>d:>No placeholders found in $markup');
			// h::log( 'd:>No placeholders found in $markup: '.self::$args['task']);

			return false;

		}

		// log ##
		h::log( self::$args['task'].'~>d:>"'.count( $placeholders ) .'" placeholders found in string');
		// h::log( 'd:>"'.count( $placeholders ) .'" placeholders found in string');

		// remove any leftover placeholders in string ##
		foreach( $placeholders as $key => $value ) {

			// h::log( self::$args['task'].'~>d:>'.$value );

			// now, we need to look for the config pattern, defined as field(setting:value;) and try to handle any data found ##
			// $regex_find = \apply_filters( 'q/render/markup/config/regex/find', '/[[(.*?)]]/s' );
			
			// if ( 
			// 	preg_match( $regex_find, $value, $matches ) 
			// ){

			if ( 
				// $config_string = method::string_between( $value, '[[', ']]' )
				$config_string = method::string_between( $value, trim( tag::g( 'arg_o' )), trim( tag::g( 'arg_c' )) )
			){

				// store placeholder ##
				$placeholder = $value;

				// $config_string = json_encode( $config_string );
				// h::log( $config_string );

				// // grab config JSON ##
				// $config_string = '{ "handle":{ "all":"square-sm", "lg":"vertical-lg" }, "string": "value" }';
				$config_object = json_decode( $config_string );
				// $config_object = isset( $config_json[0] ) ? $config_json[0] : false ;

				// h::log( 'd:>config: '.$config_string );
				// h::log( $config_json );
				// h::log( $config_object );

				// sanity ##
				if ( 
					! $config_string
					|| ! is_object( $config_object )
					// || ! isset( $matches[0] ) 
					// || ! $matches[0]
				){

					h::log( self::$args['task'].'~>e:>No config in placeholder: '.$placeholder ); // @todo -- add "loose" lookups, for white space '@s
					// h::log( 'd:>No config in placeholder: '.$placeholder ); // @todo -- add "loose" lookups, for white space '@s''

					continue;

				}

				// h::log( $matches[0] );

				// get field ##
				// h::log( 'value: '.$value );
				
				// $field = trim( method::string_between( $value, '{{ ', '[[' ) );
				$field = str_replace( $config_string, '', $value );

				// clean up field data ##
				$field = preg_replace( "/[^A-Za-z0-9_]/", '', $field );

				// h::log( 'field: '.$field );

				// check if field is sub field i.e: "post__title" ##
				if ( false !== strpos( $field, '__' ) ) {

					$field_array = explode( '__', $field );

					$field_name = $field_array[0]; // take first part ##
					$field_type = $field_array[1]; // take second part ##

				} else {

					$field_name = $field; // take first part ##
					$field_type = $field; // take second part ##

				}

				// we need field_name, so validate ##
				if (
					! $field_name
					|| ! $field_type
				){

					h::log( self::$args['task'].'~>e:>Error extracting $field_name or $field_type from placeholder: '.$placeholder );

					continue;

				}

				// matches[0] contains the whole string matched - for example "(handle:square;)" ##
				// we can use this to work out the new_placeholder value
				// $placeholder = $value;
				// $new_placeholder = explode( '(', $placeholder )[0].' }}';
				// $new_placeholder = '{{ '.$field.' }}';
				$new_placeholder = tag::wrap([ 'open' => 'var_o', 'value' => $field, 'close' => 'var_c' ]);

				// test what we have ##
				// h::log( "d:>placeholder: ".$value );
				// h::log( "d:>new_placeholder: ".$new_placeholder);
				// h::log( "d:>field_name: ".$field_name );
				// h::log( "d:>field_type: ".$field_type );

				foreach( $config_object as $k => $v ) {

					// h::log( "d:>config_setting: ".$k );
					// h::log( "d:>config_value: ".$v );

					h::log( 't:> - add config handlers... based on field type ##');
					// config::handle() ##
					switch ( $field_type ) {

						case "src" :
							
							// assign new $args[FIELDNAME]['src'] with value of config --
							self::$args[$field_name]['config'][$k] = is_object( $v ) ? (array) $v : $v; // note, $v may be an array of values

						break ;

					}

				}

				// h::log( self::$args[$field_name] );

				// now, edit the placeholder, to remove the config ##
				placeholder::edit( $placeholder, $new_placeholder );

			}
		
        }

	}



	public static function string( $args = null ){

		// h::log( $args['key'] );

		// sanity ##
		if (  
			is_null( $args )
			|| ! isset( $args['key'] )
			|| ! isset( $args['value'] )
			|| ! isset( $args['string'] )
		){

			h::log( self::$args['task'].'~>e:>Error in passed args to "string" method' );

			return false;

		}

		// get string ##
		$string = $args['string'];
		$value = $args['value'];
		$key = $args['key'];

		// h::log( 'key: "'.$key.'" - value: "'.$value.'"' );

		// look for wrapper in markup ##
		// if ( isset( self::$args[$key] ) ) {
		// if ( isset( self::$markup[$key] ) ) { // ?? @todo -- how is this working ?? -- surely, this should look for 'wrap'
		// wrap once ..
		// if ( 
		// 	isset( self::$markup['wrap'] ) 
		// 	&& ! self::$wrapped
		// ) { 

		// 	// h::log( 't:>@todo.. string wrap logic...' );

		// 	// $markup = self::$args[ $key ];
		// 	$markup = self::$markup[ 'wrap' ];

		// 	// filter ##
		// 	$string = core\filter::apply([ 
		// 		'parameters'    => [ 'string' => $string ], // pass ( $string ) as single array ##
		// 		'filter'        => 'q/render/markup/wrap/'.self::$args['task'].'/'.$key, // filter handle ##
		// 		'return'        => $string
		// 	]); 

		// 	// h::log( 'found: '.$markup );

		// 	// wrap key value in found markup ##
		// 	// example: markup->wrap = '<h2 class="mt-5">{{ content }}</h2>' ##
		// 	$value = str_replace( 
		// 		// '{{ content }}', 
		// 		tag::wrap([ 'open' => 'var_o', 'value' => 'content', 'close' => 'var_c' ]), 
		// 		$value, 
		// 		$markup 
		// 	);

		// 	// track ##
		// 	self::$wrapped = true;

		// }

		// filter ##
		$string = core\filter::apply([ 
             'parameters'    => [ 'string' => $string ], // pass ( $string ) as single array ##
             'filter'        => 'q/render/markup/string/before/'.self::$args['task'].'/'.$key, // filter handle ##
             'return'        => $string
        ]); 

		// variable replacement -- regex way ##
		$open = trim( tag::g( 'var_o' ) );
		$close = trim( tag::g( 'var_c' ) );

		// h::log( 'open: '.$open );
		// "~\\$open\s+(.*?)\s+\\$close~" // note:: added "+" for multiple whitespaces.. not sure it's good yet...

		// $regex = \apply_filters( 'q/render/markup/string', "~\{{\s+$key\s+\}}~" ); // '~\{{\s(.*?)\s\}}~' 
		$regex = \apply_filters( 'q/render/markup/string', "~\\$open\s+$key\s+\\$close~" ); // '~\{{\s(.*?)\s\}}~' 
		$string = preg_replace( $regex, $value, $string ); 

		// filter ##
		$string = core\filter::apply([ 
             'parameters'    => [ 'string' => $string ], // pass ( $string ) as single array ##
             'filter'        => 'q/render/markup/string/after/'.self::$args['task'].'/'.$key, // filter handle ##
             'return'        => $string
        ]); 

		// return ##
		return $string;

	}



	public static function wrap( $args = null ){

		// h::log( $args['key'] );
		// h::log( 'd:>hello...' );

		// sanity ##
		if (  
			is_null( $args )
			// || ! isset( $args['key'] )
			// || ! isset( $args['value'] )
			|| ! isset( $args['string'] )
		){

			h::log( self::$args['task'].'~>e:>Error in passed args to "wrap" method' );

			return false;

		}

		// h::log( 'd:>hello 2...' );

		// get string ##
		$string = $args['string'];
		// $value = $args['value'];
		// $key = $args['key'];

		// h::log( 'key: "'.$key.'" - value: "'.$value.'"' );

		// look for wrapper in markup ##
		// and wrap once ..
		if ( 
			isset( self::$markup['wrap'] ) 
			// && ! self::$wrapped
		) { 

			// h::log( 'd:>hello 3...' );

			// $markup = self::$args[ $key ];
			$markup = self::$markup[ 'wrap' ];

			// h::log( 'd:>wrap string in: '.$markup );

			// filter ##
			$markup = core\filter::apply([ 
				'parameters'    => [ 'markup' => $markup ], // pass ( $string ) as single array ##
				'filter'        => 'q/render/markup/wrap/'.self::$args['context'].'/'.self::$args['task'], // filter handle ##
				'return'        => $markup
			]); 

			// h::log( 'found: '.$markup );

			// wrap key value in found markup ##
			// example: markup->wrap = '<h2 class="mt-5">{{ content }}</h2>' ##
			$string = str_replace( 
				// '{{ content }}', 
				tag::wrap([ 'open' => 'var_o', 'value' => 'content', 'close' => 'var_c' ]), 
				$string, 
				$markup 
			);

			// track ##
			// self::$wrapped = true;

		}

		// filter ##
		$string = core\filter::apply([ 
             'parameters'    => [ 'string' => $string ], // pass ( $string ) as single array ##
             'filter'        => 'q/render/markup/string/wrap/'.self::$args['context'].'/'.self::$args['task'], // filter handle ##
             'return'        => $string
        ]); 

		// template replacement ##
		// $string = str_replace( '{{ '.$key.' }}', $value, $string );
		// h::log( $string );

		// // regex way ##
		// $regex = \apply_filters( 'q/render/markup/string', "~\{{\s+$key\s+\}}~" ); // '~\{{\s(.*?)\s\}}~' 
		// $string = preg_replace( $regex, $value, $string ); 

		// // filter ##
		// $string = core\filter::apply([ 
        //      'parameters'    => [ 'string' => $string ], // pass ( $string ) as single array ##
        //      'filter'        => 'q/render/markup/string/after/'.self::$args['task'].'/'.$key, // filter handle ##
        //      'return'        => $string
        // ]); 

		// return ##
		return $string;

	}



    /**
     * Update Markup base for passed field ##
     * 
     */
    public static function set( string $field = null, $count = null ){

        // sanity ##
        if ( 
            is_null( $field )
            || is_null( $count )
        ) {

			// log ##
			h::log( self::$args['task'].'~>n:>No field value or count iterator passed to method');

            return false;

        }

        // check ##
        // helper::log( 'Update template markup for field: '.$field.' @ count: '.$count );

        // look for required markup ##
        // if ( ! isset( self::$args[$field] ) ) {
		// h::log( self::$markup );
		if ( ! isset( self::$markup[$field] ) ) {

			// log ##
			h::log( self::$args['task'].'~>n:>Field: "'.$field.'" does not have required markup defined in "$markup->'.$field.'"' );

            // bale if not found ##
            return false;

        }

        // get markup ##
        // $markup = self::$args[$field];

        // get target placeholder ##
		// $placeholder = '{{ '.$field.' }}';
		$placeholder = tag::wrap([ 'open' => 'var_o', 'value' => $field, 'close' => 'var_c' ]);
        if ( 
            ! placeholder::exists( $placeholder )
        ) {

			// log ##
			h::log( self::$args['task'].'~>n:>Placeholder: "'.$placeholder.'" is not in the passed markup template' );

            return false;

        }

        // so, we have the repeater markup to copy, placeholder in template to locate new markup ... 
        // && we need to find all placeholders in markup and append field__X__PLACEHOLDER

        // get all placeholders from markup->$field ##
        if ( 
            ! $placeholders = placeholder::get( self::$markup[$field] ) 
        ) {

			// log ##
			h::log( self::$args['task'].'~>n:>No placeholders found in passed string' );

            return false;

        }

        // test ##
        // helper::log( $placeholders );

        // iterate over {{ placeholders }} adding prefix ##
        $new_placeholders = [];
        foreach( $placeholders as $key => $value ) {

			// h::log( 'Working placeholder: '.$value );
			// h::log( 'variable_open: '.tag::g( 'var_o' ) );

			// var open and close, with and without whitespace ##
			$array_replace = [
				tag::g( 'var_o' ),
				trim( tag::g( 'var_o' ) ),
				tag::g( 'var_c' ),
				trim( tag::g( 'var_c' ) )
			];
			// new placeholder ##
			// h::log( 't:>todo.. make this new field name more reliable' );
			$new = tag::g( 'var_o' ).trim($field).'__'.trim($count).'__'.trim( str_replace( $array_replace, '', trim($value) ) ).tag::g( 'var_c' );

			// single whitespace max ## @might be needed ##
			// $new = preg_replace( '!\s+!', ' ', $new );	

			// h::log( 'new_placeholder: '.$new );

			$new_placeholders[] = $new;

            // $new_placeholders[] = '{{ '.trim($field).'__'.trim($count).'__'.str_replace( [ '{{', '{{ ', '}}', ' }}' ], '', trim($value) ).' }}';

        } 

        // testnew placeholders ##
        // h::log( $new_placeholders );

        // generate new markup from template with new_placeholders ##
        $new_markup = str_replace( $placeholders, $new_placeholders, self::$markup[$field] );

        // helper::log( $new_markup );

        // use strpos to get location of {{ placeholder }} ##
        $position = strpos( self::$markup['template'], $placeholder );
        // helper::log( 'Position: '.$position );

        // add new markup to $template as defined position - don't replace {{ placeholder }} yet... ##
        $new_template = substr_replace( self::$markup['template'], $new_markup, $position, 0 );

        // test ##
        // helper::log( $new_template );

        // push back into main stored markup ##
        self::$markup['template'] = $new_template;

        // kick back ##
        return true;

    }


}
