<?php
/**
 * Changelog
 */

$bloog_lite = wp_get_theme( 'the100' );
?>
<div class="featured-section changelog">
<?php
	WP_Filesystem();
	global $wp_filesystem;
	$eightstore_lite_changelog       = $wp_filesystem->get_contents( get_template_directory() . '/readme.txt' );
	$changelog_start = strpos($eightstore_lite_changelog,'== Changelog ==');
	$eightstore_lite_changelog_before = substr($eightstore_lite_changelog,0,($changelog_start+15));
	$eightstore_lite_changelog = str_replace($eightstore_lite_changelog_before,'',$eightstore_lite_changelog);
	$eightstore_lite_changelog = str_replace('**','<br/>**',$eightstore_lite_changelog);
	$eightstore_lite_changelog = str_replace('= 1.0','<br/><br/>= 1.0',$eightstore_lite_changelog);
	echo $eightstore_lite_changelog;
	echo '<hr />';
	?>
</div>