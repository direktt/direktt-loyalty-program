# Direktt Loyalty Program

A powerful WordPress plugin for running a loyalty points system, tightly integrated with the [Direktt WordPress Plugin](https://direktt.com/).

- **Award/reduce loyalty points** for users via the admin interface and custom user profile tool.
- **Send customizable notifications** to users and admins on point changes and resets.
- **Review full transaction history** for every user via wp-admin or user profile tool.
- **Display loyalty points & history** to users via a simple shortcode.

## Requirements

- WordPress 5.0 or higher
- The [Direktt Plugin](https://wordpress.org/plugins/direktt/) (must be active)

## Installation

1. Install and activate the **Direktt** core plugin.
2. Upload this plugin's folder to the `/wp-content/plugins/` directory.
3. Activate **Direktt Loyalty Program** from your WordPress plugins page.
4. Configure the plugin under **Direktt > Settings > Loyalty Program Settings**.

## Usage

### Admin Interface

- Find **Direktt > Settings > Loyalty Program Settings** in your WordPress admin menu.
- Configure:
    - Which user category/tag is considered “admin” for the loyalty program.
    - Initial points for new users.
    - Add point rules (increments for add/remove points to and from the user account).
    - Set up notifications for users and admins on point changes or resets.

### Points Management

- Access a user profile via the Direktt User profile or wp-admin.
- Add or remove points using configured rules.
- Reset user points to initial value when users reedem Awards.
- All actions are logged in the user’s **transaction history**.

### Shortcode (Front End)

Show the loyalty points account and recent transaction history to a Direktt user:

```[direktt_loyalty_program_service]```

## Notification Templates

Direktt Message templates support following dynamic placeholders:

- `#change#` — number of points added/removed
- `#points#` — new points balance
- Other admin templates: `#display_name#`, `#subscription_id#`

## Transaction Logs

For every points change or reset, an entry is made with admin name, change amount, balance, and timestamp.

---

## Updating

The plugin supports updating directly from this GitHub repository.

---

## License

GPL-2.0-or-later

---

## Support

Contact [Direktt](https://direktt.com/) for questions, issues, or contributions.