<?php
 
// return an array ##
return [
'group__frontpage_work' => [
	// 'config'	=> [ 'run' => false ],
	// 'config' => [ 'meta'	=> true ], // add meta data ##
	// 'config' => [ 'srcset'	=> true ], // add srcset data ##
	// [[{"handle":"square-sm"}]]
	// [[{ "handles":{ "all":"square-sm", "lg":"vertical-lg" }, "handle": "square-sm" }]]
	'markup' => '
		<h2 class="row"><div class="col-12 mb-4">{{ frontpage_work_title }}</div></h2>

		<div class="card mb-3">
			<a href="{{ frontpage_work_top__post_permalink }}">
				<img class="fit card-img-top lazy" style="max-height: 200px" src="" 
					data-src="{{ frontpage_work_top__src[[{"handle":"square-sm"}]] }}" />
			</a>
			<div class="card-body">
				<h5 class="card-title">
					<a href="{{ frontpage_work_top__post_permalink }}">
						{{ frontpage_work_top__post_title }}
					</a>
				</h5>
				<p class="card-text">{{ frontpage_work_top__post_excerpt }}</p>
				<span class="badge badge-pill badge-primary ml-1">
					{{ frontpage_work_top__category_name }}
				</span>
			</div>
		</div>

		<ul class="list-group list-group-flush">
			{{# frontpage_work_more }}
			<li class="list-group-item">
				<a href="{{ post_permalink }}">
					{{ post_title }}
				</a>
				<span class="badge badge-pill badge-primary ml-1">
					{{ category_name }}
				</span>
			</li>
			{{/#}}
		</ul>
		
		<div class="row">
			<div class="col-12 list-group list-group-flush border-top">
				<div class="list-group-item">
					Find More
					<a 
						class="badge badge-primary text-white p-2 q-scroll-top" 
						data-toggle="collapse" 
						data-target="#search_content"
						aria-controls="search_content" 
						aria-expanded="false" 
						aria-label="Search navigation"
						href="#">
						Search
					</a>
				</div>
			</div>
		</div>
	'
]
];