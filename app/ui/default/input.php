<?php
if ( ! isset( $type ) ) {
	$type = 'text';
}
?>
<input type="<?php echo $type; ?>" name="<?php echo $name; ?>" class="<?php echo $class; ?>"  value="<?php echo $value; ?>" placeholder="<?php echo $placeholder; ?>"/>
