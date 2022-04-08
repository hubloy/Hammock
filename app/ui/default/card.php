<?php
if ( ! isset( $icon ) ) {
	$icon = 'cart';
}
if ( ! isset( $active ) ) {
	$active = false;
}
if ( ! isset( $header ) ) {
	$header = false;
}

if ( ! isset( $attributes ) ) {
	$attributes = array();
}
$data = array();
foreach ( $attributes as $k => $v ) {
	$data[] = "$k=$v";
}
$data = implode( ' ', $data );

if ( ! isset( $padding ) ) {
	$padding = true;
}

$active_class = $active ? 'uk-card-primary' : 'uk-card-default';
$title_class  = '';
if ( ! $padding ) {
	$active_class .= ' uk-padding-remove';
	$title_class   = 'uk-padding uk-padding-remove-bottom';
}

?>
<div>
	<div class="uk-card <?php echo esc_attr( $active_class ); ?> uk-card-body uk-card-hover" <?php echo esc_html( $data ); ?> >
		<h3 class="uk-card-title <?php echo esc_attr( $title_class ); ?>">
			<span uk-icon="icon: <?php echo esc_attr( $icon ); ?>; ratio: 1.5"></span> <?php echo esc_html( $title ); ?>
			<div class="uk-position-top-right uk-padding-small uk-padding-remove-top">
				<?php echo esc_html( $header ); ?>
			</div>
		</h3>
		<div class="uk-card-body">
			<?php echo esc_html( $body ); ?>
		</div>
		<?php
		if ( isset( $footer ) ) {
			?>
				<div class="uk-card-footer">
				<?php echo esc_html( $footer ); ?>
				</div>
				<?php
		}
		?>
		
	</div>
</div>
