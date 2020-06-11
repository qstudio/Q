<?php

namespace q\ui;

use q\core;
use q\core\helper as h;

/*

HOW TO.. hack into the self::$args.. fields, etc with data from the_posts ... ???

One rendering Engine for all use cases

Requirements:
- pass standerdized args ( markup, callbacks, filter ets )
- log on global or call specific basis
- manipulate markup and data to return at once 
- option to return pure data or marked-up data ( render/ or get/ ) 
- friendly from templates etc -- render\the_title();
- pass fake data ( fields = [ 'the_title', 'Hello' ] ) - skips data fetching for field?? all??
- __magic function finder _find()

Modules:
- group - render field data from ACF field group
- the_ || wordpress - the_posts, the_title etc.. include getters and render methods
- field ( dael with single use case post_fields )
- type ( src, video, gallery?? etc )

filters:
- validate args ( check requirements, filter.. )
- callbacks ( either via config or args -- defined list, any other render method, wp functions, openhouse.. ?? )
- pre process ( filter config, filter fields )
- format return ( filter args, filter data, apply markup.. filter again.. )
- post-process ( js callbacks, srcset etc.. )

*/

// load it up ##
\q\ui\render::run();

class render extends \Q {

	protected static

        // default args to merge with passed array ##
        $args = [
            'config'            => [
                'run'           => true, // don't run this item ##
                'debug'         => false, // don't debug this item ##
				'return'        => 'echo', // default to echo return string ##
				'srcset' 		=> true // add srcset to src references ##
            ],
            'filter'        => [
                'img'           => 'srcset' // apply srcset handlers to all images ## 
            ]      
        ],

        // frontend pre-processor callbacks to update field values ##
        $callbacks = [
            'get_posts'         => [ // standard WP get_posts()
                'namespace'     => 'global', // global scope to allow for namespacing ##
                'method'        => '\get_posts()',
                'args'          => [] // default - can be edited via global and specific filters ##
            ],
        ],

        // value formatters ##
        $format = [
            // Arrays could be collection of WP Post Objects OR repeater block - so check ##
            'array'             => [
                'type'          => 'is_array',
                'method'        => 'format_array'
            ],
            'post_object'       => [
                'type'          => 'is_object',
                'method'        => 'format_object'
            ],
            'integer'           => [
                'type'          => 'is_int',
                'method'        => 'format_integer'
            ],
            'string'            => [
                'type'          => 'is_string',
                'method'        => 'format_text',
            ],
		],
		

		// allowed field types ##
        $type = [
            'src'             	=> [
                'format'        => [ 'is_string', 'is_int', 'is_array', 'is_object' ],
                'method'        => 'src'
            ],
            // 'post_object'       => [
            //     'type'          => 'is_object',
            //     'method'        => 'format_object'
            // ],
            // 'integer'           => [
            //     'type'          => 'is_int',
            //     'method'        => 'format_integer'
            // ],
            // 'string'            => [
            //     'type'          => 'is_string',
            //     'method'        => 'format_text',
            // ],
        ],

        // standard fields to add to wp_post objects
        $wp_post_fields = [
            'ID',
            'post_title',
            'post_content',
		],
		
		// extended WP_Post fields -- with hand crafted values ##
        $wp_post_fields_custom = [
            'post_excerpt',
            'permalink', 
			'category_name', 
			'human_date', // human readable date ##
			'post_date',
            'category_permalink',
            'src', // requires additional lookup and handle ##
        ],

        $output = null, // return string ##
        $fields = null, // field names and values ##
        $markup = null, // store local version of passed markup ##
        $log = null, // tracking array for feedback ##
        $acf_fields = null // fields grabbed by acf function ##

	;
	
	public static function run(){

		core\load::libraries( self::load() );

	}


    /**
    * Load Libraries
    *
    * @since        2.0.0
    */
    public static function load()
    {

		return $array = [
			// validate and assign args ##
			'args' => h::get( 'ui/render/args.php', 'return', 'path' ),

			// render group() ##
			'group' => h::get( 'ui/render/group.php', 'return', 'path' ),

			// all render methods - connecting to get logic ##
			// most "the_" functions will require some logic
			'method' => h::get( 'ui/render/method.php', 'return', 'path' ),

			// check callbacks on defined fields ## 
			// @todo - allow to be passed from calling method ['callback' => 'srcset' ] etc ##
			'callback' => h::get( 'ui/render/callback.php', 'return', 'path' ),

			// get field data ##
			'get' => h::get( 'ui/render/get.php', 'return', 'path' ), 

			// prepare and manipulate field data ##
			'fields' => h::get( 'ui/render/fields.php', 'return', 'path' ), 

			// check format of each fields data and modify as required to markup ##
			'format' => h::get( 'ui/render/format.php', 'return', 'path' ),

			// defined field types, with modifies to extend base data ##
			'type' => h::get( 'ui/render/type.php', 'return', 'path' ),

			// prepare defined markup, search for and replace placeholders 
			'markup' => h::get( 'ui/render/markup.php', 'return', 'path' ),

			// output string ##
			'output' => h::get( 'ui/render/output.php', 'return', 'path' ),

			// log activity ##
			'log' => h::get( 'ui/render/log.php', 'return', 'path' ),
		];

	}
	
	/** 
	 * bounce to function getter ##
	 * function name can be any of the following patterns:
	 * 
	 * the_group
	 * the_%%%
	 * 
	 * field_FIELDNAME // @todo
	 * type_IMAGE || ARRAY || WP_Object etc // @todo
	 */
	public static function __callStatic( $function, $args ){	

		// take first array item, unwrap array - __callStatic wraps the array in an array ##
		if ( is_array( $args ) && isset( $args[0] ) ) { 
			
			// h::log('Taking the first array item..');
			$args = $args[0];
		
		}

		// test namespace ##
		h::log( __NAMESPACE__ );

		// the__ methods ##
		if (
			// "the_" == substr( $function, 0, 4 ) // $function starts with "the_" ##
			// && 
			\method_exists( __NAMESPACE__.'\render\method', $function ) // && exists ##
			&& \is_callable([ __NAMESPACE__.'\render\method', $function ]) // && exists ##
		) {

			// h::log( 'Found function: "q\ui\render\method::'.$function.'()"' );

			// call it ##
			return render\method::{$function}( $args );

		}

		// log ##
		h::log( 'No matching method found for: '.$function );

		// kick back nada - as this renders on the UI ##
		return false;

	}


}