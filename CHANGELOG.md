### 6.0.1 ###

* Update: View filter priority moved to Willow - legacy model remains inside Q 

### 6.0.0 ###

* Update: Move to more standard OOP model
* new api q__log() q()

### 5.0.3 ###

* Update: pre-branch for Q 6.0 - prepared and tested against willow 2.0

### 5.0.2 ###

* Update: Change to Grunt build steps to move all SCSS config to child theme

### 5.0.1 ###

* New: View filters to add templates from plugins

### 5.0.0 ###

* New: Universal form validation

### 4.9.8 ###

* New: SEO and asset loading optimizations

### 4.9.7 ###

* FIX: FireFox updates for deferred style loading issue.

### 4.9.6 ###

* NEW: Script and Style hooks to defer loading of assets with filters for skipping handles

### 4.9.5 ###

* Update: Moved to singular log file for simplicity sake
* Update: Willow and Parent / Child theme reorganization
* Update: Gruntfile task runners removed from Q - nothing to build :)

### 4.9.2 ###

* Assets and Task Runners

### 4.9.1 ###

* FIX: Bad namespace in plugin/acf

### 4.9.0 ###

* Update: Removed all assets, js, css to parent theme
* New: tinymce fixes for WP Editor
* New: Purge and reduction of code base

### 4.7.3 ###

* Fix: Added admin checks for ACF filters which were running on profile updates

### 4.7.2 ###

* New: Grunt Task Runners to propagate module assets to theme
* New: Admin file and directory methods
* Update: Total overhaul of the module and extension systems 

### 4.7.1 ###

* Fix: to module/search JS logic event listener, moved to bootstrap shown.bs.collapse

### 4.7.0 ###

* Update: navigation::siblings + navigation::children have Willow controllers
* Update: wp_post head hook update for wp_title
* Added: module/docs for table of contents / anchor control of wiki / docs content

### 4.6.9 ###

* Fixes: Search Extension empty logic

### 4.6.8 ###

* Fixes: get logic for thumbnail media queries

### 4.6.7 ###

* Fixes: to Bootstrap Helper module to include deubbing tool

### 4.6.6 ###

* Update: anspress logic

### 4.6.5 ###

* Update: tweaks to getter logic

### 4.6.4 ###

* Update: changes to log system, seperation from Willow

### 4.6.2 ###

* Staged Version Update

### 4.6.1 ###

* Theme Logic Update

### 4.6.0 ###

* Versioning Release

### 4.5.0 ###

* Major update to all systems, break-up and re-introduction of features
* Assets moved to theme
* Willow render engine forked to it's own repo
* New getters
* Better markup control 

### 3.1.4 ###

* Added Brand Bar admin controls

### 3.1.3 ###

* Versioning

### 3.1.0 ###

* Clearing the slate for this select issue

### 3.0.98 ###

* added delay to q select pageload

### 3.0.95 ###

* Added admin option for Q Brand Bar : Breaking News ticker 

### 3.0.9 ###

* Added admin option to switch SCSS components and controller to theme to include assets

### 3.0.8 ###

* More changes to Gravity Forms filter.


### 3.0.8 ###

* Refinement of auth.net Gravity Forms filter, with additional early test filter.

### 3.0.7 ###

* Added lookup comment for transaction debugging.
* Small markup change to the tab.php controller file to render proper bootstrap panel markup

### 3.0.6 ###

* Addition to auth.net Gravity Forms Controller to pass descirption key in transaction.

### 3.0.5 ###

* Moved ACF tac config out of Q into theme

### 3.0.4 ###

* Added filter to helper for Q Device limitation.

### 3.0.3 ###

* Added auth.net patch to Gravity Forms Controller to pass descirption key in transaction.

### 3.0.2 ###

* Added additional Gravity Forms wrapper methods

### 3.0.1 ###

* Admin menu bar filters

### 3.0.0 ###

* Moved all helper logic into main q_helper
* Added handler for new q_device plugin using Mobile Detect

### 2.9.2 ###

* Fix to global debug tracker, allowing debugging for plugins to be activated from Q Settings

### 2.9.1 ###

* Added enable logic to tab controller via render arg "enable" => "field"

### 2.9.0 ###

* Moved additional shared UI and admin configuration into Q controllers 

### 2.8.9 ###

* Changed cache-busting version control to q_theme::version to allow for easier UI udpdates. 

### 2.8.8 ###

* updated version

### 2.8.7 ###

* Added github controller for Cron timeouts

### 2.8.6 ###

* Updates to Controller set-up
* Addition of generic tab acf field group
* Added default tab markup with filter - q_tab can still accept markup from config also

### 2.8.5 ###

* Updates to Asana class for task creation

### 2.8.2 ###

* Updates to Test controllers for better email and tracking

### 2.8.1 ###

* jQuery fix to resolve scrolling issue on tab controller

### 2.8.0 ###

* Test suite build into Q to allow for automated testing, logging, and alerts to Asana

### 2.7.6 ###

* Filters Gravity Forms Upload path to avoid AWS S3 integration errors - https://docs.gravityforms.com/gform_upload_path/

### 2.7.5 ###

* Added dependency checks for Q, q_theme and ACF to block fatal errors

### 2.7.3 ###

* Q Settings menu item ordering

### 2.7.2 ###

* Q Settings language update

### 2.7.1 ###

* Added external library handler to Q Settings

### 2.7.0 ###

* Added Font Awesome

### 2.6.0 ###

* Moved ACF Q Setting into filters, so that plugins can add and ammed listed items
* Added Test suite to Q, controllable from Q Settings view, with email and error log viewers and controllers 
* Added admin controllers for Brand Bar and Consent System
* Moved debug settings into Test system

### 2.5.2 ###

* handle faq tabs close

### 2.5.1 ###

* fixed snackbar styles

### 2.5.0 ###

* tweak to facebook open graph inclusion

### 2.4.9 ###

* added google::recaptcha_hook method to allow for config to be passed to recpatcha

### 2.4.8 ###

* moved shared libs from q-gh-brand-bar plugin

### 2.4.7 ###

* Added variable definde check to q.global.js to allow for repcatcha overrides on specific templates

### 2.4.6 ###

* Moved q.theme.css and q.theme.js files back to q_theme plugin and removed form git tracking, as these are compilled files, not editable.

### 2.4.5 ###

* removed automatic call to YouTube CSS - can be added manually via q\plugin\youtube:css() or \add_action( 'wp_head', [ 'q\plugin\youtube', 'wp_head' ], 5 );

### 2.4.3 ###

* Added ie/css to theme enqueuer

### 2.4.2 ###

* Moved all css / js enqueuing to Q from Q Theme - custom libraries can be added via filters and methods in Q Theme

### 2.4.1 ###

* Added fallback template hierarchy for all libraries added via Q settings to check in Q Theme, then Q - with debugging setting to load non-minified versions, if found

### 2.4.0 ###

* Added password protected check to tab and render method to wordpress

### 2.3.9 ###

* Moved Q GLobal JS and CSS to optional include, removed old libraries

### 2.3.8 ###

* Updates from Exchange integration
* Q global js / css can be included from q_theme with fallback check to Q
* deprecated webmasters function in hook/wp_head

### 2.3.7 ###

* Comment clean up

### 2.3.5 ###

* Google class reference fix

### 2.3.5 ###

* Updated and tidied class inclusion, tested Club & International

### 2.3.0 ###

* Moved options to ACF API

### 2.2.3 ###

* Testing sub modules with version bump

### 2.2.2 ###

* Facebook and Twitter script consent checks

### 2.2.1 ###

* Facebook and Twitter Open Graph meta tags added

### 2.2.0 ###

* preparation for sub module usage

### 1.3.3 ###

* removed filter in Q_Hook_WP_Head.class.php to hide version number on assets - this allows for easier cache busting.
* renamed language file to q.pot in line with WP plugin conventions.

### 1.3.2 ###

* various fixes to assets locations
* added "page-slug" to body_classes
* removed transient caching on q_get_option - as WP already caches get_option calls and this allows for live device switching
* Added facebook share widget

### 1.3.1 ###

* 3.8.1 Testing
* Forum link

### 1.3.0 ###

* Initial working version

## Upgrade Notice ##

### 1.3.0 ###

* Initial working version
