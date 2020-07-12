<?php

// return an array ##
return [ 'navigation' 			=> [

'_global'						=> [
	'config' 					=> [
		'run' 					=> true,
		'debug'					=> false,
	],
],

/*
// siblings ---------
'siblings'  					=> [
	'args' 						=> [
		'post_type'         	=> 'page',
		'add_parent'        	=> false,
		'posts_per_page'    	=> 10, // @todo.. filter posts_per_page... per page ##
	],
	'markup'             		=> [
		'template'				=> '<li class="{{ li_class }}{{ active-class }}">{{ item }}</li>',
		'wrap'             		=> '<div class="row row justify-content-center mt-5 mb-5"><ul class="pagination">{{ content }}</ul></div>'
	],
],

// next_back ---------
'relative'  					=> [
	'args' 						=> [
		'post_type'         	=> 'page',
		'add_parent'        	=> false,
		'posts_per_page'    	=> 10, // @todo.. filter posts_per_page... per page ##
	],
	'markup'             		=> [
		'template'				=> '<li class="{{ li_class }}{{ active-class }}">{{ item }}</li>',
		'wrap'             		=> '<div class="row row justify-content-center mt-5 mb-5"><ul class="pagination">{{ content }}</ul></div>'
	],
],
*/

// menu ---------
'menu'  						=> [
	'args' 						=> [
		'echo'					=> true,
	]
],

// use your pagination ---------
'pagination'  					=> [
	'markup'             		=> [
		'template'				=> '<li class="{{ li_class }}{{ active-class }}">{{ item }}</li>',
		'wrap'             		=> '<div class="row row justify-content-center mt-5 mb-5"><ul class="pagination">{{ content }}</ul></div>'
	],
	'args'						=> [
		'end_size'				=> 0,
		'mid_size'				=> 2,
	],
	'ui'						=> [
		'prev_text'				=> '&lsaquo; '.\__('Previous', 'q-textdomain' ),
		'next_text'				=> \__('Next', 'q-textdomain' ).' &rsaquo;', 
		'first_text'			=> '&laquo; '.\__('First', 'q-textdomain' ),
		'last_text'				=> \__('Last', 'q-textdomain' ).' &raquo',
		'li_class'				=> 'page-item',
		'class_link_item'		=> 'page-link',
		'class_link_first' 		=> 'd-none d-md-inline page-link page-first d-none d-md-block',
		'class_link_last' 		=> 'd-none d-md-inline page-link page-last d-none d-md-block'
	]
]

]];