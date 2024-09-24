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
    
 

    echo '<img src="' . $response->{'images'}->{"images"} . '" width="100%" height="100%">';
    echo '<span style="margin-left: 10px;font-size: 20px; color: #87CEEB">' . $response->{'name'} . '</span><br />';
    if($attributes['showAddr']){
        echo '<span style="margin-left: 10px">Address: ' . $response->{'contact'}->{'address'} . '</span><br />';
    }
    if($attributes['showPhone']){
        echo '<span style="margin-left: 10px">Phone: +' . $response->{'contact'}->{'call_number'} . '</span><br />';
    }
    if($attributes['showLift']){
        echo '<span style="margin-left: 10px">Lifts Count: ' . $response->{'lifts'}->{'count'} . '</span><br />';
    }
    
    echo '<img src="' . plugin_dir_url( __DIR__ ) . 'src/image/symbols/' . $response->{'symbol'}->{"fnugg_id"} . '.svg" style="margin-left: 10px">';

    echo $response->{'symbol'}->{"name"} . '<span style="margin-left: 8px; font-size: 30px;color: #FFFF00">' .  $response->{'temperature'}->{"value"} . '&#176;</span>' .  "<br />";
    echo '<div style="display:flex; posititon:relative; margin-top: -70px; margin-left: 280px;color: white;font-size: 16px; ">';
    echo '<div>';
        echo '<img src="' . plugin_dir_url( __DIR__ ) . 'src/image/symbols/path.svg' . '">';
    echo '</div>';
    echo '<div>';
        echo "Sesongstart" . "<br />" . substr($response->{'resort_opening_date'}, 0, 10);
    echo '</div>';

    echo '</div>';


        
        
        
        
        
        
        
        
        
        ?>
</p>
