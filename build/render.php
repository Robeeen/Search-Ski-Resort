<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>
<p <?php echo get_block_wrapper_attributes(); ?>>
	
	<?php
	$searchName =  $attributes['selectedOption'];
    if( ! empty($searchName)){
         echo 'Resort Name Searched: ' . esc_html( $searchName ); }
        ?>
</p>
