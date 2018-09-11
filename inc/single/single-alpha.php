<?php

/**
 * The template for single alpha
 *
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 * @since 1.0
 */
?>
<main class="main alpha-content bg-light">
	<?php if (have_posts()) : the_post(); update_post_caches($posts);$shareData = array( 'url' => get_permalink(), 'title' => $post->post_title, 'excerpt' => get_the_excerpt(), 'img' => dobby_thumbnail_url(), ); wp_localize_script( 'share', 'sr', $shareData );?>
	    <div class="container">
			<div class="row">
			  <div class="col-md-12 d-none d-sm-block pt-1"></div>
				<div class="col-md-12 pb-4">
					<div class="post-bar">
						<div class="bg-thumbnail" style="background-image:url(<?php echo dobby_thumbnail_url(); ?>)"></div>
						    <div class="meta text-center text-white">
							    <h1><?php the_title(); ?></h1>
							        <div class="about pt-2 pt-md-3">
								      <span class="d-inline-block"><i class="dobby v3-activity"></i> <?php echo get_the_date(); ?></span>
								      <span class="d-none d-md-inline-block"><i class="dobby v3-interactive"></i> <?php comments_number('0', '1', '%'); ?> <?php _e('Comments' , 'dobby'); ?></span>
								      <span class="d-inline-block"><i class="dobby v3-browse"></i> <?php echo dobby_get_post_views(); ?> <?php _e('Views' , 'dobby'); ?></span>
								      <span class="d-inline-block"><i class="dobby v3-praise"></i> <?php if( get_post_meta($post->ID,'love',true) ){ echo num2tring(get_post_meta($post->ID,'love',true)); } else { echo '0'; }?> <?php _e('Thumb' , 'dobby'); ?></span>
							        </div>
							        <div class="share-group pt-3 pt-md-4">
					 					<a href="javascript:;" class="plain twitter" onclick="share('qq');" rel="nofollow">
											<div class="wrap">
												<i class="dobby v3-qq" title="分享到QQ"></i>
											</div>
										</a>
										<a href="javascript:;" class="plain weibo" onclick="share('weibo');" rel="nofollow">
											<div class="wrap">
												<i class="dobby v3-weibo" title="分享到微博"></i>
											</div>
										</a>
										<a href="javascript:;" class="plain qzone style-plain" onclick="share('qzone');" rel="nofollow">
											<div class="wrap">
												<i class="dobby v3-qzone" title="分享到QQ空间"></i>
											</div>
										</a>
										<a href="javascript:;" class="plain twitter style-plain" onclick="share('twitter');" rel="nofollow">
											<div class="wrap">
												<i class="dobby v3-twitter" title="分享到twitter"></i>
											</div>
										</a>
										<a href="javascript:;" class="plain qrcode-box style-plain" rel="nofollow">
											<div class="wrap">
											    <i class="dobby v3-weixin" title="分享到微信"></i>
											</div>
											<div class="qrcode-plane"><div class="qrcode" data-url="<?php the_permalink() ?>"></div><p>分享文章到朋友圈</p></div>
										</a>
									</div>
							</div>
					</div>
				    <div class="pt-3">
					  <div class="row">
				        <article class="col-lg-8">
					      <div class="article">			  
						  <div class="main wmcontent watermarked" id="article-post">
						  <div class="watermark" style="background-image: url(&quot;https://image.jecho.cn/shui.png&quot;);"></div><?php the_content(); ?></div>
					        <div class="copyright mt-3 clearfix">
						      <div class="tags float-left mt-3">
							  <span class="dobby v3-label_fill"></span>
							  <?php if ( get_the_tags() ) { the_tags('', ' ', ''); } else{ echo '<a>' . __( 'None' , 'dobby') . '</a>';  }?>
						      </div>
						      <div class="float-right mt-3">
							    <span id="copyright"><?php _e('© The copyright belongs to the author','dobby'); ?></span>
						      </div>
					        </div>
					      </div>
					      <?php require_once( get_template_directory() . '/inc/author.php'); ?>
					      <?php comments_template(); ?>
				        </article>
				        <aside class="aside-widget col-lg-4 d-none d-lg-block d-xl-block">
					    <div id="stickysingle">
						  <?php dynamic_sidebar('sidebar_single'); ?>
					    </div>
				        </aside>
			          </div>
			        </div>
			      </div>
		        </div>
		    </div>
	        <?php endif;?>
</main>
