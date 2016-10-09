<?php

/**
  ReduxFramework Sample Config File
  For full documentation, please visit: https://docs.reduxframework.com
 * */

if (!class_exists('admin_folder_Redux_Framework_config')) {

    class admin_folder_Redux_Framework_config {

        public $args        = array();
        public $sections    = array();
        public $theme;
        public $ReduxFramework;

        public function __construct() {

            if (!class_exists('ReduxFramework')) {
                return;
            }

            // This is needed. Bah WordPress bugs.  ;)
            if ( true == Redux_Helpers::isTheme( __FILE__ ) ) {
                $this->initSettings();
            } else {
                add_action('plugins_loaded', array($this, 'initSettings'), 10);
            }

        }

        public function initSettings() {

            // Just for demo purposes. Not needed per say.
            $this->theme = wp_get_theme();

            // Set the default arguments
            $this->setArguments();

            // Set a few help tabs so you can see how it's done
            $this->setHelpTabs();

            // Create the sections and fields
            $this->setSections();

            if (!isset($this->args['opt_name'])) { // No errors please
                return;
            }

            // If Redux is running as a plugin, this will remove the demo notice and links
            add_action( 'redux/loaded', array( $this, 'remove_demo' ) );
            
            // Function to test the compiler hook and demo CSS output.
            // Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.
            //add_filter('redux/options/'.$this->args['opt_name'].'/compiler', array( $this, 'compiler_action' ), 10, 2);
            
            // Change the arguments after they've been declared, but before the panel is created
            //add_filter('redux/options/'.$this->args['opt_name'].'/args', array( $this, 'change_arguments' ) );
            
            // Change the default value of a field after it's been set, but before it's been useds
            //add_filter('redux/options/'.$this->args['opt_name'].'/defaults', array( $this,'change_defaults' ) );
            
            // Dynamically add a section. Can be also used to modify sections/fields
            //add_filter('redux/options/' . $this->args['opt_name'] . '/sections', array($this, 'dynamic_section'));

            $this->ReduxFramework = new ReduxFramework($this->sections, $this->args);
        }

        /**

          This is a test function that will let you see when the compiler hook occurs.
          It only runs if a field	set with compiler=>true is changed.

         * */
        function compiler_action($options, $css) {
            //echo '<h1>The compiler hook has run!';
            //print_r($options); //Option values
            //print_r($css); // Compiler selector CSS values  compiler => array( CSS SELECTORS )

            /*
              // Demo of how to use the dynamic CSS and write your own static CSS file
              $filename = dirname(__FILE__) . '/style' . '.css';
              global $wp_filesystem;
              if( empty( $wp_filesystem ) ) {
                require_once( ABSPATH .'/wp-admin/includes/file.php' );
              WP_Filesystem();
              }

              if( $wp_filesystem ) {
                $wp_filesystem->put_contents(
                    $filename,
                    $css,
                    FS_CHMOD_FILE // predefined mode settings for WP files
                );
              }
             */
        }

        /**

          Custom function for filtering the sections array. Good for child themes to override or add to the sections.
          Simply include this function in the child themes functions.php file.

          NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
          so you must use get_template_directory_uri() if you want to use any of the built in icons

         * */
        function dynamic_section($sections) {
            //$sections = array();
            $sections[] = array(
                'title' => __('Section via hook', 'redux-framework-demo'),
                'desc' => __('<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'redux-framework-demo'),
                'icon' => 'el-icon-paper-clip',
                // Leave this as a blank section, no options just some intro text set above.
                'fields' => array()
            );

            return $sections;
        }

        /**

          Filter hook for filtering the args. Good for child themes to override or add to the args array. Can also be used in other functions.

         * */
        function change_arguments($args) {
            //$args['dev_mode'] = true;

            return $args;
        }

        /**

          Filter hook for filtering the default value of any given field. Very useful in development mode.

         * */
        function change_defaults($defaults) {
            $defaults['str_replace'] = 'Testing filter hook!';

            return $defaults;
        }

        // Remove the demo link and the notice of integrated demo from the redux-framework plugin
        function remove_demo() {

            // Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
            if (class_exists('ReduxFrameworkPlugin')) {
                remove_filter('plugin_row_meta', array(ReduxFrameworkPlugin::instance(), 'plugin_metalinks'), null, 2);

                // Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
                remove_action('admin_notices', array(ReduxFrameworkPlugin::instance(), 'admin_notices'));
            }
        }

        public function setSections() {

            /**
              Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
             * */
            // Background Patterns Reader
            $sample_patterns_path   = ReduxFramework::$_dir . '../sample/patterns/';
            $sample_patterns_url    = ReduxFramework::$_url . '../sample/patterns/';
            $sample_patterns        = array();

            if (is_dir($sample_patterns_path)) :

                if ($sample_patterns_dir = opendir($sample_patterns_path)) :
                    $sample_patterns = array();

                    while (( $sample_patterns_file = readdir($sample_patterns_dir) ) !== false) {

                        if (stristr($sample_patterns_file, '.png') !== false || stristr($sample_patterns_file, '.jpg') !== false) {
                            $name = explode('.', $sample_patterns_file);
                            $name = str_replace('.' . end($name), '', $sample_patterns_file);
                            $sample_patterns[]  = array('alt' => $name, 'img' => $sample_patterns_url . $sample_patterns_file);
                        }
                    }
                endif;
            endif;

            ob_start();

            $ct             = wp_get_theme();
            $this->theme    = $ct;
            $item_name      = $this->theme->get('Name');
            $tags           = $this->theme->Tags;
            $screenshot     = $this->theme->get_screenshot();
            $class          = $screenshot ? 'has-screenshot' : '';

            $customize_title = sprintf(__('Customize &#8220;%s&#8221;', 'redux-framework-demo'), $this->theme->display('Name'));
            
            ?>
            <div id="current-theme" class="<?php echo esc_attr($class); ?>">
            <?php if ($screenshot) : ?>
                <?php if (current_user_can('edit_theme_options')) : ?>
                        <a href="<?php echo wp_customize_url(); ?>" class="load-customize hide-if-no-customize" title="<?php echo esc_attr($customize_title); ?>">
                            <img src="<?php echo esc_url($screenshot); ?>" alt="<?php esc_attr_e('Current theme preview'); ?>" />
                        </a>
                <?php endif; ?>
                    <img class="hide-if-customize" src="<?php echo esc_url($screenshot); ?>" alt="<?php esc_attr_e('Current theme preview'); ?>" />
                <?php endif; ?>

                <h4><?php echo $this->theme->display('Name'); ?></h4>

                <div>
                    <ul class="theme-info">
                        <li><?php printf(__('By %s', 'redux-framework-demo'), $this->theme->display('Author')); ?></li>
                        <li><?php printf(__('Version %s', 'redux-framework-demo'), $this->theme->display('Version')); ?></li>
                        <li><?php echo '<strong>' . __('Tags', 'redux-framework-demo') . ':</strong> '; ?><?php printf($this->theme->display('Tags')); ?></li>
                    </ul>
                    <p class="theme-description"><?php echo $this->theme->display('Description'); ?></p>
            <?php
            if ($this->theme->parent()) {
                printf(' <p class="howto">' . __('This <a href="%1$s">child theme</a> requires its parent theme, %2$s.') . '</p>', __('http://codex.wordpress.org/Child_Themes', 'redux-framework-demo'), $this->theme->parent()->display('Name'));
            }
            ?>

                </div>
            </div>

            <?php
            $item_info = ob_get_contents();

            ob_end_clean();

            $sampleHTML = '';
            if (file_exists(dirname(__FILE__) . '/info-html.html')) {
                /** @global WP_Filesystem_Direct $wp_filesystem  */
                global $wp_filesystem;
                if (empty($wp_filesystem)) {
                    require_once(ABSPATH . '/wp-admin/includes/file.php');
                    WP_Filesystem();
                }
                $sampleHTML = $wp_filesystem->get_contents(dirname(__FILE__) . '/info-html.html');
            }

            // ACTUAL DECLARATION OF SECTIONS
            $this->sections[] = array(
                'title'     => __('BAR Builder', 'redux-framework-demo'),
                'desc'      => __('Here you can define the basic settings for your BAR.', 'redux-framework-demo'),
                'icon'      => 'el-icon-home',
                // 'submenu' => false, // Setting submenu to false on a given section will hide it from the WordPress sidebar menu!
                'fields'    => array(
					
					array(
                        'id'        => 'mab-main',
                        'type'      => 'switch',
                        'title'     => __('Activate BAR', 'redux-framework-demo'),
                        //'subtitle'  => __('Look, it\'s activated!', 'redux-framework-demo'),
                        'default'   => true,
                    ),
 					
					array(
                        'id'        => 'mab-text',
                        'type'      => 'text',
                        'title'     => __('BAR message to visitors - HTML allowed', 'redux-framework-demo'),
                        'subtitle'  => __('Default message', 'redux-framework-demo'),
                        'desc'      => __('Type the message you want to show on the BAR.', 'redux-framework-demo'),
                        'validate'  => 'html', //see http://codex.wordpress.org/Function_Reference/wp_kses_post
                        'default'   => 'Welcome to our website!'
                    ),
					
					array(
                       'id'        => 'mab-linktext',
                       'type'      => 'text',
                       'title'     => __('Link text to visitors', 'redux-framework-demo'),
                       'subtitle'  => __('Default link text', 'redux-framework-demo'),
                       'desc'      => __('(Optional) Type the desired call to action, like "subscribe now!". It will appear on your BAR right after the message you typed above. No HTML tags here.', 'redux-framework-demo'),
                       'validate'  => 'no_html', //see http://codex.wordpress.org/Function_Reference/wp_kses_post
                       'default'   => 'Subscribe now!'
                   ),

					array(
                       'id'        => 'mab-urllinktext',
                       'type'      => 'text',
                       'title'     => __('URL link text to visitors', 'redux-framework-demo'),
                       'subtitle'  => __('URL default link text', 'redux-framework-demo'),
                       'desc'      => __('Type the desired URL for the text link you typed above.', 'redux-framework-demo'),
                       'validate'  => 'url', //see http://codex.wordpress.org/Function_Reference/wp_kses_post
                       'default'   => 'http://www.example.com/subscribe'
                   ),

					array(
                        'id'        => 'mab-checkalttext',
                        'type'      => 'switch',
                        'title'     => __('Alternative Text', 'redux-framework-demo'),
                        'subtitle'  => __('Turn on if you want to show a different message at the BAR to your logged users. New fields will appear below.', 'redux-framework-demo'),
//                        'desc'      => __('This is the description field, again good for additional info.', 'redux-framework-demo'),
                        'default'   => false
                    ),

					array(
                        'id'        => 'mab-alttext',
                        'type'      => 'text',
                        'title'     => __('BAR message to logged users - HTML allowed', 'redux-framework-demo'),
                        'subtitle'  => __('Message to logged users', 'redux-framework-demo'),
                        'desc'      => __('Type the message you want your logged users to see. Leave blank if you do not want to display a different message for logged users (default will apply).', 'redux-framework-demo'),
						'required'   => array ('mab-checkalttext','equals','1'),
                        'validate'  => 'html' 
                    ),

					array(
                       'id'        => 'mab-linkalttext',
                       'type'      => 'text',
                       'title'     => __('Link text to logged users', 'redux-framework-demo'),
                       'subtitle'  => __('Link text', 'redux-framework-demo'),
                       'desc'      => __('(Optional) Type the desired "call to action" for your logged users, like "buy now!". It will appear on your BAR right after the message you typed above. No HTML tags here.', 'redux-framework-demo'),
						'required'   => array ('mab-checkalttext','equals','1'),
                       'validate'  => 'no_html', 
                      // 'default'   => 'Subscribe now!'
                   ),

					array(
                      'id'        => 'mab-urllinkalttext',
                      'type'      => 'text',
                      'title'     => __('URL link text - logged users', 'redux-framework-demo'),
                      'subtitle'  => __('URL link text', 'redux-framework-demo'),
                      'desc'      => __('Type the desired URL for the text link you typed above, like http://www.example.com/buy', 'redux-framework-demo'),
					   'required'   => array ('mab-checkalttext','equals','1'),
                      'validate'  => 'url', 
                  ),

                    array(
                        'id'            => 'mab-typography',
                        'type'          => 'typography',
                        'title'         => __('Typography', 'redux-framework-demo'),
                      //  'compiler'      => true,  // Use if you want to hook in your own CSS compiler
                        'google'        => true,    // Disable google fonts. Won't work if you haven't defined your google api key
                       'font-backup'   => true,    // Select a backup non-google font in addition to a google font
						'google_api_key' => '35be917f8a17a6cad61094f49b8b5b77154b61db',
                        'font-style'    => true, // Includes font-style and weight. Can use font-style or font-weight to declare
                        'subsets'       => true, // Only appears if google is true and subsets not set to false
                        'font-size'     => true,
                        'line-height'   => false,
                       // 'word-spacing'  => true,  // Defaults to false
                       // 'letter-spacing'=> true,  // Defaults to false
                        'color'         => true,
                        'preview'       => true, // Disable the previewer
                        'all_styles'    => false,    // Enable all Google Font style/weight variations to be added to the page
                        'output'        => array('body .masterbar'), // An array of CSS selectors to apply this font style to dynamically
                        //'compiler'      => array('h2.site-description-compiler'), // An array of CSS selectors to apply this font style to dynamically
                        'units'         => 'px', // Defaults to px
//                        'subtitle'      => __('Typography option with each property can be called individually.', 'redux-framework-demo'),
                        'default'       => array(
                            'color'         => '#ffffff',
                            'font-style'    => '400',
                            'font-family'   => 'Abel',
							 'font-backup'  => 'Arial, Helvetica, sans-serif',
							 'text-align'    => 'center',
                            'google'        => true,
                            'font-size'     => '28px'),
                           // 'line-height'   => '40px'),
                        'preview' => array('text' => 'Testing the font.'),
                    ),

					array(
					    'id'       => 'mab-link',
					    'type'     => 'link_color',
					    'title'    => __('Link Color Option', 'redux-framework-demo'),
					    'subtitle' => __('Style your links', 'redux-framework-demo'),
					    //'desc'     => __('This is the description field, again good for additional info.', 'redux-framework-demo'),
						'output'    => array('body .masterbar .bar-link'),
					    'default'  => array(
					        'regular'  => '#eeee22', // 
					        'hover'    => '#4b5ebc', // 
					        'active'   => '#dbdbdb',  // 
					        'visited'  => '#dbdbdb'  // 
					  
						)
					),
					
					array(
					         'id'        => 'mab-linkwindow',
					         'type'      => 'checkbox',
					         'title'     => __('Open link in a new window', 'redux-framework-demo'),
					         'subtitle'  => __('If it\'s checked, users will see the link content in a new window.', 'redux-framework-demo'),
					//       'desc'      => __('This is the description field, again good for additional info.', 'redux-framework-demo'),
					         'default'   => '0'// 1 = on | 0 = off
					                    ),


					array(
                        'id'        => 'mab-shadow',
                        'type'      => 'checkbox',
                        'title'     => __('Text Shadow', 'redux-framework-demo'),
                        'subtitle'  => __('If it\'s checked, users will see BAR text with shadow.', 'redux-framework-demo'),
//                        'desc'      => __('This is the description field, again good for additional info.', 'redux-framework-demo'),
                        'default'   => '1'// 1 = on | 0 = off
                    ),
					 array(
                        'id'        => 'mab-backcolor',
                        'type'      => 'color',
                        'title'     => __('Background Color', 'redux-framework-demo'),
                        'subtitle'  => __('Pick a background color for the BAR (default: #dd9933).', 'redux-framework-demo'),
					    'output'        => array('background-color' => 'body .masterbar'),
                        'default'   => '#ff4444',
                        'validate'  => 'color',
                    ),
 					array(
                        'id'            => 'mab-padd',
                        'type'          => 'spacing',
                        'output'        => array('body .masterbar'), // An array of CSS selectors to apply this font style to
                        'mode'          => 'padding',    // absolute, padding, margin, defaults to padding
                        'all'           => true,        // Have one field that applies to all
						 
                        //'top'           => false,     // Disable the top
                        //'right'         => false,     // Disable the right
                        //'bottom'        => false,     // Disable the bottom
                        //'left'          => false,     // Disable the left
                        'units'         => 'px',      // You can specify a unit value. Possible: px, em, %
                        'units_extended'=> 'false',    // Allow users to select any type of unit
                  //      'display_units' => 'false',   // Set to false to hide the units if the units are specified
                        'title'         => __('Padding', 'redux-framework-demo'),
                        'subtitle'      => __('Choose your basic padding setting or leave blank for default (3px).', 'redux-framework-demo'),
					
//                        'desc'          => __('You can enable or disable any piece of this field. Top, Right, Bottom, Left, or Units.', 'redux-framework-demo'),
                        'default'       => array(
                            'padding-top'    => '3px', 
                            'padding-right'  => '3px', 
                            'padding-bottom' => '3px', 
                            'padding-left'   => '3px'
                        )
                    ),
					array(
                        'id'        => 'mab-border',
                        'type'      => 'border',
                        'title'     => __('Border Option', 'redux-framework-demo'),
                        'subtitle'  => __('Define your border-bottom style. Default is 3px, solid and blue.', 'redux-framework-demo'),
                        'output'    => array('body .masterbar'), // An array of CSS selectors to apply this font style to
                       'desc'      => __('Set \'None\' instead of \'Solid\', if you don\'t want a border.', 'redux-framework-demo'),
//						'validate' => 'color',
						'color'    => true,
						'style'    => true,
						'all'    => false,
						'top'    => false, 
                        'right'  => false, 
                        'bottom' => true, 
                        'left'   => false,
                        'default'   => array(
                            'border-color'  => '#1e73be', 
                            'border-style'  => 'solid', 
                           // 'border-top'    => '3px', 
					        // 'border-right'  => '3px', 
        					 'border-bottom' => '3px', 
        					//'border-left'   => '3px'
                        )
                    ),
					array(
                        'id'        => 'mab-css',
                        'type'      => 'textarea',
                        'title'     => __('Custom CSS', 'redux-framework-demo'),
                        'subtitle'  => __('Quickly add some CSS to your BAR by adding it to this block.', 'redux-framework-demo'),
//                        'desc'      => __('This field is even CSS validated!', 'redux-framework-demo'),
                        'validate'  => 'css',
                    ),
					array(
                        'id'        => 'mab-hide',
                        'type'      => 'checkbox',
                        'title'     => __('Let users hide the BAR', 'redux-framework-demo'),
                        'subtitle'  => __('If it\'s checked, users will be able to click to hide the BAR.', 'redux-framework-demo'),
//                        'desc'      => __('This is the description field, again good for additional info.', 'redux-framework-demo'),
                        'default'   => '1'// 1 = on | 0 = off
                    ),
                ),
            );

             
            
            $this->sections[] = array(
                'icon'      => 'el-icon-move',
                'title'     => __('Select Places', 'redux-framework-demo'),
                'desc'      => __('<p class="description">Please, indicate where do you want the BAR to show</p>', 'redux-framework-demo'),
                'fields'    => array(
					
					array(
						'id'        => 'mab-all',
					    'type'      => 'switch',
						//'required'   => array ('mab-home','not','1'),
					    'title'     => __('BAR all over my website', 'redux-framework-demo'),
					    'subtitle'  => __('If it\'s "On", BAR will be displayed all over your website.', 'redux-framework-demo'),
					    'desc'      => __('"On" is the default behavior. Change to "Off" to see other options.', 'redux-framework-demo'),
					     'default'   => true
					                    ),

//					array(
//					                        'id'        => 'mab-checkalttext',
//					                        'type'      => 'switch',
//					                        'title'     => __('Alternative Text', 'redux-framework-demo'),
//					                        'subtitle'  => __('Turn on if you want to show a different message at the BAR to your logged users. New fields will appear below.', 'redux-framework-demo'),
//					//                        'desc'      => __('This is the description field, again good for additional info.', 'redux-framework-demo'),
//					                        'default'   => false
//					                    ),

					array(
                        'id'        => 'mab-home',
                        'type'      => 'switch',
						'required'   => array ('mab-all','=', false),
                        'title'     => __('BAR only at homepage', 'redux-framework-demo'),
                        'subtitle'  => __('If it\'s "On", BAR will be displayed only at your homepage.', 'redux-framework-demo'),
                        'desc'      => __('If you set "On", the last option will become irrelevant. In this case, if you still want to display the BAR on other places beside the homepage, simply use the [masterbar] shortcode on a per post basis.', 'redux-framework-demo'),
                        'default'   => false
                    ),

                    array(
                        'id'        => 'mab-post',
                        'type'      => 'select',
                        'data'      => 'post_type',
                        'multi'     => true,
						'required'   => array(array('mab-home','=',false),array('mab-all','=',false)),
                        'title'     => __('Post Type Multi Select', 'redux-framework-demo'),
                        'subtitle'  => __('Indicate post types in which BAR will be displayed.', 'redux-framework-demo'),
                        'desc'      => __('It will only work if "BAR only at homepage" is not checked. If you want to display the BAR also at specific places that not match the post type you\'ve selected here, simply use the [masterbar] shortcode on a per post basis.', 'redux-framework-demo'),
                    ),
					
                   

                )
            );

					  $this->sections[] = array(
                		'icon'      => 'el-icon-plus',
                		'title'     => __('Shortcode', 'redux-framework-demo'),
                		'desc'      => '<p>' . __('You can use a [masterbar] shortcode on the page, post or CPT in which you want the BAR to appear.','redux-framework-demo') . '</p><p class="description">' . __('Be careful. If you use this shortcode on a post that is already set to display the BAR, the result will be 2 BARS on top of your post. Not beautiful.','redux-framework-demo') . '<p>'

			);

            $theme_info  = '<div class="redux-framework-section-desc">';
            $theme_info .= '<p class="redux-framework-theme-data description theme-uri">' . __('<strong>Theme URL:</strong> ', 'redux-framework-demo') . '<a href="' . $this->theme->get('ThemeURI') . '" target="_blank">' . $this->theme->get('ThemeURI') . '</a></p>';
            $theme_info .= '<p class="redux-framework-theme-data description theme-author">' . __('<strong>Author:</strong> ', 'redux-framework-demo') . $this->theme->get('Author') . '</p>';
            $theme_info .= '<p class="redux-framework-theme-data description theme-version">' . __('<strong>Version:</strong> ', 'redux-framework-demo') . $this->theme->get('Version') . '</p>';
            $theme_info .= '<p class="redux-framework-theme-data description theme-description">' . $this->theme->get('Description') . '</p>';
            $tabs = $this->theme->get('Tags');
            if (!empty($tabs)) {
                $theme_info .= '<p class="redux-framework-theme-data description theme-tags">' . __('<strong>Tags:</strong> ', 'redux-framework-demo') . implode(', ', $tabs) . '</p>';
            }
            $theme_info .= '</div>';

            if (file_exists(dirname(__FILE__) . '/../README.md')) {
                $this->sections['theme_docs'] = array(
                    'icon'      => 'el-icon-list-alt',
                    'title'     => __('Documentation', 'redux-framework-demo'),
                    'fields'    => array(
                        array(
                            'id'        => '17',
                            'type'      => 'raw',
                            'markdown'  => true,
                            'content'   => file_get_contents(dirname(__FILE__) . '/../README.md')
                        ),
                    ),
                );
            }
            
            // You can append a new section at any time.
           

            if (file_exists(trailingslashit(dirname(__FILE__)) . 'README.html')) {
                $tabs['docs'] = array(
                    'icon'      => 'el-icon-book',
                    'title'     => __('Documentation', 'redux-framework-demo'),
                    'content'   => nl2br(file_get_contents(trailingslashit(dirname(__FILE__)) . 'README.html'))
                );
            }
        }

        public function setHelpTabs() {

            // Custom page help tabs, displayed using the help API. Tabs are shown in order of definition.
            $this->args['help_tabs'][] = array(
                'id'        => 'redux-help-tab-1',
                'title'     => __('Theme Information 1', 'redux-framework-demo'),
                'content'   => __('<p>This is the tab content, HTML is allowed.</p>', 'redux-framework-demo')
            );

            $this->args['help_tabs'][] = array(
                'id'        => 'redux-help-tab-2',
                'title'     => __('Theme Information 2', 'redux-framework-demo'),
                'content'   => __('<p>This is the tab content, HTML is allowed.</p>', 'redux-framework-demo')
            );

            // Set the help sidebar
            $this->args['help_sidebar'] = __('<p>This is the sidebar content, HTML is allowed.</p>', 'redux-framework-demo');
        }

        /**

          All the possible arguments for Redux.
          For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments

         * */
        public function setArguments() {

            $theme = wp_get_theme(); // For use with some settings. Not necessary.

            $this->args = array(
                'opt_name' => 'masterbar_settings',
                'display_name' => 'MasterBar Settings',
                'display_version' => '1.0',
                'page_slug' => 'masterbar_options',
                'page_title' => 'MasterBar Options',
                'update_notice' => '1',
                'intro_text' => '<p>Here is the place to define your MasterBar settings.</p>',
                'footer_text' => '<p>If you liked MasterBar, please give it a five stars rating on <a href="http://wordpress.org/plugins/masterbar/" target="_blank">wordpress.org</a>.</p>',
                'admin_bar' => '1',
                'menu_type' => 'menu',
                'menu_title' => 'MasterBar',
                'page_parent_post_type' => 'your_post_type',
                'page_priority' => '100',
                'default_show' => '1',
                'hints' => 
                array(
                  'icon' => 'el-icon-question-sign',
                  'icon_position' => 'right',
                  'icon_size' => 'normal',
                  'tip_style' => 
                  array(
                    'color' => 'light',
                  ),
                  'tip_position' => 
                  array(
                    'my' => 'top left',
                    'at' => 'bottom right',
                  ),
                  'tip_effect' => 
                  array(
                    'show' => 
                    array(
                      'duration' => '500',
                      'event' => 'mouseover',
                    ),
                    'hide' => 
                    array(
                      'duration' => '500',
                      'event' => 'mouseleave unfocus',
                    ),
                  ),
                ),
                'output' => '1',
                'output_tag' => '1',
                'compiler' => '1',
                'page_icon' => 'icon-themes',
                'page_permissions' => 'manage_options',
                'save_defaults' => '1',
                'show_import_export' => '1',
                'transient_time' => '3600',
                'network_sites' => '1',
              );

            // SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
//            $this->args['share_icons'][] = array(
//                'url'   => 'https://github.com/ReduxFramework/ReduxFramework',
//                'title' => 'Visit us on GitHub',
//                'icon'  => 'el-icon-github'
//                //'img'   => '', // You can use icon OR img. IMG needs to be a full URL.
//            );
//            $this->args['share_icons'][] = array(
//                'url'   => 'https://www.facebook.com/pages/Redux-Framework/243141545850368',
//                'title' => 'Like us on Facebook',
//                'icon'  => 'el-icon-facebook'
//            );
//            $this->args['share_icons'][] = array(
//                'url'   => 'http://twitter.com/reduxframework',
//                'title' => 'Follow us on Twitter',
//                'icon'  => 'el-icon-twitter'
//            );
//            $this->args['share_icons'][] = array(
//                'url'   => 'http://www.linkedin.com/company/redux-framework',
//                'title' => 'Find us on LinkedIn',
//                'icon'  => 'el-icon-linkedin'
//            );

        }

    }
    
    global $reduxConfig;
    $reduxConfig = new admin_folder_Redux_Framework_config();


}

/**
  Custom function for the callback referenced above
 */
if (!function_exists('admin_folder_my_custom_field')):
    function admin_folder_my_custom_field($field, $value) {
        print_r($field);
        echo '<br/>';
        print_r($value);
    }
endif;

/**
  Custom function for the callback validation referenced above
 * */
if (!function_exists('admin_folder_validate_callback_function')):
    function admin_folder_validate_callback_function($field, $value, $existing_value) {
        $error = false;
        $value = 'just testing';

        /*
          do your validation

          if(something) {
            $value = $value;
          } elseif(something else) {
            $error = true;
            $value = $existing_value;
            $field['msg'] = 'your custom error message';
          }
         */

        $return['value'] = $value;
        if ($error == true) {
            $return['error'] = $field;
        }
        return $return;
    }

endif;
