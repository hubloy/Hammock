<?php
namespace HubloyMembership\View\Backend\Email;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use HubloyMembership\Base\View;
use HubloyMembership\Helper\Template;
use HubloyMembership\Helper\File;

class Setting extends View {

	/**
	 * Builds template and return it as string.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function to_html() {
		$params        = $this->data['params'];
		$template      = $this->data['template'];
		$is_admin      = $this->data['is_admin'];
		$place_holders = $this->data['place_holders'];
		$id            = $this->data['id'];
		$file_service  = new File();
		ob_start();
		?>
		<div class="uk-margin">
			<label class="uk-form-label uk-text-bold" for="enabled">
				<?php esc_html_e( 'Enabled', 'hubloy-membership' ); ?>
			</label>
			<div class="uk-form-controls hubloy_membership-input">
				<?php
				$this->ui->render(
					'switch',
					array(
						'title'  => __( 'Enabled', 'hubloy-membership' ),
						'name'   => 'enabled',
						'value'  => 1,
						'option' => $params['enabled'],
					)
				);
				?>
				<p class="uk-text-meta"><?php esc_html_e( 'Enable or disable email', 'hubloy-membership' ); ?></p>
			</div>
		</div>
		<div class="uk-margin">
			<label class="uk-form-label" for="subject"><?php esc_html_e( 'Email Subject', 'hubloy-membership' ); ?></label>
			<div class="uk-form-controls">
				<?php
					$this->ui->render(
						'input',
						array(
							'name'        => 'subject',
							'class'       => 'uk-input',
							'value'       => $params['subject'],
							'placeholder' => '',
						)
					);
				?>
				<p class="uk-text-meta">
					<?php echo sprintf( esc_html__( 'Use the follwing placeholders %s', 'hubloy-membership' ), '<strong>' . implode( '</strong>, <strong>', $place_holders ) . '</strong>' ); ?>
				</p>
			</div>
		</div>
		<div class="uk-margin">
			<label class="uk-form-label" for="heading"><?php esc_html_e( 'Email Heading', 'hubloy-membership' ); ?></label>
			<div class="uk-form-controls">
				<?php
					$this->ui->render(
						'input',
						array(
							'name'        => 'heading',
							'class'       => 'uk-input',
							'value'       => $params['heading'],
							'placeholder' => '',
						)
					);
				?>
			</div>
		</div>
		<?php
		if ( $is_admin ) {
			?>
				<div class="uk-margin">
					<label class="uk-form-label" for="recipient"><?php esc_html_e( 'Recipient', 'hubloy-membership' ); ?></label>
					<div class="uk-form-controls">
					<?php
						$this->ui->render(
							'input',
							array(
								'name'        => 'recipient',
								'class'       => 'uk-input',
								'value'       => $params['recipient'],
								'placeholder' => '',
							)
						);
					?>
					</div>
				</div>
				<?php
		}
		if ( current_user_can( 'edit_themes' ) && ( ! empty( $template ) ) ) {
			?>
			<h4 class="uk-heading-small uk-heading-divider"><?php esc_html_e( 'Email Template', 'hubloy-membership' ); ?></h4>
			<?php
			$local_file    = Template::get_theme_template_file( $template );
			$template_file = HUBMEMB_TEMPLATE_DIR . $template;
			$template_dir  = Template::template_directory();
			?>
			<div class="template">
				<?php if ( $file_service->exists( $local_file ) ) : ?>
					<button class="uk-button uk-button-default uk-button-small" type="button" uk-toggle="target: .hubloy_membership-template-details; animation: uk-animation-fade"><?php esc_html_e( 'View', 'hubloy-membership' ); ?></button>
					<?php if ( $file_service->is_writable( $local_file ) ) : ?>
						<a href="#" class="hubloy_membership-ajax-click uk-button uk-button-default" data-action="hubloy_membership_email_delete_theme" data-nonce="<?php echo wp_create_nonce( 'hubloy_membership_email_delete_theme' ); ?>" data-id="<?php echo $id; ?>">
							<?php esc_html_e( 'Delete template file', 'hubloy-membership' ); ?>
						</a>
					<?php endif; ?>
					<p>
					<?php
						printf( esc_html__( 'This template has been overridden by your theme and can be found in: %s.', 'hubloy-membership' ), '<code>' . esc_html( trailingslashit( basename( get_stylesheet_directory() ) ) . $template_dir . '/' . $template ) . '</code>' );
					?>
					</p>
					<div class="uk-card uk-card-default uk-card-body uk-margin-small hubloy_membership-template-details" hidden>
						<pre><?php echo esc_html( $file_service->read_file( $local_file ) ); ?></pre>
					</div>
				<?php elseif ( $file_service->exists( $template_file ) ) : ?>
					<button class="uk-button uk-button-default uk-button-small" type="button" uk-toggle="target: .hubloy_membership-template-details; animation: uk-animation-fade"><?php esc_html_e( 'View', 'hubloy-membership' ); ?></button>
					<?php
						$emails_dir    = get_stylesheet_directory() . '/' . $template_dir . '/emails';
						$templates_dir = get_stylesheet_directory() . '/' . $template_dir;
						$theme_dir     = get_stylesheet_directory();

					if ( is_dir( $emails_dir ) ) {
						$target_dir = $emails_dir;
					} elseif ( is_dir( $templates_dir ) ) {
						$target_dir = $templates_dir;
					} else {
						$target_dir = $theme_dir;
					}

					if ( $file_service->is_writable( $target_dir ) ) :
						?>
							<a href="#" class="uk-button uk-button-default uk-button-small hubloy_membership-ajax-click" data-action="hubloy_membership_email_copy_theme" data-nonce="<?php echo wp_create_nonce( 'hubloy_membership_email_copy_theme' ); ?>" data-id="<?php echo $id; ?>">
							<?php esc_html_e( 'Copy file to theme', 'hubloy-membership' ); ?>
							</a>
						<?php
						endif;
					?>
						<p>
							<?php
								printf( esc_html__( 'To override and edit this email template copy %1$s to your theme folder: %2$s.', 'hubloy-membership' ), '<code>' . esc_html( plugin_basename( $template_file ) ) . '</code>', '<code>' . esc_html( trailingslashit( basename( get_stylesheet_directory() ) ) . $template_dir . '/' . $template ) . '</code>' );
							?>
						</p>
					<div class="uk-card uk-card-default uk-card-body uk-margin-small hubloy_membership-template-details" hidden>
						<pre><?php echo esc_html( $file_service->read_file( $template_file ) ); ?></pre>
					</div>
				<?php else : ?>
					<p><?php esc_html_e( 'File was not found.', 'hubloy-membership' ); ?></p>
				<?php endif; ?>
			</div>
			<?php
		}
		return ob_get_clean();
	}
}
?>
