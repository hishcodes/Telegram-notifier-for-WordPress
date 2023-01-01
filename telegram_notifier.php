<?php

/*
Plugin Name: Telegram Notifier
Description: Sends messages using Telegram bot.
Version: 1.0
Author: Hisham Moideen
Plugin URI: https://wp.hishcodes.com
Author URI: https://hishcodes.com
*/

//Log in notification
function telegram_login_notifier_login($user_login, $user) {
  $options = get_option('telegram_new_post_notifier_options');
  $bot_token = $options['bot_token'];
  $chat_id = $options['chat_id'];
  $message = "User $user_login has logged in.";
  $send_message = file_get_contents("https://api.telegram.org/bot$bot_token/sendMessage?chat_id=$chat_id&text=$message");
}
add_action('wp_login', 'telegram_login_notifier_login', 10, 2);

//Log out notification
function telegram_login_notifier_logout() {
  $options = get_option('telegram_new_post_notifier_options');
  $bot_token = $options['bot_token'];
  $chat_id = $options['chat_id'];
  
    // Send the message
    $user = wp_get_current_user();
    $user_login = $user->user_login;
    $message = "User $user_login has logged out.";
    $send_message = file_get_contents("https://api.telegram.org/bot$bot_token/sendMessage?chat_id=$chat_id&text=$message");
  }
  add_action('wp_logout', 'telegram_login_notifier_logout');


  //New post notification
  function telegram_new_post_notifier_publish_post($post_id) {
    // Get the bot token and chat ID from the plugin settings
    $options = get_option('telegram_new_post_notifier_options');
    $bot_token = $options['bot_token'];
    $chat_id = $options['chat_id'];
  
    // Check if the notification has already been sent for this post
    $notification_sent = get_post_meta($post_id, 'telegram_new_post_notification_sent', true);
    if ($notification_sent) {
      return;
    }
  
    // Get the post details
    $post = get_post($post_id);
    $title = $post->post_title;
    $url = get_permalink($post_id);
  
    // Send the message
    $message = "New post published: $title - $url";
    $send_message = file_get_contents("https://api.telegram.org/bot$bot_token/sendMessage?chat_id=$chat_id&text=$message");
  
    // Set the notification sent flag
    update_post_meta($post_id, 'telegram_new_post_notification_sent', true);
  }
  add_action('publish_post', 'telegram_new_post_notifier_publish_post');


  // Register the plugin settings
function telegram_new_post_notifier_register_settings() {
  register_setting('telegram_new_post_notifier_options', 'telegram_new_post_notifier_options');
  add_settings_section('telegram_new_post_notifier_section', 'Telegram Notifier Settings', 'telegram_new_post_notifier_section_callback', 'telegram_new_post_notifier');
  add_settings_field('bot_token', 'Bot Token', 'telegram_new_post_notifier_bot_token_callback', 'telegram_new_post_notifier', 'telegram_new_post_notifier_section');
  add_settings_field('chat_id', 'Chat ID', 'telegram_new_post_notifier_chat_id_callback', 'telegram_new_post_notifier', 'telegram_new_post_notifier_section');
}
add_action('admin_init', 'telegram_new_post_notifier_register_settings');

// Display the plugin settings form
function telegram_new_post_notifier_display_form() {
  ?>
  <div class="wrap">
    <h1>Telegram Notifier</h1>
    <form method="post" action="options.php">
      <?php settings_fields('telegram_new_post_notifier_options'); ?>
      <?php do_settings_sections('telegram_new_post_notifier'); ?>
      <?php submit_button(); ?>
    </form>
    <a href="https://www.hishcodes.com/" target="_blank">Plugin by Hishcodes</a>
  </div>
  <?php
}

// Add a link to the plugin settings page from the plugin list
function telegram_new_post_notifier_add_settings_link($links) {
  $settings_link = '<a href="options-general.php?page=telegram_new_post_notifier">Settings</a>';
  array_push($links, $settings_link);
  return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'telegram_new_post_notifier_add_settings_link');

// Add the plugin settings page to the WordPress menu
function telegram_new_post_notifier_add_menu_item() {
  add_options_page('Telegram Notifier', 'Telegram Notifier', 'manage_options', 'telegram_new_post_notifier', 'telegram_new_post_notifier_display_form');
}
add_action('admin_menu', 'telegram_new_post_notifier_add_menu_item');

// Display the plugin settings section
function telegram_new_post_notifier_section_callback() {
  echo 'Enter your bot token and chat ID below:';
}

// Display the bot token field
function telegram_new_post_notifier_bot_token_callback() {
  $options = get_option('telegram_new_post_notifier_options');
  $value = $options['bot_token'];
  echo "<input type='text' name='telegram_new_post_notifier_options[bot_token]' value='$value' />";
}

// Display the chat ID field
function telegram_new_post_notifier_chat_id_callback() {
  $options = get_option('telegram_new_post_notifier_options');
  $value = $options['chat_id'];
  echo "<input type='text' name='telegram_new_post_notifier_options[chat_id]' value='$value' />";
}
?>