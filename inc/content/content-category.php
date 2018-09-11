<?php

/**
 * The template for content category
 *
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 * @since 1.0
 */
?>
<article class="col-md-6 col-xl-4 category-item">
	<div class="alpha-item globe-block">
		<?php dobby_thumbnail(); ?>
		<div class="text p-3">
			<h3 class="text-center"><a href="<?php the_permalink(); ?>" class="text-dark"><?php the_title(); ?></a></h3>
			<p class="text-secondary"><?php echo wp_trim_words(get_the_excerpt(),150); ?></p>
			<ul class="stuff text-secondary clearfix">
				<span class="author"><img class="avatar" src="https://image.jecho.cn/2018030821000628.jpg" data-original="https://image.jecho.cn/2018030821000628.jpg" alt="<?php echo get_the_author_meta('nickname'); ?>" style="display: inline-block;"><a href="https://www.jecho.cn/author/Jecho/" class="text-secondary">雨沐晨枫</a></span>
				<li><i class="dobby v3-browse"></i> <?php echo dobby_get_post_views(); ?></li>
				<li><i class="dobby v3-like"></i> <?php if( get_post_meta($post->ID,'love',true) ){ echo num2tring(get_post_meta($post->ID,'love',true)); } else { echo '0'; }?></li>
				<li class="float-right"><a href="<?php the_permalink(); ?>" class="text-secondary"><?php _e('Read More', 'dobby'); ?> <i class="dobby v3-arrow"></i></a></li>
			</ul>
		</div>
	</div>
</article>