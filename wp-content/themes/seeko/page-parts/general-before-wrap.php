<?php
/**
 * Before content wrap
 * Used in all templates
 */
?>

<section <?php svq_main_section_class(); ?>>

	<?php
	/**
	 * Before main content - action
	 */
	do_action( 'svq_before_main_row' );
	?>

	<div <?php svq_main_row_class(); ?>>

		<?php
		/**
		 * Before main content - action
		 */
		do_action( 'svq_before_content' );
		?>

		<div <?php svq_main_col_class(); ?>>

			<?php
			/**
			 * Before main content - action
			 */
			do_action( 'svq_before_main_content' );
