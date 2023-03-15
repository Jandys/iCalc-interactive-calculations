<?php


namespace icalc\util;


function requireAutocomplete(){
    wp_enqueue_script('icalc_autocomplete_scripts', plugins_url('/scripts/autocomplete.js', __FILE__), array(), '0.0.1', false);
    add_action('wp_enqueue_scripts', 'icalc_autocomplete_scripts');
}