<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>
<p <?php echo get_block_wrapper_attributes(); ?>>
	
	<?php
	$searchName =  $attributes['selectedOption'];

    $search = explode(" ", $searchName);
        
    $api_url = "http://devwp.local/wp-json/fnugg/v1/search?q=$search[0]";

    $json_data = file_get_contents($api_url);     
      
    $response = json_decode($json_data);

    echo "Resort ID: " . $response->{'_id'} . "<br />";
    echo "Resort Name: " . $response->{'name'} . "<br />";
    echo "Resort Description: " . $response->{'description'} . "<br />";
    echo "Resort Type: " . $response->{'_type'} . "<br />";
    echo "Lifts Count: " . $response->{'lifts'}->{'count'} . "<br />";
    // echo "Symbol-name: " . $response->{'symbol'}->{'name'} . "<br />";
    // echo "Symbol-id: " . $response->{'symbol'}->{'yr_id'} . "<br />";
        
        
        
        
        
        
        
        
        
        
        ?>
</p>
