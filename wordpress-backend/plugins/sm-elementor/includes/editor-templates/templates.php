<?php
namespace SM_Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<script type="text/template" id="tmpl-elementor-template-library-save-preset">
	<div class="elementor-template-library-blank-icon">
		<i class="eicon-library-save" aria-hidden="true"></i>
		<span class="elementor-screen-only"><?php echo __( 'Save', 'elementor' ); ?></span>
	</div>
	<div class="elementor-template-library-blank-title">{{{ title }}}</div>
	<div class="elementor-template-library-blank-message">{{{ description }}}</div>
	<form id="elementor-template-library-save-preset-form">
		<input type="hidden" name="post_id" value="<?php echo get_the_ID(); ?>">
		<input id="elementor-template-library-save-preset-name" name="title" placeholder="<?php echo esc_attr__( 'Enter Template Name', 'elementor' ); ?>" required>
		<button id="elementor-template-library-save-preset-submit" class="elementor-button elementor-button-success">
			<span class="elementor-state-icon">
				<i class="fa fa-spin fa-circle-o-notch" aria-hidden="true"></i>
			</span>
			<?php echo __( 'Save', 'elementor' ); ?>
		</button>
	</form>
	<div class="elementor-template-library-blank-footer">
		<?php echo __( 'Want to learn more about the Elementor library?', 'elementor' ); ?>
		<a class="elementor-template-library-blank-footer-link" href="https://go.elementor.com/docs-library/" target="_blank"><?php echo __( 'Click here', 'elementor' ); ?></a>
	</div>
</script>
