# Assign Missing Player TV URLs

WordPress plugin that assigns each `player` custom post a unique `player_tv_url` meta field if one is missing, when the current user is logged in and viewing an admin screen.

## Assumptions

- A `player` custom post type exists with a meta field called `player_external_id`
- All players have a unique value in this field (different than the WordPress post ID)
