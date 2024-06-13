<!--<div class="lqd-pf-column <?php $this->get_grid_class() ?> col-sm-6 col-xs-12 masonry-item <?php $this->entry_term_classes() ?>"> -->

	<article <?php echo liquid_helper()->html_attributes( $attributes ); ?>>
		<div class="lqd-pf-item" style="height: 285px; background: #eee; border: 1px solid; overflow: hidden;" >

			<div class="lqd-pf-img mb-16 relative rounded-6 overflow-hidden">
				<figure>
					<?php $this->entry_thumbnail(); ?>
				</figure>
				<span class="lqd-pf-overlay-bg flex items-center justify-center absolute top-0 start-0">
					<i class="lqd-icn-ess icon-md-arrow-forward"></i>
				</span>
			</div>

			<div class="lqd-pf-details">
				<?php the_title( '<h2 class="mt-0 mb-4 h5">', '</h2>'  ) ?>
				<?php $this->entry_cats() ?>

			</div>

			<?php $this->get_overlay_button(); ?>

		</div>
	</article>

<!--</div>-->