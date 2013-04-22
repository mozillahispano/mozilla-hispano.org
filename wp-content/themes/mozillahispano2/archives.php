<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
/*
Template Name: Archives
*/
?>

<?php get_header(); ?>

<div id="contenido" class="widecolumn">

<?php get_search_form(); ?>

<h2>Archives by Month:</h2>
	<ul>
		<?php wp_get_archives('type=monthly'); ?>
	</ul>

<h2>Archives by Subject:</h2>
	<ul>
		 <?php wp_list_categories(); ?>
	</ul>
	
<?php get_sidebar(); ?>

</div>



<?php get_footer(); ?>
