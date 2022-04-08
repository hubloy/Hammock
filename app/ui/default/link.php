<?php
if ( ! isset( $class ) ) {
	$class = '';
} else {
	$class = 'class="' . $class . '"';
}
if ( ! isset( $href ) ) {
	$href = '';
} else {
	$href = 'href="' . $href . '"';
}
if ( ! isset( $target ) ) {
	$target = '';
} else {
	$target = 'target="' . $target . '"';
}
if ( ! isset( $rel ) ) {
	$rel = '';
} else {
	$rel = 'rel="' . $rel . '"';
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
<a <?php echo esc_html( $class ); ?> <?php echo esc_html( $href ); ?> <?php echo esc_html( $target ); ?> <?php echo esc_html( $rel ); ?> <?php echo esc_html( $data ); ?> title="<?php echo esc_attr( $title ); ?>"><?php echo esc_html( $title ); ?></a>
