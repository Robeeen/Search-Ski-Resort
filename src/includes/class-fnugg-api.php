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
        $source_fields = 'name,description,lifts.count,lifts.open';
        $response = wp_remote_get($this->api_base . 'search?q=' . urlencode($query) . '&sourceFields=' . urlencode($source_fields));
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // Filter the data
        $filtered_data = [
            'name' => $data['hits']['hits'][0]['_source']['name'] ?? 'not found',
            'description' => $data['hits']['hits'][0]['_source']['description'] ?? 'not found',
            'lifts' => [
                'count' => $data['hits']['hits'][0]['_source']['lifts']['count'] ?? 0,
                'open' => $data['hits']['hits'][0]['_source']['lifts']['open'] ?? 0,                
            ],
        ];

        // Cache the result
       // set_transient($cache_key, $filtered_data, $this->cache_time);

        return rest_ensure_response($filtered_data);
    }
}

new Fnugg_API();
