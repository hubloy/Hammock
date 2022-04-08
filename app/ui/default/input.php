<?php
if ( ! isset( $type ) ) {
	$type = 'text';
}
?>
<input type="<?php echo esc_attr( $type ); ?>" name="<?php echo esc_attr( $name ); ?>" class="<?php echo esc_attr( $class ); ?>"  value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>"/>
