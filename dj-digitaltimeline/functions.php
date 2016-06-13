<?php 

  function digitaltimeline_scripts()
  {
    // Register the script like this for a theme:
    wp_enqueue_script( 'jquery', get_template_directory_uri() . '/scripts/vendor/jquery-1.10.1.min.js' );
    wp_enqueue_script( 'jqueryEase', get_template_directory_uri() . '/scripts/vendor/jquery.easing.1.3.js' );
    wp_enqueue_script( 'jqueryUI', get_template_directory_uri() . '/scripts/vendor/jquery-ui.min.js' );
    wp_enqueue_script( 'jqueryTouchPunch', get_template_directory_uri() . '/scripts/vendor/jquery.ui.touch-punch.min.js' );
    wp_enqueue_script( 'jsPDF', get_template_directory_uri() . '/scripts/vendor/jspdf/jspdf.min.js' ); //jspdf.min.js' );
    wp_enqueue_script( 'html2Canvas', get_template_directory_uri() . '/scripts/vendor/html2canvas/html2canvas.min.js' );
    // wp_enqueue_script( 'html2CanvasSVG', get_template_directory_uri() . '/scripts/vendor/html2canvas/html2canvas.svg.min.js' );
    wp_enqueue_script( 'canvas2Image', get_template_directory_uri() . '/scripts/vendor/html2canvas/canvas2image.js' );
    // wp_enqueue_script( 'canvg-color', get_template_directory_uri() . '/scripts/vendor/canvg/canvg/rgbcolor.js' );
    // wp_enqueue_script( 'canvg-blur', get_template_directory_uri() . '/scripts/vendor/canvg/StackBlur.js' );
    // wp_enqueue_script( 'canvg', get_template_directory_uri() . '/scripts/vendor/canvg/canvg.js' );

    wp_enqueue_script( 'metaQuery', get_template_directory_uri() . '/scripts/vendor/metaquery.min-min.js' );
    wp_enqueue_script( 'modernizer', get_template_directory_uri() . '/scripts/vendor/modernizr-2.8.3.min.js' );

    wp_enqueue_script( 'digitaltimeline-controller', get_template_directory_uri() . '/scripts/DIGITALTIMELINE.controller.js' );
    wp_enqueue_script( 'digitaltimeline-main', get_template_directory_uri() . '/scripts/DIGITALTIMELINE.main.js' );

  }

  add_action( 'wp_enqueue_scripts', 'digitaltimeline_scripts' );


  function custom_remove_cpt_slug( $post_link, $post, $leavename ) {
    if ( 'timeline' != $post->post_type || 'publish' != $post->post_status ) {
      return $post_link;
    }

    $post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );

    return $post_link;
  }

  add_filter( 'post_type_link', 'custom_remove_cpt_slug', 10, 3 );


  function custom_parse_request_tricksy( $query ) {
    // Only noop the main query
    if ( ! $query->is_main_query() )  return;

    // Only noop our very specific rewrite rule match
    if ( 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
      return;
    }

    // 'name' will be set if post permalinks are just post_name, otherwise the page rule will match
    if ( ! empty( $query->query['name'] ) ) {
      $query->set( 'post_type', array( 'post', 'page', 'timeline' ) );
    }
  }

  add_action( 'pre_get_posts', 'custom_parse_request_tricksy' );


  function create_cpt_Timeline() {
    register_post_type( 'Event Type',
      array(
        'labels' => array(
            'name' => __( 'Event Types' ),
            'singular_name' => __( 'Event Type' )
        ),

        // 'taxonomies' => array('category'),
        
        'has_archive' => false,
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        // 'exclude_from_search' => true,
        'hierarchical' => true,
        'supports' => array('title', 'editor', 'page-attributes')
      )
    );

    register_post_type( 'Timeline',
      array(
        'labels' => array(
            'name' => __( 'Timelines' ),
            'singular_name' => __( 'Timeline' )
        ),

        // 'taxonomies' => array('category'),
        
        'has_archive' => false,
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'page',
        // 'exclude_from_search' => true,
        'hierarchical' => true,
        'supports' => array('title', 'editor')
      )
    );

    register_post_type( 'Event',
      array(
        'labels' => array(
            'name' => __( 'Events' ),
            'singular_name' => __( 'Event' )
        ),

        // 'taxonomies' => array('category'),
        
        'has_archive' => false,
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'page',
        // 'exclude_from_search' => true,
        'hierarchical' => true,
        'supports' => array('title', 'editor')
      )
    );
    //register_taxonomy('event');
    // register_taxonomy_for_object_type( 'category', 'event' );
    // flush_rewrite_rules();
  }

  add_action( 'init', 'create_cpt_Timeline' );


  function bs_event_table_head( $defaults ) {
      $defaults['event_date'] = 'Event Date';
      $defaults['tl_group'] = 'Group';
      $defaults['title'] = 'Title';
      unset($defaults['date']);
      return $defaults;
  }

  add_filter('manage_event_posts_columns', 'bs_event_table_head');


  function bs_event_table_content( $column_name, $post_id ) {
      if ($column_name == 'event_date') {
        $data = get_field('event_date', $post_id);
        // $y = substr($data, 0, 4);
        // $m = substr($data, 4, 2);
        // $d = substr($data, 6, 2);
        // $date = $y . '/' . $m . '/' . $d;
        // echo $date;
        echo date( _x( 'F d, Y', 'Event date format', 'textdomain' ), strtotime($data) );
      }

      if ($column_name == 'tl_group') {
        $groupObj = get_field('tl_group', $post_id);
        
        $i = 0;
        foreach($groupObj as $g):
          setup_postdata($g);
          if ($i > 0) echo ", ";
          echo get_the_title($g);
          $i++;
        endforeach;

        wp_reset_postdata();
      }
  }

  add_action( 'manage_event_posts_custom_column', 'bs_event_table_content', 10, 2 );


  // function bs_event_table_content( $column_name, $post_id ) {
  //   if ($column_name == 'event_date') {
  //   $event_date = get_post_meta( $post_id, '_bs_meta_event_date', true );
  //     echo  date( _x( 'F d, Y', 'Event date format', 'textdomain' ), strtotime( $event_date ) );
  //   }
  //   if ($column_name == 'ticket_status') {
  //   $status = get_post_meta( $post_id, '_bs_meta_event_ticket_status', true );
  //   echo $status;
  //   }

  //   if ($column_name == 'venue') {
  //   echo get_post_meta( $post_id, '_bs_meta_event_venue', true );
  //   }
  // }

  // add_action( 'manage_event_posts_custom_column', 'bs_event_table_content', 10, 2 );


  function bs_event_table_sorting( $columns ) {
    $columns['event_date'] = 'event_date';
    // $columns['categories'] = 'categories';
    $columns['tl_group'] = 'tl_group';
    return $columns;
  }

  add_filter( 'manage_edit-event_sortable_columns', 'bs_event_table_sorting' );


  function bs_event_date_column_orderby( $vars ) {
    if ( isset( $vars['orderby'] ) && 'event_date' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => 'event_date',
            'orderby' => 'meta_value'
        ) );
    }

    if ( isset( $vars['orderby'] ) && 'tl_group' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => 'tl_group',
            'orderby' => 'meta_value'
        ) );
    }

    return $vars;
  }

  add_filter( 'request', 'bs_event_date_column_orderby' );


  function remove_admin_menu_items() {
    $remove_menu_items = array(__('Comments'),__('Dashboard'),__('Posts'));//,__('Pages'));
    global $menu;
    end ($menu);
    while (prev($menu)){
      $item = explode(' ',$menu[key($menu)][0]);
      if(in_array($item[0] != NULL?$item[0]:"" , $remove_menu_items)){
      unset($menu[key($menu)]);}
    }
  }

  add_action('admin_menu', 'remove_admin_menu_items');


  function my_login_logo() { ?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/login-logo.png);
            background-size: 300px;
            width: 300px;
            height: 218px;
        }
    </style>
  <?php }

  add_action( 'login_enqueue_scripts', 'my_login_logo' );

?>