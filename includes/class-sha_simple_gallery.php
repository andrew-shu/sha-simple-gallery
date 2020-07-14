<?php

class SHA_Simple_Gallery {
	 
	protected $_module_slug = 'sha_sgal';
	
  protected $_taxonomy = 'sha-sgal-gals';
	
  protected $_shortcode = 'cptgallery';

	protected $_settings = [];
  
	// Base initing function
  public function init() {

		$this->init_variables();

		// Init hooks only once
		if ( !defined( 'SHA_SGAL_INITED' ) || ( true !== SHA_SGAL_INITED ) ) {
			define( 'SHA_SGAL_INITED', true );
			$this->init_admin_hooks();
			$this->init_public_hooks();
		}
	}

	// Initing all variables
	private function init_variables() {

		$default_settings = [
      'default'             => 4,
      'variants'            => [
        '2' => __( '2 images', 'sha-sgal' ),
        '3' => __( '3 images', 'sha-sgal' ),
        '4' => __( '4 images', 'sha-sgal' ),
        '5' => __( '5 images', 'sha-sgal' ),
        '6' => __( '6 images', 'sha-sgal' )
      ],
      'default_thumb_size'  => 'medium',
      'default_full_size'   => 'large',
      'load_js'             => true,
      'load_css'            => true,
    ];
    
    $this->_settings = $default_settings;
	}

	// ReDefine variables
	public function define_variables() {

    $this->_settings = apply_filters( 'sha_sgal_settings', $this->_settings );
  }

	// Initing all admin actions and filters
	private function init_admin_hooks() {

		add_action( 'init', [ $this, 'define_variables' ] );
		add_action( 'init', [ $this, 'register_cpt' ] );
		add_action( 'init', [ $this, 'load_textdomain' ] );
    add_action( 'admin_head', [ $this, 'hide_parent_taxonomy' ] );
		add_action( 'enter_title_here', [ $this, 'change_placeholder' ] );
		add_action( 'current_screen', [ $this, 'current_screen' ] );
		add_action( 'wp_dropdown_cats', [ $this, 'override_taxonomy_dropdown' ], 10, 2 );
    add_action( 'restrict_manage_posts', [ $this, 'gallery_filter_admin_grid' ] );
    add_filter( 'parse_query', [ $this, 'add_gallery_filter' ] );
    add_action( "manage_edit-{$this->_taxonomy}_columns", [ $this, 'override_gallery_grid_columns' ], 10, 2 );
		add_action( "manage_{$this->_taxonomy}_custom_column", [ $this, 'add_shortcode_value_to_admin_grid' ], 10, 3 );
		add_action( "{$this->_taxonomy}_add_form_fields", [ $this, 'add_extra_fields_to_add_form' ] );
		add_action( "{$this->_taxonomy}_edit_form_fields", [ $this, 'add_shortcode_field_to_edit_form' ] );
    add_action( "edited_{$this->_taxonomy}", [ $this, 'gallery_save_controller' ] );
		add_action( "create_{$this->_taxonomy}", [ $this, 'gallery_save_controller' ] );

    add_shortcode( $this->_shortcode, [ $this, 'add_shortcode' ] );
	}

	// Initing all public actions and filters
	private function init_public_hooks() {
    add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles_and_scripts' ] );
	}

	// Register custom post type and taxonomy
	public function register_cpt() {
		register_post_type( $this->_module_slug,
			[
				'labels'              => [
					'name'					      => __( 'CPT gallery', 'sha-sgal' ),
					'add_new'				      => __( 'Add New Picture', 'sha-sgal' ),
					'add_new_item'			  => __( 'Add New Picture', 'sha-sgal' ),
          'all_items'           => __( 'Gallery Pictures', 'sha-sgal' ),
					'edit'				      	=> __( 'Edit Picture', 'sha-sgal' ),
					'edit_item'   				=> __( 'Edit Picture', 'sha-sgal' ),
					'new_item'	   		  	=> __( 'New Picture', 'sha-sgal' ),
					'view'	    		  		=> __( 'View Picture', 'sha-sgal' ),
					'view_item'				    => __( 'View Picture', 'sha-sgal' ),
					'search_items'   			=> __( 'Search Pictures', 'sha-sgal' ),
					'not_found'				    => __( 'No Pictures found', 'sha-sgal' ),
					'not_found_in_trash'	=> __( 'No Pictures found in Trash', 'sha-sgal' ),
					'parent'				      => __( 'Parent Picture', 'sha-sgal' ),
				],
				'public'		          => true,
        'publicly_queryable'  => false, 
				'menu_position'       => 29,
				'supports'            => [
						'title',
						'excerpt',
						'thumbnail'					
        ],
				'taxonomies'        	=> [
					$this->_taxonomy,
				],
				'menu_icon'       		=> 'dashicons-format-gallery',
				'has_archive'        	=> false
			]
		);
		
		if ( !taxonomy_exists( $this->_module_slug ) ) {
			register_taxonomy(
				$this->_taxonomy,
				$this->_module_slug,
				[
					'hierarchical'  			=> true,
          'labels'              => [
            'name'                  => __( 'Galleries', 'sha-sgal' ),
            'singular_name'         => __( 'Gallery', 'sha-sgal' ),
            'search_items'          => __( 'Search Galleries', 'sha-sgal' ),
            'all_items'             => __( 'Galleries', 'sha-sgal' ),
            'edit_item'             => __( 'Edit Gallery', 'sha-sgal' ),
            'view_item'             => __( 'View Gallery', 'sha-sgal' ),
            'update_item'           => __( 'Update Gallery', 'sha-sgal' ),
            'add_new_item'          => __( 'Add New Gallery', 'sha-sgal' ),
            'new_item_name'         => __( 'New Gallery Name', 'sha-sgal' ),
            'not_found'             => __( 'No galleries found.', 'sha-sgal' ),
            'no_terms'              => __( 'No galleries', 'sha-sgal' ),
            'items_list_navigation' => __( 'Galleries list navigation', 'sha-sgal' ),
            'items_list'            => __( 'Galleries list', 'sha-sgal' ),
            'most_used'             => __( 'Most Used Galleries', 'sha-sgal' ),
            'back_to_items'         => __( '&larr; Back to Galleries', 'sha-sgal' ),
            'menu_name'             => __( 'Galleries', 'sha-sgal' ),
            'name_admin_bar'        => __( 'Galleries', 'sha-sgal' ),
            'archives'              => __( 'Galleries', 'sha-sgal' ),
            'parent_item'           => __( 'Parent Gallery', 'sha-sgal' )
          ],
          'publicly_queryable'  => false,
					'show_in_quick_edit'	=> true,
					'show_admin_column'		=> true,
					'capabilities'        => []
				]
			);
		}
	}

	// Load textdomain
	public function load_textdomain() {

		load_plugin_textdomain( 'sha-sgal', false, plugin_dir_path( SHA_SGAL_PLUGIN_FILE ) . 'languages' );
	}
  
  // Change placeholder text
  public function change_placeholder( $title ) {
     $screen = get_current_screen();
  
     if  ( $this->_module_slug == $screen->post_type ) {
          $title = 'Type picture name';
     }
  
     return $title;
  }	

  // Current screen handler
  public function current_screen( $screen ) {
    if ( is_object( $screen ) && ( $screen->post_type == $this->_module_slug ) ) {
      add_filter( 'gettext', [ $this, 'override_excerpt_text' ], 10, 2 );
    }
  }
  
  // Override default excerpt labels
  public function override_excerpt_text( $translation, $original ) {
    global $post;
    if ( 'Excerpt' == $original ) {
      return __( 'Picture description', 'sha-sgal' );
    } else {
      $pos = strpos( $original, 'Excerpts are optional hand-crafted summaries of your' );
      if ( $pos !== false ) {
        return  __( 'Description for picture', 'sha-sgal' );
      }
    }

    return $translation;
  }
  
  // Override parent item dropdown to hidden field
  public function override_taxonomy_dropdown( $output, $r ) { 
    if ( $r['taxonomy'] == $this->_taxonomy ) {
      return $this->get_module_template(
        'admin/templates/elements/taxonomy_hidden.phtml'
      );
    }

    return $output; 
  }
  
  // Add styles to admin head to hide slug and parent item dropdown
  public function hide_parent_taxonomy() {
      echo $this->get_module_template(
        'admin/templates/elements/admin_head_styles.phtml'
      );
  }

	// Change order and add extra fields in gallery grid
  public function override_gallery_grid_columns( $columns ) {
    if ( isset( $_REQUEST['taxonomy'] ) && ( $_REQUEST['taxonomy'] == $this->_taxonomy ) ) {
      return [
        'cb'        => '<input type="checkbox" />',
        'name'      => __( 'Gallery Name', 'sha-sgal' ),
        'shortcode'	=> __( 'Gallery Shortcode', 'sha-sgal' ),
        'posts'     => __( 'Images', 'sha-sgal' )
      ];
    }
    
    return $columns;
	}
	
	// Output gallery shortcode in admin grid
  public function add_shortcode_value_to_admin_grid( $value, $name, $id ) {
		return 'shortcode' === $name ? '[cptgallery id=' . $id . ']' : $value;
	}
  
  // Add extra fields to gallery list page
	public function add_extra_fields_to_add_form() {
    global $_wp_additional_image_sizes;
    echo $this->get_module_template(
      'admin/templates/elements/gallery_extra_fields.phtml',
      [
        '_wp_additional_image_sizes' => $_wp_additional_image_sizes
      ]
    );
	}

	// Add extra fields to gallery edit page
  public function add_shortcode_field_to_edit_form( $term ) {
    global $_wp_additional_image_sizes;

    echo $this->get_module_template(
      'admin/templates/elements/gallery_edit_extra_fields.phtml',
      [
        't_id'                        => $term->term_id,
        'term_meta'                   => get_option( "cpt_gal_{$term->term_id}" ),
        '_wp_additional_image_sizes'  => $_wp_additional_image_sizes
      ]
    );
	}

	// Save gallery extra field on save
  public function gallery_save_controller( $term_id ) {
		if ( isset( $_POST['term_meta'] ) ) {
			$t_id = $term_id;
			$term_meta = get_option( "cpt_gal_{$t_id}" );
			$cat_keys = array_keys( $_POST['term_meta'] );
			foreach ( $cat_keys as $key ) {
				if ( isset ( $_POST['term_meta'][$key] ) ) {
					$term_meta[$key] = $_POST['term_meta'][$key];
				}
			}

			update_option( "cpt_gal_{$t_id}", $term_meta );
		}
	}

	// Register and output shortcode
  public function add_shortcode( $atts ) {    
		if ( isset( $atts['id'] ) ) {
      $gal_id = (int)$atts['id'];
      $gallery_settings = get_option( "cpt_gal_{$gal_id}" );
			$gallery_data = get_term( $gal_id );
			$args = [
				'post_type'		=> $this->_module_slug,
				'post_status'	=> 'publish',
        'orderby'     => 'date',
        'order'       => 'DESC',
				'tax_query'		=> [
					[
						'taxonomy'	=> $this->_taxonomy,
						'field'		  => 'id',
						'terms'		  => $atts['id']
					]
				]
			];
			
			$loop = new WP_Query( $args );

			$pictures = [];

			if ( $loop->have_posts() ) {
				while ( $loop->have_posts() ) {
					$loop->the_post();

          if ( !has_post_thumbnail( get_the_ID() ) ) {
            continue;
          }

          $thumb_size = isset( $gallery_settings['cpt_thumb_size'] ) ? $gallery_settings['cpt_thumb_size'] : $this->_settings['default_thumb_size'];
					$pictures[] = [
						'id'		  => get_the_ID(),
						'title'		=> esc_html( strip_tags( get_the_title() ) ),
						'caption'	=> esc_html( strip_tags( get_the_excerpt() ) ),
						'thumb'		=> get_the_post_thumbnail_url(get_the_ID(), $thumb_size ),
						'large'		=> get_the_post_thumbnail_url(get_the_ID(), $this->_settings['default_full_size'] )
					];
				}
			}

      $theme_url = is_child_theme()
        ? get_stylesheet_directory()
        : get_template_directory();

      $theme_url = trailingslashit( $theme_url );
      $plugin_dir_exploded_string = explode( '/', SHA_SGAL_PLUGIN_FILE );
      $plugin_name = $plugin_dir_exploded_string[ count( $plugin_dir_exploded_string ) - 2 ];

      if ( file_exists( $theme_url . $plugin_name . '/shortcode.phtml' ) ) {
        $template = $theme_url . $plugin_name . '/shortcode.phtml';
        $global = 1;
      } else {
        $template = 'public/templates/elements/shortcode.phtml';
        $global = 0;
			}

			return $this->get_module_template(
        $template,
        [
          'gallery_id'        => $gal_id,
          'gallery_settings'  => $gallery_settings,
          'gallery_data'      => $gallery_data,
          'gallery_pics'      => $pictures
        ],
        $global
      );
		}
	}

  // Enqueue fancybox css/js
  public function enqueue_styles_and_scripts() {
    
    $timestamp = ( WP_DEBUG ) ? time() : date('dmY');

    wp_enqueue_style(
      'fancybox',
      plugin_dir_url( SHA_SGAL_PLUGIN_FILE ) . 'public/css/fancybox/jquery.fancybox.css',
      [],
      $timestamp,
      'all'
    );
    
    if ( $this->_settings['load_css'] ) {
      wp_enqueue_style(
        $this->_module_slug,
        plugin_dir_url( SHA_SGAL_PLUGIN_FILE ) . 'public/css/styles.css',
        [],
        $timestamp,
        'all'
      );
    }

    wp_enqueue_script(
      'fancybox',
      plugin_dir_url( SHA_SGAL_PLUGIN_FILE ) . 'public/js/fancybox/jquery.fancybox.pack.js',
      [ 'jquery' ],
      $timestamp,
      false
    );

    if ( $this->_settings['load_js'] ) {
      wp_enqueue_script(
        $this->_module_slug,
        plugin_dir_url( SHA_SGAL_PLUGIN_FILE ) . 'public/js/scripts.js',
        [ 'jquery' ],
        $timestamp,
        false
      );
    }
  }

  // Add gallery filter to pictures admin grid
  public function gallery_filter_admin_grid() {
    
    $type = 'post';

    if ( isset( $_GET['post_type'] ) ) {
      $type = $_GET['post_type'];
    }

    if ( $this->_module_slug == $type ) {
      $terms = get_terms( $this->_taxonomy );
      echo $this->get_module_template(
        'admin/templates/elements/gallery_filter_dropdown.phtml',
        [
          'terms' => $terms
        ]
      );
    }
  }

  // Add filter by gallery to pictures admin grid
  public function add_gallery_filter( $query ) {
      global $pagenow;

      if ( !isset( $_GET['gal_id'] ) ) {
        return;
      }

      if ( $_GET['gal_id'] == -1 ) {
        return;
      }
      
      $type = 'post';

      if ( isset( $_GET['post_type'] ) ) {
        $type = $_GET['post_type'];
      }
            
      $qv = &$query->query_vars;

      if ( is_admin() && ( $this->_module_slug == $type ) && ( $pagenow == 'edit.php' ) && !empty( $_GET['gal_id'] ) ) {
          $term = get_term_by('id', $_GET['gal_id'], $this->_taxonomy );
          $qv[ $this->_taxonomy ] = $term->slug;
      }
  }

 	// Get template and output it's html
	private function get_module_template( $template, $args = [], $global_template = 0 ) {
		
    if ( $global_template == 1 ) {
      $template_file = $template;
    } else {
      $template_file = sprintf(
        '%s%s',
        plugin_dir_path( SHA_SGAL_PLUGIN_FILE ),
        $template
      );
    }

		$args['module_slug'] = $this->_module_slug;
		extract( $args );

		ob_start();
		require( $template_file );
		return ob_get_clean();
	}
}
