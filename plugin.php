<?php

/**
 * Plugin Name: Discount ESP Referral Form
 * Description: A form that allows users to enter data about their vehicle using a discount code.
 * Version: 1.0
 * Author: Aiden Merrill
 */

function referral_form_enqueue_scripts()
{
    global $post;
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'referral_form')) {
        $referral_script_url = (defined('WP_DEBUG') && WP_DEBUG) ? 'http://localhost:3000/src/main.js' : plugin_dir_url(__FILE__) . 'dist/assets/main-YTN2Bx7c.js';
        wp_enqueue_script('referral-plugin-script', $referral_script_url, array(), false, true);
    }
}

add_action('wp_enqueue_scripts', 'referral_form_enqueue_scripts');

function referral_form_enqueue_styles()
{
    global $post;
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'referral_form')) {
        $referral_style_url = plugin_dir_url(__FILE__) . 'dist/assets/main-j4AFURBj.css';
        wp_enqueue_style('referral-plugin-styles', $referral_style_url);
    }
}

add_action('wp_enqueue_scripts', 'referral_form_enqueue_styles');


function referral_form_shortcode()
{
    ob_start();
    include_once plugin_dir_path(__FILE__) . 'public/index.php';
    return ob_get_clean();
}

add_shortcode('referral_form', 'referral_form_shortcode');
