<?php

/**
 * Displays a person's thumbnail image.
 *
 * @author Jo Dickson
 * @since 1.0.0
 * @param $post object | Person post object
 * @param $css_classes str | Additional classes to add to the thumbnail wrapper
 * @return Mixed | thumbnail HTML or void
 **/
if ( ! function_exists( 'ucfwp_get_person_thumbnail' ) ) {
	function ucfwp_get_person_thumbnail( $post, $css_classes='' ) {
		if ( ! $post->post_type == 'person' ) { return; }
		$thumbnail = get_the_post_thumbnail_url( $post ) ?: ucfwp_get_theme_mod_or_default( 'person_thumbnail' );
		// Account for attachment ID being returned by get_theme_mod_or_default():
		if ( is_numeric( $thumbnail ) ) {
			$thumbnail = wp_get_attachment_url( $thumbnail );
		}
		ob_start();
		if ( $thumbnail ):
	?>
		<div class="media-background-container person-photo mx-auto <?php echo $css_classes; ?>">
			<img src="<?php echo $thumbnail; ?>" alt="" title="<?php echo $post->post_title; ?>" class="media-background object-fit-cover">
		</div>
	<?php
		endif;
		return ob_get_clean();
	}
}


/**
 * Returns a person's name with title prefix and suffix applied.
 *
 * @author Jo Dickson
 * @since 1.0.0
 * @param $post object | Person post object
 * @return Mixed | person's formatted name or void
 **/
if ( ! function_exists( 'ucfwp_get_person_name' ) ) {
	function ucfwp_get_person_name( $post ) {
		if ( ! $post->post_type == 'person' ) { return; }
		$prefix = get_field( 'person_title_prefix', $post->ID ) ?: '';
		$suffix = get_field( 'person_title_suffix', $post->ID ) ?: '';
		if ( $prefix ) {
			$prefix = trim( $prefix ) . ' ';
		}
		if ( $suffix && substr( $suffix, 0, 1 ) !== ',' ) {
			$suffix = ' ' . trim( $suffix );
		}
		return wptexturize( $prefix . $post->post_title . $suffix );
	}
}


/**
 * Add custom people list layout for UCF Post List shortcode
 **/
if ( ! function_exists( 'ucfwp_post_list_display_people_before' ) ) {
	function ucfwp_post_list_display_people_before( $content, $items, $atts ) {
		ob_start();
	?>
	<div class="ucf-post-list ucfwp-post-list-people">
	<?php
		return ob_get_clean();
	}
}

add_filter( 'ucf_post_list_display_people_before', 'ucfwp_post_list_display_people_before', 10, 3 );


if ( ! function_exists( 'ucfwp_post_list_display_people_title' ) ) {
	function ucfwp_post_list_display_people_title( $content, $items, $atts ) {
		$formatted_title = '';
		if ( $atts['list_title'] ) {
			$formatted_title = '<h2 class="ucf-post-list-title">' . $atts['list_title'] . '</h2>';
		}
		return $formatted_title;
	}
}

add_filter( 'ucf_post_list_display_people_title', 'ucfwp_post_list_display_people_title', 10, 3 );


/**
 * Add custom people list layout for UCF Post List shortcode
 **/
if ( ! function_exists( 'ucfwp_post_list_display_people' ) ) {
	function ucfwp_post_list_display_people( $content, $items, $atts ) {
		if ( ! is_array( $items ) && $items !== false ) { $items = array( $items ); }
		ob_start();
	?>
		<?php if ( $items ): ?>
		<ul class="list-unstyled row ucf-post-list-items">
			<?php foreach ( $items as $item ): ?>
			<?php $is_content_empty = ucfwp_is_content_empty( $item->post_content ); ?>
			<li class="col-6 col-sm-4 col-md-3 col-xl-2 mt-3 mb-2 ucf-post-list-item">
				<?php if ( ! $is_content_empty ) { ?>
				<a class="person-link" href="<?php echo get_permalink( $item->ID ); ?>">
				<?php } ?>
					<?php echo ucfwp_get_person_thumbnail( $item ); ?>
					<h3 class="mt-2 mb-1 person-name"><?php echo ucfwp_get_person_name( $item ); ?></h3>
					<?php if ( $job_title = get_field( 'person_jobtitle', $item->ID ) ): ?>
					<div class="font-italic person-job-title">
						<?php echo $job_title; ?>
					</div>
					<?php endif; ?>
					<?php if ( $email = get_field( 'person_email', $item->ID ) ): ?>
					<div class="person-email">
						<?php if ( $is_content_empty ) { ?>
						<a href="mailto:<?php echo $email; ?>">
						<?php } ?>
						<?php echo $email; ?>
						<?php if ( $is_content_empty ) { ?>
						</a>
						<?php } ?>
					</div>
					<?php endif; ?>
					<?php if ( $phone = get_field( 'person_phone', $item->ID ) ): ?>
					<div class="person-job-title">
						<?php echo $phone; ?>
					</div>
					<?php endif; ?>
				<?php if ( ! $is_content_empty ) { ?>
				</a>
				<?php } ?>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php else: ?>
		<div class="ucf-post-list-error mb-4">No results found.</div>
		<?php endif; ?>
	<?php
		return ob_get_clean();
	}
}

add_filter( 'ucf_post_list_display_people', 'ucfwp_post_list_display_people', 10, 3 );


if ( ! function_exists( 'ucfwp_post_list_display_people_after' ) ) {
	function ucfwp_post_list_display_people_after( $content, $items, $atts ) {
		ob_start();
	?>
	</div>
	<?php
		return ob_get_clean();
	}
}

add_filter( 'ucf_post_list_display_people_after', 'ucfwp_post_list_display_people_after', 10, 3 );
?>
