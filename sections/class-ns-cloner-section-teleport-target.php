<?php
/**
 * Create Teleport Target Section Class
 *
 * @package NS_Cloner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class NS_Cloner_Section_Teleport_Target
 *
 * Adds and validates options for url and title of the new subsite to be created.
 */
class NS_Cloner_Section_Teleport_Target extends NS_Cloner_Section {

	/**
	 * Mode ids that this section should be visible and active for.
	 *
	 * @var array
	 */
	public $modes_supported = [ 'clone_teleport' ];

	/**
	 * DOM id for section box.
	 *
	 * @var string
	 */
	public $id = 'teleport_target';

	/**
	 * Priority relative to other section boxes in UI.
	 *
	 * @var int
	 */
	public $ui_priority = 325;

	/**
	 * Output content for section settings box on admin page.
	 */
	public function render() {
		$this->open_section_box( __( 'Remote Site Details', 'ns-cloner' ) );
		?>
		<div class="teleport-site-waiting">
			<h5><?php esc_html_e( 'Waiting for connection to remote site (enter connection info above).', 'ns-cloner' ); ?></h5>
		</div>
		<div class="teleport-site-loading">
			<h5><span class="ns-cloner-validating-spinner"></span><?php esc_html_e( 'Loading info from remote site...', 'ns-cloner' ); ?>
		</div>
		<div class="teleport-site-connected">
			<h5><label for="teleport_target_title"><?php esc_html_e( 'Give the target site a title', 'ns-cloner' ); ?></label></h5>
			<div class="ns-cloner-input-group">
				<input type="text" name="teleport_target_title" placeholder="<?php esc_attr_e( 'New Site Title', 'ns-cloner' ); ?>" />
			</div>
			<h5><label for="target_name"><?php esc_html_e( 'Give the target site a URL', 'ns-cloner' ); ?></label></h5>
			<div class="ns-cloner-input-group">
				<label class="before"></label>
				<input type="text" name="teleport_target_name" />
				<label class="after"></label>
			</div>
		</div>
		<?php
		$this->close_section_box();
	}

	/**
	 * Check ns_cloner_request() and any validation error messages to $this->errors.
	 */
	public function validate() {
		$teleport = ns_cloner()->get_addon( 'teleport' );
		// Only validate this section when cloning to a subsite.
		if ( $teleport->get_remote_data( 'is_multisite' ) && ! $teleport->is_full_network() ) {
			$name  = ns_cloner_request()->get( 'teleport_target_name' );
			$title = ns_cloner_request()->get( 'teleport_target_title' );
			// Use our customized local validation here. We're not going to make a whole extra
			// request to the remote site to check the blog name there, so this just makes sure
			// that the global requirements are met, and any specific subsite/page conflicts on
			// the remote site will be caught when the cloning starts and return an error then.
            $errors = ns_wp_validate_site( $name, $title );
            foreach ( $errors as $error ) {
                // Skip any local conflicts because those don't matter.
                if ( __( 'Sorry, that site already exists!', 'ns-cloner' ) === $error ) {
                    continue;
                }
                $this->errors[] = $error;
            }
		}
	}

}
