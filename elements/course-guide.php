<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Themo_Widget_Course_Guide extends Widget_Base {

    public function get_name() {
        return 'themo-portfolio-grid';
    }

    public function get_title() {
        return __( 'Course Guide', 'th-widget-pack' );
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    public function get_categories() {
        return [ 'themo-elements' ];
    }

    private function get_tours_list() {
        $portfolio = array();

        $loop = new \WP_Query( array(
            'post_type' => array('themo_portfolio'),
            'posts_per_page' => -1,
            'post_status'=>array('publish'),
        ) );

        $portfolio['none'] = __('None', 'th-widget-pack');

        while ( $loop->have_posts() ) : $loop->the_post();
            $id = get_the_ID();
            $title = get_the_title();
            $portfolio[$id] = $title;
        endwhile;

        //wp_reset_query();
        wp_reset_postdata();

        return $portfolio;
    }

    private function get_project_group_list() {
        $portfolio_group = array();

        $portfolio_group['none'] = __( 'None', 'th-widget-pack' );

        $taxonomy = 'themo_hole_type';

        $tax_terms = get_terms( $taxonomy );

        if ( ! empty( $tax_terms ) && ! is_wp_error( $tax_terms ) ){
            foreach( $tax_terms as $item ) {
                $portfolio_group[$item->term_id] = $item->name;
            }
        }

        return $portfolio_group;
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'section_layout',
            [
                'label' => __( 'Layout', 'th-widget-pack' ),
            ]
        );

        $this->add_control(
            'filter',
            [
                'label' => __( 'Show filter bar', 'th-widget-pack' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' => __( 'Yes', 'th-widget-pack' ),
                'label_off' => __( 'No', 'th-widget-pack' ),
            ]
        );

        $this->add_control(
            'individual',
            [
                'label'   => __( 'Select Individually', 'th-widget-pack' ),
                'type'    => Controls_Manager::SELECT2,
                'label_block' => true,
                'multiple'    => true,
                //'default' => 'none',
                'options' => $this->get_tours_list()
            ]
        );

        $this->add_control(
            'group',
            [
                'label'   => __( 'Select by Group', 'th-widget-pack' ),
                'type'    => Controls_Manager::SELECT2,
                'label_block' => true,
                'multiple'    => true,
                //'default' => 'none',
                'options' => $this->get_project_group_list()
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => __( 'Order by', 'th-widget-pack' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'menu_order',
                'options' => [
                    'date' => __( 'Date Published', 'th-widget-pack' ),
                    'menu_order' => __( 'Drag and Drop', 'th-widget-pack' ),
                ],
            ]
        );

        $this->add_control(
            'columns',
            [
                'label' => __( 'Number of Columns to show', 'th-widget-pack' ),
                'type' => Controls_Manager::SELECT,
                'default' => '2',
                'options' => [
                    '2' => __( '2', 'th-widget-pack' ),
                    '3' => __( '3', 'th-widget-pack' ),
                    '4' => __( '4', 'th-widget-pack' ),
                    '5' => __( '5', 'th-widget-pack' ),
                ],
            ]
        );

        $this->add_control(
            'gutter',
            [
                'label' => __( 'Gutter', 'th-widget-pack' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'on',
                'options' => [
                    'on' => __( 'On', 'th-widget-pack' ),
                    'off' => __( 'Off', 'th-widget-pack' )
                ],
            ]
        );


        $default_rgba = 'rgba(0, 0, 0, 0.75)'; // Fallback RGBA\

        if ( function_exists( 'get_theme_mod' ) ) {

            $default_hex = get_theme_mod( 'color_primary', $default_rgba );

            // Test if HEX, then convert to RGBA, else use RGBA
            if (isset($default_hex) && strpos($default_hex, '#') !== false) {
                list($r, $g, $b) = sscanf($default_hex, "#%02x%02x%02x");
                $default_rgba = "rgba(".$r .", ". $g. ", ". $b . ", 0.75)";
            }elseif(isset($default_hex)){
                $default_rgba = $default_hex;
            }

            error_log("RGBA: ".$default_rgba,0);
        }


        $this->add_control(
            'hover_color',
            [
                'label' => __( 'Hover Color', 'th-widget-pack' ),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_3,
                ],
                'default' => $default_rgba,
                'selectors' => [
                    '{{WRAPPER}} .th-portfolio-item:hover .th-port-overlay' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        /*$this->start_controls_section(
            'section_style_content',
            [
                'label' => __( 'Content', 'th-widget-pack' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->end_controls_section();*/

    }

    protected function render() {
        $settings = $this->get_settings();

        global $th_folio_count;
        $folio_id = 'th-portfolio-' . ++$th_folio_count;


        switch( $settings['columns'] ) {
            case 2:
                $portfolio_row = ' two-columns';
                $portfolio_item = array('th-portfolio-item', 'item', 'col-sm-6');
                break;
            case 3:
                $portfolio_row = ' three-columns';
                $portfolio_item = array('th-portfolio-item', 'item', 'col-md-4', 'col-sm-6');
                break;
            case 4:
                $portfolio_row = ' four-columns';
                $portfolio_item = array('th-portfolio-item', 'item', 'col-md-3', 'col-sm-6');
                break;
            case 5:
                $portfolio_row = ' five-columns';
                $portfolio_item = array('th-portfolio-item', 'item', 'col-md-2', 'col-sm-6');
                break;
            default:
                $portfolio_row = '';
                $portfolio_item = array();
        }

        if ( isset( $settings['gutter'] ) &&  $settings['gutter'] == 'on' ){
            $portfolio_row .= ' th-port-gutter';
        }

        ?>

        <?php
        $th_uid = uniqid( 'th-portfolio-content-' );
        ?>
        <div id="<?php echo esc_attr($th_uid); ?>" class="th-portfolio">

            <?php if ( $settings['filter'] == 'yes' ) : ?>

                <div id="filters" class="th-portfolio-filters">
                    <span><?php echo esc_html__( 'Sort:', 'th-widget-pack' ); ?></span>
                    <a href="#" data-filter="*" class="current"><?php echo esc_html__( 'All', 'th-widget-pack' ); ?></a>
                    <?php
                    $taxonomy = 'themo_hole_type';

                    // Only show filter links for the groups selected.
                    $tax_args = array(
                        'taxonomy' => $taxonomy,
                        'include' => $settings['group'],
                        'hide_empty' => false,
                    );

                    $tax_terms = get_terms( $tax_args );

                    foreach ( $tax_terms as $tax_term ) {
                        echo '<a href="#" data-filter="#'.esc_attr($th_uid).' .p-' . esc_attr($tax_term->slug) . '">' . esc_html($tax_term->name) .'</a>';
                    }
                    ?>
                </div>

            <?php endif; ?>

            <div id="th-portfolio-row" class="th-portfolio-row row portfolio_content <?php echo esc_attr($portfolio_row); ?>">

                <?php
                $args = array();
                if ( $settings['individual'] ) {
                    if ( in_array( 'none', $settings['individual'] ) ) {
                        $settings['individual'] = array_diff( $settings['individual'], array( 'none' ) );
                    }
                    if ( $settings['individual'] ) {
                        $post_ids = $settings['individual'];
                        $args['post__in'] = $post_ids;
                    }
                }
                $args['post_type'] = array( 'themo_hole' );
                if ( $settings['group'] ) {
                    if ( in_array( 'none', $settings['group'] ) ) {
                        $settings['group'] = array_diff( $settings['group'], array( 'none' ) );
                    }
                    if ( $settings['group'] ) {
                        $project_type_id = $settings['group'];
                        $args['tax_query'] = array(
                            array(
                                'taxonomy' => 'themo_hole_type',
                                'field'    => 'term_id',
                                'terms'    => $project_type_id,
                                //'operator' => 'IN',
                            ),
                        );
                    }
                }
                if ( $settings['order'] == 'date' ) {
                    $args['orderby'] = 'date';
                } elseif ( $settings['order'] == 'menu_order' ) {
                    $args['orderby'] = 'menu_order';
                    $args['order'] = 'ASC';
                }
                $args['post_status'] = 'publish';
                $args['posts_per_page'] = -1;

                // The Query
                $query = new \WP_Query( $args );

                // The Loop
                if ( $query->have_posts() ) {
                    while ( $query->have_posts() ) {
                        $query->the_post();
                        // get post format
                        $format = get_post_format();
                        if ( false === $format ) {
                            $format = '';
                        }

                        // default settings
                        $link_url = get_the_permalink();
                        $link_title = get_the_title();
                        $link_target_markup = false;
                        $img_src = false;
                        $alt_text = '';

                        // Link post type options
                        if ( isset( $format ) && $format == 'link' ) {

                            $link_url = get_post_meta( get_the_ID(), '_format_link_url', true );
                            $link_title = get_post_meta( get_the_ID(), '_format_link_title', true );
                            $link_target = get_post_meta( get_the_ID(), '_format_link_target' );

                            if ( ! $link_url > "" ) {
                                $link_url = get_the_permalink();
                            }

                            // Link Target
                            if( isset( $link_target[0][0] ) && $link_target[0][0] == "_blank" ) {
                                $link_target_markup = "target='_blank'";
                            }

                            // Custom Title
                            if( ! $link_title > "" ) {
                                $link_title = get_the_title();
                            }
                        }

                        // Get Project Format Options
                        $project_thumb_alt_img = get_post_meta( get_the_ID(), 'th_project_thumb', false );

                        $fallback_lightbox_url = false;

                        if ( isset( $project_thumb_alt_img[0] ) && $project_thumb_alt_img[0] > "" ) {
                            $alt = false;

                            // Check if Image comes in Med size with Square crop / else get small
                            $img_src = themo_return_metabox_image( $project_thumb_alt_img[0], null, "th_img_md_square", true, $alt );

                            list($th_actual_width, $th_actual_height) = getimagesize($img_src);
                            if ((605 !== $th_actual_width) && (605 !== $th_actual_height)){

                                // Check if Image comes in Small size with Square crop / else get thumb
                                $img_src = themo_return_metabox_image( $project_thumb_alt_img[0], null, "th_img_sm_square", true, $alt );

                                list($th_actual_width, $th_actual_height) = getimagesize($img_src);

                                if ((394 !== $th_actual_width) && (394 !== $th_actual_height)){
                                    $img_src = themo_return_metabox_image( $project_thumb_alt_img[0], null, "thumbnail", true, $alt );
                                }
                            }

                            $alt_text = $alt;
                            $fallback_lightbox_url = themo_return_metabox_image( $project_thumb_alt_img[0], null, "th_img_xl", true, $alt );

                        }

                        //Image post type options
                        if( isset( $format ) && $format == 'image' ) {

                            // Fallback lightbox url
                            $link_url = $fallback_lightbox_url;

                            // lightbox mark up
                            $featured_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'th_img_xl' );
                            if( isset( $featured_url[0] ) ) {
                                $link_url = $featured_url[0];
                            }
                            $elementor_global_image_lightbox = get_option('elementor_global_image_lightbox');

                            if (!empty($elementor_global_image_lightbox) && $elementor_global_image_lightbox == 'yes') {
                                $link_target_markup = false;
                            }else{
                                $link_target_markup = ' data-toggle=lightbox data-gallery=multiimages';
                            }

                            $link_title = the_title_attribute( 'echo=0' );
                        }

                        $filtering_links = array();
                        $terms = get_the_terms( get_the_ID(), 'themo_hole_type' );
                        if ( $terms && ! is_wp_error( $terms ) ) {
                            foreach ( $terms as $term ) {
                                $filtering_links[] = 'p-' . $term->slug;
                            }
                        }

                        $classes = array_merge( $portfolio_item, $filtering_links );
                        ?>
                        <div id="post-<?php the_ID(); ?>" <?php post_class( $classes ); ?>>
                            <div class="th-port-wrap">
                                <?php
                                if ( isset( $img_src ) && $img_src > "" ) {
                                    echo '<img class="img-responsive th-port-img" src="' . esc_url( $img_src ) . '" alt="' . esc_attr( $alt_text ) . '">';
                                } else {
                                    if ( has_post_thumbnail( get_the_ID() ) ) {
                                        $featured_img_attr = array( 'class'	=> "img-responsive th-port-img" );

                                        $th_id = get_post_thumbnail_id(get_the_ID());
                                        $th_image = wp_get_attachment_image_src($th_id, "th_img_md_square");

                                        if ($th_image){
                                            list($width, $height) = getimagesize($th_image[0]);
                                            if ((605 == $width) && (605 == $height)){
                                                echo wp_kses_post(get_the_post_thumbnail( get_the_ID(), "th_img_md_square", $featured_img_attr ));
                                            }
                                            else{
                                                $th_image = wp_get_attachment_image_src($th_id, "th_img_sm_square");
                                                list($width, $height) = getimagesize($th_image[0]);
                                                if ((394 == $width) && (394 == $height)){
                                                    echo wp_kses_post(get_the_post_thumbnail( get_the_ID(), "th_img_sm_square", $featured_img_attr ));
                                                }else{
                                                    //default when no image
                                                    echo wp_kses_post(get_the_post_thumbnail( get_the_ID(), "thumbnail", $featured_img_attr ));
                                                }

                                            }
                                        }
                                    }
                                }

                                $th_project_title = get_the_title();
                                $th_project_title_meta = get_post_meta( get_the_ID(), 'th_project_title', true );
                                if( $th_project_title_meta > "" ) {
                                    $th_project_title = $th_project_title_meta;
                                }

                                $th_project_highlight = false;
                                $th_project_highlight = get_post_meta( get_the_ID(), 'th_project_highlight', true );

                                $th_project_intro = false;
                                $th_project_intro = get_post_meta( get_the_ID(), 'th_project_intro', true );
                                if( $th_project_intro === false || empty( $th_project_intro ) ) {
                                    $automatic_post_excerpts = 'on';
                                    if ( function_exists( 'get_theme_mod' ) ) {
                                        $automatic_post_excerpts = get_theme_mod( 'themo_automatic_post_excerpts', 'on' );
                                    }
                                    if( $automatic_post_excerpts === 'off' ) {
                                        $th_project_intro = apply_filters( 'the_content', get_the_content() );
                                        $th_project_intro = str_replace( ']]>', ']]&gt;', $th_project_intro );
                                        if( $th_project_intro != "" ) {
                                            $th_project_intro = '<p class="th-port-sub">' . $th_project_intro . '</p>';
                                        }
                                    } else {
                                        $th_project_intro = apply_filters( 'the_excerpt', get_the_excerpt() );
                                        $th_project_intro = str_replace( ']]>', ']]&gt;', $th_project_intro );
                                        $th_project_intro = str_replace( '<p', '<p class="th-port-sub"', $th_project_intro );
                                    }
                                }else{
                                    $th_project_intro = '<p class="th-port-sub">' . $th_project_intro . '</p>';
                                }

                                $th_project_button_text = false;
                                $th_project_button_text = get_post_meta( get_the_ID(), 'th_project_button_text', true );
                                ?>

                                <div class="th-port-overlay"></div>
                                <div class="th-port-inner">
                                    <?php if( $th_project_highlight ) { ?>
                                        <div class="th-port-top-text"><?php echo esc_html($th_project_highlight); ?></div>
                                    <?php } ?>
                                    <div class="th-port-center">
                                        <h3 class="th-port-title"><?php echo esc_html( $th_project_title ); ?></h3>
                                        <?php echo wp_kses_post($th_project_intro); ?>
                                        <?php if( ! $th_project_button_text === false || ! empty( $th_project_button_text ) ) { ?>
                                            <span class="th-port-btn"><?php echo esc_html( $th_project_button_text ); ?></span>
                                        <?php } ?>
                                    </div>
                                    <?php echo '<a href="' . esc_url( $link_url ) . '" class="th-port-link" ' . esc_html( $link_target_markup ) . '></a>'; ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="alert">';
                    _e('Sorry, no results were found.', 'th-widget-pack');
                    echo '</div>';
                    get_search_form();
                }
                // Restore original Post Data
                wp_reset_postdata();
                ?>

            </div>

        </div>

        <?php
    }

    protected function _content_template() {}
}

Plugin::instance()->widgets_manager->register_widget_type( new Themo_Widget_Course_Guide() );
