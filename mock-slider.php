<?php
/**
 * Plugin Name: WP Mock Slider
 * Description: Wp Mock Slider is an awesome WordPress slider plugin with a lot of nice features. Just install and build sliders in a few minutes.
 * Version: 1.0.0
 * Author: Muhammad Umer
 * Author URI: http://facebook.com/sangherra1
 * License: GPL2
 */
 
define('WMS_PATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );
define('WMS_NAME', "WP Mock Slider");
define ("WMS_VERSION", "1.0.0");


wp_enqueue_script('mock-slider-classie', WMS_PATH.'js/modernizr.custom.js', array('jquery'));
wp_enqueue_script('mock-slider-modernizr', WMS_PATH.'js/classie.js', array('jquery'));
wp_enqueue_script('mock-slider-main', WMS_PATH.'js/main.js', array('jquery'));
wp_enqueue_style('mock-slider-normalize_css', WMS_PATH.'css/normalize.css');
wp_enqueue_style('mock-slider-demo_css', WMS_PATH.'css/demo.css'); 
wp_enqueue_style('mock-slider-animate_css', WMS_PATH.'css/animate.css'); 
wp_enqueue_style('mock-slider-mockup2_css', WMS_PATH.'css/mockup2.css'); 

function WMS_script(){
 
print '<script>
  (function() {
				new Slideshow( document.getElementById( "slideshow" ) );

				/* Mockup responsiveness */
				var body = docElem = window.document.documentElement,
					wrap = document.getElementById( "wrap" ),
					mockup = WMSwrap.querySelector( ".mockup" ),
					mockupWidth = mockup.offsetWidth;

				scaleMockup();

				function scaleMockup() {
					var wrapWidth = WMSwrap.offsetWidth,
						val = wrapWidth / mockupWidth;

					mockup.style.transform = "scale3d(" + val + ", " + val + ", 1)";
				}
				
				window.addEventListener( "resize", resizeHandler );

				function resizeHandler() {
					function delayed() {
						resize();
						resizeTimeout = null;
					}
					if ( typeof resizeTimeout != "undefined" ) {
						clearTimeout( resizeTimeout );
					}
					resizeTimeout = setTimeout( delayed, 50 );
				}

				function resize() {
					scaleMockup();
				}
			})();
</script>';
 
}
 
add_action('wp_footer', 'WMS_script');

function WMS_get_slider(){
 
$slider= '<div class="demo-2">
		<div class="container">
<div id="WMSwrap" class="WMSwrap">
<div class="mockup">
<img class="mockup__img" src="'.WMS_PATH.'img/mockup2.jpg" />
<div id="" class="screen">
      <ul id="slideshow" class="slideshow">';
 
    $WMS_query= "post_type=slider-image";
    query_posts($WMS_query);
     
     
    if (have_posts()) : while (have_posts()) : the_post(); 
        $img= get_the_post_thumbnail( $post->ID, 'large' );
        $custom = get_post_custom($post->ID);
  		$large_heading = $custom["large_heading"][0];
		$slide_Description = $custom["slide_Description"][0];
		$slide_link = $custom["slide_link"][0];
		$link_text = $custom["link_text"][0];
		$link_target = $custom["link_target"][0];
		  
        $slider.='<li class="slideshow__item">'.$img.'<header class="codrops-header">
						<h1>'.esc_html($large_heading).'<i>'.esc_html($slide_Description).'</i><br/><a href="'.esc_url($slide_link).'" target = "'.esc_html($link_target).'">'.esc_html($link_text).'</a></h1>
					</header></li>';
          
    endwhile; endif; wp_reset_query();
 
 
    $slider.= '</ul></div>
	
				</div>
			</div>
			</div>
			</div>';
     
    return $slider;
 
}
 
 
/**add the shortcode for the slider- for use in editor**/
 
function WMS_insert_slider($atts, $content=null){
 
$slider= WMS_get_slider();
 
return $slider;
 
}
 
 
add_shortcode('MS_slider', 'WMS_insert_slider');
 
 
 
/**add template tag- for use in themes**/
 
function WMS_slider(){
 
    print WMS_get_slider();
}

define('CPT_NAME', "WP Mock Slider");
define('CPT_SINGLE', "Slider Image");
define('CPT_TYPE', "slider-image");
 
add_theme_support('post-thumbnails', array('slider-image'));

function WMS_register() {  
    $args = array(  
        'label' => __(CPT_NAME),  
        'singular_label' => __(CPT_SINGLE),  
        'public' => true,  
        'show_ui' => true,  
        'capability_type' => 'post',  
        'hierarchical' => false,  
        'rewrite' => true,  
        'supports' => array('title', 'thumbnail')  
       );  
   
    register_post_type(CPT_TYPE , $args );  
}  
 
add_action('init', 'WMS_register');

add_action("admin_init", "WMS_shortcode_admin_init");
add_action("admin_init", "WMS_admin_init");
add_action('save_post', 'save_large_heading');
 
    function WMS_admin_init(){
        add_meta_box("slideInfo-meta", "Layer Options", "meta_options", "slider-image", "normal", "high");
    }
     
	function WMS_shortcode_admin_init(){
        add_meta_box("shortcodeInfo-meta", "Shortcode", "WMS_slider_shortcode", "slider-image", "side", "high");
    }
     
    function WMS_slider_shortcode(){
?>
<label>Text For Large Heading :<br/><br/> </label><input name="large_heading" value="[MS_slider]" /><?php
    }
	
	function meta_options(){
        global $post;
        $custom = get_post_custom($post->ID);
        $large_heading = $custom["large_heading"][0];
		$slide_Description = $custom["slide_Description"][0];
		$slide_link = $custom["slide_link"][0];
		$link_text = $custom["link_text"][0];
		$link_target = $custom["link_target"][0];
?>
<label>Text For Large Heading :<br/><br/> </label><input name="large_heading" value="<?php echo $large_heading; ?>" /><br/><br/>
<label>Description :<br/><br/> </label><input name="slide_Description" value="<?php echo $slide_Description; ?>" /><br/><br/>
<label>Link :<br/><br/> </label><input name="slide_link" value="<?php echo $slide_link; ?>" /><br/><br/>
<label>Link Text : <br/><br/></label><input name="link_text" value="<?php echo $link_text; ?>" /><br/><br/>
<label>Link Target : <br/><br/></label><input name="link_target" value="<?php echo $link_target; ?>" /><br/> (example: _blank)
<?php
    }
 
 
function save_large_heading(){
    global $post;
	$sanitize_large_heading = sanitize_text_field( $_POST['large_heading'] );
	$sanitize_slide_Description = sanitize_text_field( $_POST['slide_Description'] );
	$sanitize_slide_link = sanitize_text_field( $_POST['slide_link'] );
	$sanitize_link_text = sanitize_text_field( $_POST['link_text'] );
	$sanitize_link_target = sanitize_text_field( $_POST['link_target'] );
    update_post_meta($post->ID, "large_heading", $sanitize_large_heading);
	update_post_meta($post->ID, "slide_Description", $sanitize_slide_Description);
	update_post_meta($post->ID, "slide_link", $sanitize_slide_link);
	update_post_meta($post->ID, "link_text", $sanitize_link_text);
	update_post_meta($post->ID, "link_target", $sanitize_link_target);
}
	
