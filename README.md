# Fix passwords issues when migrating from WP 6.8.x

**There are migration issues between WordPress 6.8.x and ClassicPress due to a different approach to password hashing.**
This plugin provides a **temporary fix** until [#1955](https://github.com/ClassicPress/ClassicPress/pull/1955) is merged.
It's scheduled to be in ClassicPress version [2.5.0](https://github.com/ClassicPress/ClassicPress/milestone/31).

*Notice that this plugin may conflict with other plugins that modify `wp_check_password` or `wp_password_needs_rehash`.*

See [🐞 wp_check_password code discrepancy #1946](https://github.com/ClassicPress/ClassicPress/issues/1946) and [Update password checks for WordPress 6.8 #1955](https://github.com/ClassicPress/ClassicPress/pull/1955).
