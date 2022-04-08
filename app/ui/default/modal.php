<div id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $id ); ?>" uk-modal="bg-close:false">
	<div class="uk-modal-dialog">
		<button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
		<div class="uk-modal-header">
			<h2 class="uk-modal-title"><?php echo esc_html( $title ); ?></h2>
			<div class="uk-invisible hubloy-message uk-padding-remove uk-margin-remove" uk-alert></div>
		</div>
		<div class="uk-modal-body"><?php echo esc_html( $content ); ?></div>
	</div>
</div>
