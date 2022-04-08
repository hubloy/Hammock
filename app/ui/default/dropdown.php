<?php
if ( ! isset( $selected ) ) {
	$selected = '';
}
if ( ! isset( $class ) ) {
	$class = '';
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

<select name="<?php echo esc_attr( $name ); ?>" class="uk-select <?php echo esc_attr( $class ); ?>" id="form-horizontal-select" <?php echo esc_html( $data ); ?>>
	<?php
	if ( isset( $values ) ) {
		foreach ( $values as $key => $value ) {
			?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $selected, $key ); ?>><?php echo esc_html( $value ); ?></option>
			<?php
		}
	}
	?>
</select>
	
