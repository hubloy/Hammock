<?php
$pagenum     = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 0; // WPCS: CSRF OK
$page_number = max( 1, $pagenum );
$per_page    = apply_filters( 'hubloy_pagination_per_page', 10 );
ob_start();
if ( $total > $per_page ) {
	$removable_query_args = wp_removable_query_args();

	$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
	$current_url = remove_query_arg( $removable_query_args, $current_url );
	$current     = $page_number + 1;
	$total_pages = ceil( $total / $per_page );
	?>
	<ul class="uk-pagination uk-flex-right hubloy-ajax-pagination" uk-margin>
		<?php
		for ( $i = 1; $i <= $total_pages; $i ++ ) :
			$class = ( $page_number == $i ) ? 'uk-active' : '';
			?>
			<li class="<?php echo esc_attr( $class ); ?>"><a href="#" data-page="<?php echo esc_html( $i ); ?>"><?php echo esc_html( $i ); ?></a></li>

		<?php endfor; ?>
		
	</ul>
	<?php
}
?>
