<?php
/**
 * Template Name: Blank Page
 *
 * Description: Show a page without header/footer
 *
 *
 * @package WordPress
 * @subpackage BuddyApp
 * @author SeventhQueen <themesupport@seventhqueen.com>
 * @since BuddyApp 1.0
 */

/* remove header */
remove_action( 'svq_header', 'svq_show_header', 12 );

get_header();
?>

    <?php
    if ( have_posts() ) :
        // Start the Loop.
        while ( have_posts() ) : the_post();
        ?>

        <?php the_content(); ?>

        <?php
        endwhile;

    endif;
    ?>

<?php
get_footer();
