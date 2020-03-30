<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Seeko
 * @since Seeko 1.0
 */
?>

        <?php
        /**
         * After page-wrapper part - action
         */
        do_action( 'svq_after_main' );
        ?>

		<?php
		/**
		 * After footer hook
		 *
		 */
		do_action( 'svq_footer' );
		?>

    </div><!-- #page-wrapper -->

	<!-- Analytics -->
	<?php echo wp_kses_post( svq_option( 'quick_code', '' ) ) ; ?>

	<?php wp_footer(); ?>

</body>
</html>
