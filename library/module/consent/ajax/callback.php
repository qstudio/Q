<?php

// namespace ##
namespace q\module\consent;

// Q ##
use q\core;
use q\core\helper as h;
use q\module;

/**
 * AJAX callbacks
 *
 * @package   q\consent
 */

// load it up ##
\q\module\consent\callback::run();

class callback extends module\consent {

	/**
     * Construct
     *
     * @since       0.2
     * @return      void
     */
    public static function run()
    {

    	// delete cookie ##
        \add_action( 'wp_ajax_consent_reset', [ get_class(), 'reset' ] ); // ajax for logged in users
        \add_action( 'wp_ajax_nopriv_consent_reset', [ get_class(), 'reset' ] ); // ajax for not logged in users

        // set cookie ##
        \add_action( 'wp_ajax_consent_set', [ get_class(), 'set' ] ); // ajax for logged in users
        \add_action( 'wp_ajax_nopriv_consent_set', [ get_class(), 'set' ] ); // ajax for not logged in users

    }



    /**
     * Delete stored cookie
     *
     * @since       0.1
     * @return      Boolean
     */
    public static function reset()
    {

		// check nonce ##
        if ( ! \wp_verify_nonce( $_POST['nonce'], 'q_module_nonce' ) ) {

        	h::log( 'e:>AJAX referer failed...' );

			$return = [
				'status'    => '400',
				'message'   => 'Problem saving Consent preferences, please try again.'    
			];

        } else {

			// Check if a cookie has been set##
			if ( 
				cookie::get()
			) {

				// log ##
				// h::log( 'Cookie found and emptied.' );

				unset( $_COOKIE[self::$slug] );
				setcookie( self::$slug, null, -1, '/' );

				$return = [
					'status'    => true,
					'message'   => 'Stored Consent preferences reset to default.'    
				];

			} else {

				// log ##
				// h::log( 'No cookie found, so no action taken...' );

				$return = [
					'status'    => false,
					'message'   => 'No stored Consent settings found.'    
				];

			}

		}

        // set headers ##
        header( "Content-type: application/json" );

        // return it ##
        echo json_encode( $return );

        // all AJAX calls must die!! ##
        die();

    }



    /**
     * Save $_POSTed data to user cookie
     *
     * @since       0.1
     * @return      Boolean
     */
    public static function set()
    {

        // h::log( 'e:>We are setting the Consent...' );
        // h::log( $_POST );

        // try to set cookie ##
        $set_cookie = true;

        // check nonce ##
        if ( ! \wp_verify_nonce( $_POST['nonce'], 'q_module_nonce' ) ) {

        	h::log( 'e:>AJAX referer failed...' );

			$return = [
				'status'    => '400',
				'message'   => 'Problem saving Consent preferences, please try again.'    
			];

			// flag ##
        	$set_cookie = false;

        }

        // sanity ##
        if ( 
            ! isset( $_POST['q_consent_marketing'] ) 
            || ! isset( $_POST['q_consent_analytics'] )
            // || ! is_array( $_POST['q_consent'] )    
        ) {

            h::log( 'd:>Error in data passed to AJAX' );

            // return 0 ##
            $return = [
                'status'    => '400',
                'message'   => 'Problem saving Consent preferences, please try again.'    
            ];

            // flag ##
            $set_cookie = false;

        }

        // continue ##
        if ( $set_cookie ) {

            // h::log( $_POST );

            // format array... ##
            $array = [];
            
            // marketing ##
            $array['marketing'] = $_POST['q_consent_marketing'] ? 1 : 0 ;

            // analytics ##
            $array['analytics'] = $_POST['q_consent_analytics'] ? 1 : 0 ;

            // add active consent to array as this has come from an user action ##
            $array['consent'] = 1;

            // check ##
            // h::log( $array );

            // check for stored cookie -if found, update ##
            if ( cookie::set( $array ) ) {

                // log ##
                // h::log( 'AJAX saved cookie data' );

                // positive outcome ##
                $return = [
                    'status'    => '200',
                    'message'   => 'Consent preferences saved, thank you.'    
                ];

            } else {

                // log ##
                // h::log( 'AJAX failed to save cookie data' );

                // negative outcome ##
                $return = [
                    'status'    => '400',
                    'message'   => 'Problem saving Consent preferences, please try again.'    
                ];

            }

        }

        // set headers ##
        header("Content-type: application/json");

        // return it ##
        echo json_encode( $return );

        // all AJAX calls must die!! ##
        die();

    }


}