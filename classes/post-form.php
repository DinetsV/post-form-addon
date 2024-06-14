<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Post_Form
 */
class Post_Form {
	/**
     *  Build template for client
	 *
	 * @param array{
	 *     title: string,
	 *     description: string,
     *     show_categories: string,
	 *     not_registered: string,
	 * } $settings
	 *
	 * @since 1.0.0
	 *  @access public
	 *  @static
	 *
	 * @return string
	 */
	static function get_render_form( array $settings ): string {
		$user = wp_get_current_user();
        ob_start(); ?>
			<section class="el-post-form-widget">
                <h3 class="el-post-form-head"><?php echo esc_html($settings['title']); ?></h3>
				<?php if ($user->exists()): ?>
                    <p class="el-post-form-desc"><?php echo esc_html($settings['description']); ?></p>
                    <form class="el-post-form" id="post_form">
                        <label class="el-post-form-label">
                            <span class="el-post-form-label-text">
                                <?php esc_html_e('Title', 'post-form-addon'); ?>
                            </span>
                            <input type="text" name="post_title" class="el-post-form-control" required>
                        </label>
                        <?php if ($settings['show_categories']):
				            $categories = get_categories(['hide_empty' => false]);
                        ?>
                            <label class="el-post-form-label">
                                <span class="el-post-form-label-text">
								    <?php esc_html_e('Category', 'post-form-addon'); ?>
                                </span>
                                <select name="post_category" class="el-post-form-control">
                                    <option selected disabled>
										<?php esc_html_e('Select category', 'post-form-addon');?>
                                    </option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo esc_attr($category->term_id); ?>">
                                            <?php echo esc_html($category->name);?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                        <?php endif; ?>
                        <label class="el-post-form-label">
                            <span class="el-post-form-label-text">
								<?php esc_html_e('Content', 'post-form-addon'); ?>
                            </span>
                            <?php wp_editor('', 'post_content', ['textarea_name' => 'post_content', 'teeny'=>false, 'media_buttons'=>false, 'quicktags' => false]); ?>
                        </label>
                        <button type="submit" class="el-post-form-submit">
							<?php esc_html_e('Propose post', 'post-form-addon'); ?>
                        </button>
                    </form>
                <?php else: ?>
                    <p class="el-post-form-desc"><?php echo $settings['not_registered'] ?></p>
                <?php endif; ?>
			</section>
		<?php return ob_get_clean();
	}

	/**
     * Build template for preview
     *
     * @since 1.0.0
	 * @access public
	 * @static
     *
	 * @return string
	 */
	static function get_template_form(): string {
		ob_start(); ?>
            <section class="el-post-form-widget">
                <h3 class="el-post-form-head">{{{ settings.title }}}</h3>
                <p class="el-post-form-desc">{{{ settings.description }}}</p>
                <form class="el-post-form" onsubmit="event.preventDefault();">
                    <label class="el-post-form-label">
                        <span class="el-post-form-label-text">
						    <?php esc_html_e('Title', 'post-form-addon'); ?>
                        </span>
                        <input type="text" name="post_title" class="el-post-form-control">
                    </label>
                    <# if ( settings.show_categories ) { #>
                        <label class="el-post-form-label">
                            <span class="el-post-form-label-text">
							    <?php esc_html_e('Category', 'post-form-addon'); ?>
                            </span>
                            <select name="post_category" class="el-post-form-control">
                                <option selected disabled>
									<?php esc_html_e('Select category', 'post-form-addon');?>
                                </option>
                                <option value="0">
                                    <?php esc_html_e('Category', 'post-form-addon');?>
                                </option>
                            </select>
                        </label>
                    <# } #>
                    <label class="el-post-form-label">
                        <span class="el-post-form-label-text">
						    <?php esc_html_e('Content', 'post-form-addon'); ?>
                        </span>

                        <textarea name="post_content" class="el-post-form-control" rows="25"></textarea>
                    </label>
                    <button type="submit" class="el-post-form-submit">
						<?php esc_html_e('Propose post', 'post-form-addon'); ?>
                    </button>
                </form>
            </section>
		<?php return ob_get_clean();
	}

	/**
	 * Create pending post from submitted data
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return void
	 */
    static function submit_form(): void {
        $pass_check = wp_verify_nonce($_POST['nonce'], 'ajax-nonce') && $_POST['post_title'] && $_POST['post_content'];

        if ($pass_check) {
			$post_data = array(
				'post_title'    => sanitize_text_field( $_POST['post_title'] ),
				'post_content'  => $_POST['post_content'],
				'post_status'   => 'pending',
				'post_author'   => 1,
				'post_category' => [$_POST['post_category']]
			);

			wp_insert_post( $post_data );
        }
		self::send_response($pass_check);
	}

	/**
	 * Send response for client
	 *
	 * @param bool $pass_check
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 *  @return void
	 */
    static function send_response( bool $pass_check ) {
		if ( $pass_check ) {
			wp_send_json_success( esc_html__( 'Thanks for proposing a new post!', 'post-form-addon' ) );
		} else {
			wp_send_json_error( esc_html__( 'Something went wrong! Try again later...', 'post-form-addon' ) );
		}
	}
}
