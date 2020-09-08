<?php

namespace q\module;

use q\core;
use q\core\helper as h;
// use q\core\config as config;
use q\asset;

// load it up ##
\q\module\grunt::__run();

class grunt extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in module select API ##
		\q\module::filter([
			'module'	=> str_replace( __NAMESPACE__.'\\', '', static::class ),
			'name'		=> 'Q ~ Grunt LiveReload',
			'selected'	=> true,
		]);

		// add fields to Q settings ##
		\add_filter( 'q/plugin/acf/add_field_groups/q_option_module', [ get_class(), 'filter_acf_module_conditional' ], 10, 1 );

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('grunt') );
		if ( 
			! isset( core\option::get('module')->grunt )
			|| true !== core\option::get('module')->grunt 
		){

			// h::log( 'd:>Emoji is not enabled.' );

			return false;

		}

		// add direct to html, late ##
		\add_action( 'wp_footer', [ get_class(), 'grunt'] );

    }



	public static function filter_acf_module_conditional( $array ) 
    {

        // test ##
        // h::log( $array );

        // lets add our fields ##
        array_push( $array['fields'], [

			'key' => 'field_q_option_module_grunt_port',
			'label' => 'Localhost Port',
			'name' => 'q_option_module_grunt_port',
			'type' => 'text',
			'instructions' => 'Enter the port number to use.',
			'required' => 1,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_q_option_module',
						'operator' => '==',
						'value' => 'grunt',
					),
				),
			),
			'default_value'	=> '1337'
        
        ]);

        // h::log( $array['fields'] );

        // kick it back, as it's a filter ##
        return $array;

    }


    public static function grunt()
    {

		// h::log( 'e:>GRUNT HIT..' );
		// h::log( core\option::get('module_grunt_port') );

        // only if q_theme is debugging ##
        if ( ! self::$debug ) {

            return false;

		}
		
		// get port, if set ##
		$port = 
			core\option::get('module_grunt_port') ?: 
			1337 ; 

?>
        <script src="//localhost:<?php echo $port; ?>/livereload.js"></script>
<?php

    }

}
