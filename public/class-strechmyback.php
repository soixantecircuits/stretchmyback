<?php
/**
 * Stretch_my_back
 *
 * @package   Stretch_my_back
 * @author    gabrielstuff <gabriel@soixantecircuits.fr>
 * @license   GPL-2.0+
 * @link      http://soixantecircuits.fr
 * @copyright 2014 gabrielstuff
 */

/**
 * Stretch_my_back class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-strechmyback-admin.php`
 *
 * @package Stretch_my_back
 * @author  gabrielstuff <gabriel@soixantecircuits.fr>
 */
class Stretch_my_back {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '0.0.1';

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'strechmyback';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'pre_post_update', array( $this, 'save_meta_boxes' ) );
		add_action( 'wp_head', array( $this, 'add_background_if_exist') );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		// @TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-backstretch', plugins_url( 'assets/js/jquery.backstretch.min.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( $this->plugin_slug . '-backstretch', 'jquery' ), self::VERSION );

	}

	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// @TODO: Define your action hook callback here
	}

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// @TODO: Define your filter hook callback here
	}

	/**
	* Registers a Meta Box on our Contact Custom Post Type, called 'Contact Details'
	*/
	public function register_meta_boxes() {
	    add_meta_box( 'fullscreen-image', __('Fullscreen Image', $this->plugin_slug), array( $this, 'output_meta_box' ), 'page', 'normal', 'high' );
	}

	/**
	* Output a Contact Details meta box
	*
	* @param WP_Post $post WordPress Post object
	*/
	public function output_meta_box( $post ) {
	 	// Output label and field
	 	$fullscreen_image_id = get_post_meta( $post->ID, '_fullscreen_image', true );?>
		<div class="image_full_screen_wrapper">
	 	<?php
		if($fullscreen_image_id){
	 		echo wp_get_attachment_image( $fullscreen_image_id, 'thumbnail', false, array(
	'class'	=> "fullscreen_image",
	'alt'   => trim(strip_tags( get_post_meta($fullscreen_image_id, '_wp_attachment_image_alt', true) )),
) );
	 		echo ('<div id="selectImage" class="fullOverlay">'.__('Replace the current image', $this->plugin_slug ).'</div>' );
	 	} else {
	 		echo ('<label for="fullscreen_image">' . __( 'No image selected, add one ?', $this->plugin_slug ) . '</label>' );
	 		echo ('<div id="selectImage">'.__('Choose an image', $this->plugin_slug ).'</div>' );
	 	}
    echo ('<input type="text" style="display:none;" name="fullscreen_image" id="fullscreen_image" value="'.esc_attr( $fullscreen_image_id ).'" />' );
    ?>
    </div>
    <?php
	}

	public function save_meta_boxes( $post_id ) {
		error_log('saving...');
    // Check this is the Contact Custom Post Type
    if ( 'page' != $_POST['post_type'] ) {
        return $post_id;
    }
		error_log('right type...');
    // Check the logged in user has permission to edit this post
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return $post_id;
    }
		error_log('right auth...');
    // OK to save meta data
    $fullscreen_image_id = sanitize_text_field( $_POST['fullscreen_image'] );
		error_log($fullscreen_image_id);
    update_post_meta( $post_id, '_fullscreen_image', $fullscreen_image_id );
	}

	public function add_background_if_exist(){
		$fullscreen_image_id = get_post_meta( get_the_ID(), '_fullscreen_image', true );
	 	if($fullscreen_image_id){
	 		$image_attributes = wp_get_attachment_image_src( $fullscreen_image_id, 'full' );?>
	 		<script type="text/javascript">
	 			var fullscreen_image = {};
	 			fullscreen_image.url = '<?php echo esc_attr($image_attributes[0]); ?>';
	 		</script>
	 		<?php
	 	} else {
	 		return false;
	 	}
	}

}
