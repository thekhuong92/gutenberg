<?php
/**
 * Bootstraping the Gutenberg experiments page.
 *
 * @package gutenberg
 */

/**
 * The main entry point for the Gutenberg experiments page.
 *
 * @since 6.3.0
 */
function the_gutenberg_experiments() {
	?>
	<div
		id="experiments-editor"
		class="wrap"
	>
	<h1><?php echo __( 'Experimental settings', 'gutenberg' ); ?></h1>
	<?php settings_errors(); ?>
	<form method="post" action="options.php">
		<?php settings_fields( 'gutenberg-experiments' ); ?>
		<?php do_settings_sections( 'gutenberg-experiments' ); ?>
		<?php submit_button(); ?>
	</form>
	</div>
	<?php
}

/**
 * Set up the experiments settings.
 *
 * @since 6.3.0
 */
function gutenberg_initialize_experiments_settings() {
	add_settings_section(
		'gutenberg_experiments_section',
		// The empty string ensures the render function won't output a h2.
		'',
		'gutenberg_display_experiment_section',
		'gutenberg-experiments'
	);
	add_settings_field(
		'gutenberg-widget-experiments',
		__( 'Widgets', 'gutenberg' ),
		'gutenberg_display_experiment_field',
		'gutenberg-experiments',
		'gutenberg_experiments_section',
		array(
			'label' => __( 'Enable Widgets screen and Legacy Widgets block', 'gutenberg' ),
			'id'    => 'gutenberg-widget-experiments',
		)
	);
	add_settings_field(
		'gutenberg-block-directory',
		__( 'Block Directory', 'gutenberg' ),
		'gutenberg_display_experiment_field',
		'gutenberg-experiments',
		'gutenberg_experiments_section',
		array(
			'label' => __( 'Enable block directory search', 'gutenberg' ),
			'id'    => 'gutenberg-block-directory',
		)
	);
	add_settings_field(
		'gutenberg-full-site-editing',
		__( 'Full Site Editing', 'gutenberg' ),
		'gutenberg_display_experiment_field',
		'gutenberg-experiments',
		'gutenberg_experiments_section',
		array(
			'label' => __( 'Enable Full Site Editing (Warning: this will replace your theme and cause potentially irreversible changes to your site. We recommend using this only in a development environment.)', 'gutenberg' ),
			'id'    => 'gutenberg-full-site-editing',
		)
	);
	add_settings_field(
		'gutenberg-full-site-editing-demo',
		__( 'Full Site Editing Demo Templates', 'gutenberg' ),
		'gutenberg_display_experiment_field',
		'gutenberg-experiments',
		'gutenberg_experiments_section',
		array(
			'label' => __( 'Enable Full Site Editing demo templates', 'gutenberg' ),
			'id'    => 'gutenberg-full-site-editing-demo',
		)
	);
	register_setting(
		'gutenberg-experiments',
		'gutenberg-experiments'
	);
}

add_action( 'admin_init', 'gutenberg_initialize_experiments_settings' );

/**
 * Display a checkbox field for a Gutenberg experiment.
 *
 * @since 6.3.0
 *
 * @param array $args ( $label, $id ).
 */
function gutenberg_display_experiment_field( $args ) {
	$options = get_option( 'gutenberg-experiments' );
	$value   = isset( $options[ $args['id'] ] ) ? 1 : 0;
	?>
		<label for="<?php echo $args['id']; ?>">
			<input type="checkbox" name="<?php echo 'gutenberg-experiments[' . $args['id'] . ']'; ?>" id="<?php echo $args['id']; ?>" value="1" <?php checked( 1, $value ); ?> />
			<?php echo $args['label']; ?>
		</label>
	<?php
}

/**
 * Display the experiments section.
 *
 * @since 6.3.0
 */
function gutenberg_display_experiment_section() {
	?>
	<p><?php echo __( "The block editor includes experimental features that are useable while they're in development. Select the ones you'd like to enable. These features are likely to change, so avoid using them in production.", 'gutenberg' ); ?></p>

	<?php
}

/**
 * Extends default editor settings with experiments settings.
 *
 * @param array $settings Default editor settings.
 *
 * @return array Filtered editor settings.
 */
function gutenberg_experiments_editor_settings( $settings ) {
	$experiments_settings = array(
		'__experimentalEnableLegacyWidgetBlock'   => gutenberg_is_experiment_enabled( 'gutenberg-widget-experiments' ),
		'__experimentalBlockDirectory'            => gutenberg_is_experiment_enabled( 'gutenberg-block-directory' ),
		'__experimentalEnableFullSiteEditing'     => gutenberg_is_experiment_enabled( 'gutenberg-full-site-editing' ),
		'__experimentalEnableFullSiteEditingDemo' => gutenberg_is_experiment_enabled( 'gutenberg-full-site-editing-demo' ),
	);

	$gradient_presets = current( (array) get_theme_support( 'editor-gradient-presets' ) );
	if ( false !== $gradient_presets ) {
		$experiments_settings['gradients'] = $gradient_presets;
	}

	$experiments_settings['disableCustomGradients'] = get_theme_support( 'disable-custom-gradients' );

	return array_merge( $settings, $experiments_settings );
}
add_filter( 'block_editor_settings', 'gutenberg_experiments_editor_settings' );
