<?php
/**
 * WDS Plugin Police Dynamic_form
 *
 * @since   0.1.0
 * @package WDS Plugin Police
 */

/**
 * WDS Plugin Police Dynamic_form.
 *
 * @since 0.1.0
 */
class WDSPP_Dynamic_form {
	/**
	 * Parent plugin class
	 *
	 * @var   WDS_Plugin_Police
	 * @since 0.1.0
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 *
	 * @since  0.1.0
	 *
	 * @param  WDS_Plugin_Police $plugin Main plugin object.
	 *
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function hooks() {
		add_filter( 'auto_update_plugin', array( $this, 'auto_update_stored_plugins' ), 10, 2 );
	}

	/**
	 * Create the dynamic stuff to be returned from the admin-ajax request.
	 *
	 * @since 0.1.0
	 */
	public function dynamic_form() {
		$this->update( $_POST['slug'] );
		$this->lock( $_POST['slug'] );
		$this->get_form( $_POST['slug'] );
		$this->get_comments( $_POST['slug'] );
		die();
	}

	/**
	 * Echo the lock icon.
	 *
	 * @since 0.1.0
	 */
	public function lock( $slug ) {
		if ( $this->lock_status( $slug ) ) {
			echo '<a href="javascript:void(0)" id=plugin_lock_update_'
			     . $slug
			     . ' aria-label="'
			     . sprintf( esc_attr__( 'Unlock the %s plugin', 'admin-plugin-notes' ), $slug )
			     . '" title="'
			     . sprintf( esc_attr__( 'Unlock the %s plugin', 'admin-plugin-notes' ), $slug )
			     . '">';
			echo '<i class="fa fa-lock fa-lg green" aria-hidden="true"></i>';
		} else {
			echo '<a href="javascript:void(0)" id=plugin_lock_update_'
			     . $slug
			     . ' aria-label="'
			     . sprintf( esc_attr__( 'Lock the %s plugin', 'admin-plugin-notes' ), $slug )
			     . ' plugin" title="'
			     . sprintf( esc_attr__( 'Lock the %s plugin', 'admin-plugin-notes' ), $slug )
			     . '">';
			echo '<i class="fa fa-lock fa-lg grey" aria-hidden="true"></i>';
		}
		echo '</a>';
	}

	/**
	 * Returns lock status of particular slug.
	 *
	 * @param $slug string plugin slug name.
	 *
	 * @return bool
	 */
	public function lock_status( $slug ) {
		$lock_plugins = get_option( 'wds_plugin_lock_updates' );
		if ( is_array( $lock_plugins ) && in_array( $slug, $lock_plugins ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Update the lock stats option.
	 *
	 * @since 0.1.0
	 */
	public function toggle_lock() {
		$lock_plugins = get_option( 'wds_plugin_lock_updates' );
		// If this plugin is in the list, remove it.
		if ( in_array( $_POST['slug'], $lock_plugins ) ) {
			$new_lock_plugins = array();
			foreach ( $lock_plugins as $plugin ) {
				if ( $_POST['slug'] != $plugin ) {
					$new_lock_plugins[] = $plugin;
				}
			}

			if ( isset( $new_lock_plugins ) ) {
				update_option( 'wds_plugin_lock_updates', $new_lock_plugins );
			}

			// If this plugin isn't in the array add it.
		} else {
			$lock_plugins[] = $_POST['slug'];
			update_option( 'wds_plugin_lock_updates', $lock_plugins );
		}
	}

	/**
	 * Set status for auto-updating.
	 *
	 * @since 0.1.0
	 *
	 * @param $slug
	 */
	public function update( $slug ) {
		if ( $this->update_status( $slug ) ) {
			echo '<a href="javascript:void(0)" id=plugin_auto_update_'
			     . $slug
			     . ' aria-label="'
			     . sprintf( esc_attr__( 'Turn off auto updates for the %s plugin', 'admin-plugin-notes' ), $slug )
			     . '" title="'
			     . sprintf( esc_attr__( 'Turn off auto updates for the %s plugin', 'admin-plugin-notes' ), $slug )
			     . '">';
			echo '<i class="fa fa-refresh fa-lg green"></i>';
		} else {
			echo '<a href="javascript:void(0)" id=plugin_auto_update_'
			     . $slug
			     . ' aria-label="'
			     . sprintf( esc_attr__( 'Turn on auto updates for the %s plugin', 'admin-plugin-notes' ), $slug )
			     . '" title="'
			     . sprintf( esc_attr__( 'Turn on auto updates for the %s plugin', 'admin-plugin-notes' ), $slug )
			     . '">';
			echo '<i class="fa fa-refresh fa-lg grey"></i>';
		}
		echo '</a>';
	}

	/**
	 * Return the update status of the plugin passed in.
	 *
	 * @since 0.1.0
	 *
	 * @param $slug
	 *
	 * @return bool
	 */
	private function update_status( $slug ) {
		$update_plugins = get_option( 'wds_plugin_updates_auto_updates' );
		if ( is_array( $update_plugins ) && in_array( $slug, $update_plugins ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @param $slug
	 *
	 * Create the form for the slug.
	 */
	public function get_form( $slug ) {
		?><br/>
		<a href="javascript:void(0);" id="police_comment_link_<?php echo esc_attr( $slug ); ?>">
		<?php esc_html_e( 'Add a Note', 'plugin-admin-notes' ); ?>
		</a>
		<div style="display: none;" id="police_comment_div_<?php echo esc_attr( $slug ); ?>">
		<input type="hidden" name="slug" value="<?php echo esc_attr( $slug ); ?>">
		<input type="text" class="plugin_notes_<?php echo esc_attr( $slug ); ?> plugin-admin-notes-note" name="note" id="police_comment_<?php echo esc_attr( $slug ); ?>">
		<input type="button" value="<?php esc_attr_e( 'Add a Note', 'plugin-admin-notes' ); ?>" id="police_comment_submit_<?php echo esc_attr( $slug ); ?>">
		<input type="button" value="<?php esc_attr_e( 'Cancel', 'plugin-admin-notes' ); ?>" id="police_cancel_<?php echo esc_attr( $slug ); ?>">
		</div>
	<?php }

	/**
	 * Get the existing comments.
	 *
	 * @since 0.1.0
	 *
	 * @param $slug
	 */
	public function get_comments( $slug ) {
		$args    = array(
			'post_type'  => 'wdspp-plugin-police',
			'meta_query' => array(
				array(
					'key'     => 'pp_slug',
					'value'   => $slug,
					'compare' => 'IN',
				),
			),
		);
		$results = new WP_Query( $args );
		if ( $results->have_posts() ) {
			echo '<ul>';
			while ( $results->have_posts() ) {
				$results->the_post(); ?>
				<li style="font-size:smaller;">
				<?php // Outputs our note, by whom, and the formatted date.
				printf( __( '%s by <strong>%s</strong> on %s', 'maintainn-tools' ),
					get_the_content(),
					get_the_author(),
					get_the_time( 'F j, Y g:i a' )
				); ?>
				</li>
			<?php } // End while().
			echo '</ul>';
		} // End if().
		// Reset dat data.
		wp_reset_postdata();
	}

	/**
	 * Save incoming comment.
	 *
	 * @since 0.1.0
	 */
	public function save_comment() {
		$args = array(
			'post_content' => $_POST['comment'],
			'post_status'  => 'publish',
			'post_type'    => 'wdspp-plugin-police',
		);
		$id   = wp_insert_post( $args );
		update_post_meta( $id, 'pp_slug', $_POST['slug'] );
	}

	/**
	 * Toggle the udpate status option.
	 *
	 * @since 0.1.0
	 */
	public function toggle_updates() {
		$update_plugins = get_option( 'wds_plugin_updates_auto_updates' );

		// If this plugin is in the list, remove it.
		if ( in_array( $_POST['slug'], $update_plugins ) ) {
			$new_update_plugins = array();

			foreach ( $update_plugins as $plugin ) {
				if ( $_POST['slug'] != $plugin ) {
					$new_update_plugins[] = $plugin;
				}
			}

			if ( isset( $new_update_plugins ) ) {
				update_option( 'wds_plugin_updates_auto_updates', $new_update_plugins );
			}

			// If this plugin isn't in the array add it.
		} else {
			$update_plugins[] = $_POST['slug'];
			update_option( 'wds_plugin_updates_auto_updates', $update_plugins );
		}
	}

	/**
	 * Sets the auto-update for the plugins that are in the updates array.
	 *
	 * @since 0.1.0
	 */
	public function auto_update_stored_plugins( $update, $item ) {
		$plugins = get_option( 'wds_plugin_updates_auto_updates' );
		if ( in_array( $item->slug, $plugins ) ) {
			return true; // Always update plugins in this array
		} else {
			return $update; // Else, use the normal API response to decide whether to update or not
		}
	}
}
