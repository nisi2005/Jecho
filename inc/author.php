<?php

/**
 * About author for single
 *
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 * @since 1.0
 */
?>
<div class="author mt-3 clearfix">
	<div class="meta float-md-left">
		<?php echo get_avatar( get_the_author_meta( 'user_email' ) ,56 ); ?>
		<p class="name"><a href="https://www.jecho.cn/author/Jecho/" title="由雨沐晨枫发布" rel="author"><?php echo get_the_author_meta('nickname'); ?></a></p>
		<p class="motto mb-0"><?php if (get_the_author_meta('description')) { echo strip_tags(get_the_author_meta('description'));} else {_e('The person is so lazy that he left nothing.','dobby');} ?></p>
	</div>
	<div class="share float-md-right text-center">
		<?php if (dobby_option('donate_status')) { ?>
		<a href="javascript:;" id="donate" class="btn btn-donate mr-3" role="button"><i class="dobby v3-donate"></i> <?php _e('Donate','dobby')?></a>
		<?php } ?>
		<a href="javascript:;" id="thumbs" data-action="love" data-id="<?php the_ID(); ?>" role="button" class="btn btn-thumbs <?php if(isset($_COOKIE['love_'.$post->ID])) echo 'done';?>" ><i class="dobby v3-thumbs"></i><span class="ml-1"><?php _e('Thumbs','dobby')?></span></a>
	</div>
</div>
<nav class="dobby-content-navigation mt-3 text-center clearfix">
	<?php $prev_post = get_previous_post(TRUE); ?>
			<?php if(!empty($prev_post)){?>
			<a href="<?php echo get_permalink($prev_post->ID);?>" class="text-dark" rel="prev">
			<span class="nav-span previous d-inline-block">
				<span class="post-nav d-block">&lt; 上一篇</span>
				<span class="d-none d-md-block"><?php echo $prev_post->post_title;?></span>
			</span>
			<?php }else{ ?>
			<a href="javascript:void(0)" class="text-dark" rel="prev">
			<span class="nav-span previous d-inline-block">
				<span class="post-nav d-block">没有了</span>
				<span class="d-none d-md-block">已经是最后的文章</span>
			</span>
			</a>
		  <?php } ?>
		  </a>
	<?php $next_post = get_next_post(TRUE); ?>
			<?php if(!empty($next_post)){?>
			<a href="<?php echo get_permalink($next_post->ID); ?>" class="text-dark" rel="next">
			<span class="nav-span d-inline-block">
				<span class="post-nav d-block">下一篇 &gt;</span>
				<span class="d-none d-md-block"><?php echo $next_post->post_title;?></span>
			</span>
			<?php }else{ ?>
			<a href="javascript:void(0)" class="text-dark" rel="next">
			<span class="nav-span d-inline-block">
				<span class="post-nav d-block">没有了</span>
				<span class="d-none d-md-block">已经是最后的文章</span>
			</span>
			</a>
		  <?php } ?>
		  </a>
</nav>