<?php
/**
 * Assign missing Player TV URLs
 *
 * @wordpress-plugin
 * Plugin Name: Assign Missing Player TV URLs
 * Plugin URI:  https://github.com/paulshryock/nba-amp-tv-urls
 * Description: Assigns each `player` custom post a unique `player_tv_url` meta field if one is missing, when the current user is logged in and viewing an admin screen.
 * Version:     1.0.0
 * Author:      Paul Shryock
 * Author URI:  https://pshry.com/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: nba-amp-tv-urls
 * Domain Path: /languages
 */

add_action( 'admin_init', 'nba_assign_missing_player_tv_urls' );

/**
 * Assigns each `player` custom post a unique `player_tv_url` meta field is one is missing
 * 
 * @return bool Returns true if any players were updated, otherwise false
 */
function nba_assign_missing_player_tv_urls() {
  // Make sure this only runs once
  if ( did_action('admin_init') !== 1 ) {
    return false;
  }

  // Get all players that need a 'player_tv_url'
  $players = new WP_Query(
    array(
      'post_type' => 'player',
      'meta_query' => array(
        'relation' => 'OR',
        array(
         'key' => 'player_tv_url',
         'compare' => 'NOT EXISTS', // 'player_tv_url' does not exist
         'value' => '' // This is ignored, but is necessary in legacy WP versions
        ),
        array(
         'key' => 'player_tv_url',
         'value' => '' // 'player_tv_url' exists, but is empty
        )
      )
    )
  );

  // Make sure we have any players to update
  if ( ! $players ) {
    return false;
  }

  // For each player
  foreach ( $players as $player ) :
    // Get the current player's `player_external_id`
    $player_external_id = get_post_meta(
      $post_id = $player->ID,
      $key = 'player_external_id',
      $single = true
    );

    // Assign this player a unique `player_tv_url`
    add_post_meta(
      $post_id = $player->ID,
      $meta_key = 'player_tv_url',
      $meta_value = 'http://www.nba-player-tv.com/channel/' . $player_external_id,
      $unique = true
    );
  endforeach;

  // Since we used WP_Query, restore the $post global to the current post in the main query
  // In case this function ever runs during a non-admin action hook
  wp_reset_postdata();

  return true;
}
