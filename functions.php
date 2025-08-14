<?php

if ( ! class_exists( 'Kavkaz_Taxonomy_Meta' ) ) {

	class Kavkaz_Taxonomy_Meta {

		private $taxonomies = ['category', 'dil', 'erotik'];

		public function __construct() {
			add_action( 'admin_enqueue_scripts', [$this, 'load_media'] );
			add_action( 'admin_footer', [$this, 'add_script'] );

			foreach ( $this->taxonomies as $taxonomy ) {
				add_action( "{$taxonomy}_add_form_fields", [$this, 'add_fields'] );
				add_action( "created_{$taxonomy}", [$this, 'save_fields'] );
				add_action( "{$taxonomy}_edit_form_fields", [$this, 'edit_fields'] );
				add_action( "edited_{$taxonomy}", [$this, 'update_fields'] );
			}
		}

		public function add_fields( $taxonomy ) {
			?>
			<div class="form-field term-group">
				<label for="category-content"><?php _e('Content', 'kavkaz'); ?></label>
				<textarea id="category-content" name="category-content" rows="4"></textarea>
			</div>

			<div class="form-field term-group">
				<label for="category-image-id"><?php _e('Image', 'kavkaz'); ?></label>
				<input type="hidden" id="category-image-id" name="category-image-id" value="">
				<div id="category-image-wrapper"></div>
				<p>
					<button type="button" class="button ct_tax_media_button"><?php _e( 'Add Image', 'kavkaz' ); ?></button>
					<button type="button" class="button ct_tax_media_remove"><?php _e( 'Remove Image', 'kavkaz' ); ?></button>
				</p>
			</div>
			<?php
		}

		public function save_fields( $term_id ) {
			$fields = ['category-content', 'category-image-id'];
			foreach ( $fields as $field ) {
				if ( ! empty( $_POST[ $field ] ) ) {
					add_term_meta( $term_id, $field, wp_kses_post( $_POST[ $field ] ), true );
				}
			}
		}

		public function edit_fields( $term ) {
			?>
			<table class="form-table" role="presentation">
				<tr class="form-field">
					<th><label for="category-content"><?php _e('Content', 'kavkaz'); ?></label></th>
					<td>
						<?php
						wp_editor(
							get_term_meta( $term->term_id, 'category-content', true ),
							'category-content',
							[
								'media_buttons' => true,
								'textarea_name' => 'category-content',
								'textarea_rows' => 5,
								'quicktags' => true
							]
						);
						?>
					</td>
				</tr>

				<tr class="form-field">
					<th><label for="category-image-id"><?php _e('Image', 'kavkaz'); ?></label></th>
					<td>
						<input type="hidden" id="category-image-id" name="category-image-id" value="<?php echo esc_attr( get_term_meta( $term->term_id, 'category-image-id', true ) ); ?>">
						<div id="category-image-wrapper">
							<?php
							if ( $img_id = get_term_meta( $term->term_id, 'category-image-id', true ) ) {
								echo wp_get_attachment_image( $img_id, 'thumbnail' );
							}
							?>
						</div>
						<p>
							<button type="button" class="button ct_tax_media_button"><?php _e( 'Add Image', 'kavkaz' ); ?></button>
							<button type="button" class="button ct_tax_media_remove"><?php _e( 'Remove Image', 'kavkaz' ); ?></button>
						</p>
					</td>
				</tr>
			</table>
			<?php
		}

		public function update_fields( $term_id ) {
			$fields = ['category-content', 'category-image-id'];
			foreach ( $fields as $field ) {
				$value = isset( $_POST[ $field ] ) ? wp_kses_post( $_POST[ $field ] ) : '';
				update_term_meta( $term_id, $field, $value );
			}
		}

		public function load_media() {
			wp_enqueue_media();
		}

		public function add_script() {
			?>
			<script>
			jQuery(function($){
				function ct_media_upload(button) {
					var frame;
					button.on('click', function(e){
						e.preventDefault();
						if (frame) { frame.open(); return; }
						frame = wp.media({
							title: '<?php _e( "Select or Upload Image", "kavkaz" ); ?>',
							button: { text: '<?php _e( "Use this image", "kavkaz" ); ?>' },
							multiple: false
						});
						frame.on('select', function(){
							var attachment = frame.state().get('selection').first().toJSON();
							$('#category-image-id').val(attachment.id);
							$('#category-image-wrapper').html('<img src="'+attachment.url+'" style="max-height:100px;">');
						});
						frame.open();
					});
				}

				ct_media_upload($('.ct_tax_media_button'));

				$('.ct_tax_media_remove').on('click', function(){
					$('#category-image-id').val('');
					$('#category-image-wrapper').html('');
				});
			});
			</script>
			<?php
		}
	}

	new Kavkaz_Taxonomy_Meta();
}

