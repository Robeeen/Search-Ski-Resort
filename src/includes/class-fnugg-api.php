<?php

class Fnugg_API {
    private $api_base = 'https://api.fnugg.no/';
    //private $cache_time = 3600; // Cache for 1 hour

    public function __construct() {
        add_action('rest_api_init', function () {
            register_rest_route('fnugg/v1', '/autocomplete', [
                'methods' => 'GET',
                'callback' => [$this, 'autocomplete_resorts'],
                'permission_callback' => '__return_true',
            ]);

            register_rest_route('fnugg/v1', '/search', [
                'methods' => 'GET',
                'callback' => [$this, 'search_resort'],
                'permission_callback' => '__return_true',
            ]);
        });
    }

    public function autocomplete_resorts(WP_REST_Request $request) {
        $query = sanitize_text_field($request->get_param('q'));

        // Check cache
        // $cache_key = 'fnugg_autocomplete_' . md5($query);
        // $cached_result = get_transient($cache_key);
        // if ($cached_result) {
        //     return rest_ensure_response($cached_result);
        // }

        // Call Fnugg API
        $response = wp_remote_get($this->api_base . 'suggest/autocomplete/?q=' . urlencode($query));
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // Cache the result
        //set_transient($cache_key, $data, $this->cache_time);

        return rest_ensure_response($data);
        
    }

    public function search_resort(WP_REST_Request $request) {
        $query = sanitize_text_field($request->get_param('q'));

        // Check cache
        // $cache_key = 'fnugg_search_' . md5($query);
        // $cached_result = get_transient($cache_key);
        // if ($cached_result) {
        //     return rest_ensure_response($cached_result);
        // }

        // Call Fnugg API
        //source_field must not have space before comma *********//
        $source_fields = 'name,description,resort_opening_date,lifts.count,lifts.open,conditions.combined.top.symbol.fnugg_id,conditions.combined.top.symbol.name,images.image_1_1_l,contact.address,contact.zip_code,contact.call_number,conditions.combined.top.temperature.value';
        $response = wp_remote_get($this->api_base . 'search?q=' . urlencode($query) . '&sourceFields=' . urlencode($source_fields));
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
       
        // Filter the data
        $filtered_data = [
            '_index' => $data['hits']['hits'][0]['_index'] ?? 'not',
            '_type' => $data['hits']['hits'][0]['_type'] ?? 'not',
            '_id' => $data['hits']['hits'][0]['_id'] ?? 'not',
            'name' => $data['hits']['hits'][0]['_source']['name'] ?? 'not found',
            'resort_opening_date' => $data['hits']['hits'][0]['_source']['resort_opening_date'] ?? 'not found',
            'description' => $data['hits']['hits'][0]['_source']['description'] ?? 'not found',
            'contact' => [
                'address' => $data['hits']['hits'][0]['_source']['contact']['address'] ?? 'not found',
                'zip_code' => $data['hits']['hits'][0]['_source']['contact']['zip_code'] ?? 'not found',
                'call_number' => $data['hits']['hits'][0]['_source']['contact']['call_number'] ?? 'not found',
            ],            
            'lifts' => [
                'count' => $data['hits']['hits'][0]['_source']['lifts']['count'] ?? 0,
                'open' => $data['hits']['hits'][0]['_source']['lifts']['open'] ?? 0,               
            ], 
            'symbol' => [
                'fnugg_id' => $data['hits']['hits'][0]['_source']['conditions']['combined']['top']['symbol']['fnugg_id'] ?? 0,
                'name' => $data['hits']['hits'][0]['_source']['conditions']['combined']['top']['symbol']['name'] ?? 0,
            ], 
            'images' => [
                'images' => $data['hits']['hits'][0]['_source']['images']['image_1_1_l'] ?? 'not found',
            ],
            'temperature' => [
                'value' => $data['hits']['hits'][0]['_source']['conditions']['combined']['top']['temperature']['value'] ?? '0',
            ]       
          
        ];

        // Cache the result
       // set_transient($cache_key, $filtered_data, $this->cache_time);

        return rest_ensure_response($filtered_data);
    }
}

new Fnugg_API();
