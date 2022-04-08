<?php
if ( ! isset( $class ) ) {
	$class = '';
} else {
	$class = 'class="' . $class . '"';
}
if ( ! isset( $label_class ) ) {
	$label_class = '';
}
if ( ! isset( $attributes ) ) {
	$attributes = array();
}
$data = array();
foreach ( $attributes as $k => $v ) {
	$data[] = "$k=$v";
}
$data = implode( ' ', $data );
?>
<section class="slider-checkbox">
	<input type="checkbox" <?php echo esc_attr( $class ); ?> <?php echo esc_html( $data ); ?> name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php checked( $option, $value ); ?>/>
	<label class="label <?php echo esc_attr( $label_class ); ?>" for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $title ); ?></label>
</section>
