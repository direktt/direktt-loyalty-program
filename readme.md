# Direktt Loyalty Program

A powerful WordPress plugin for running a loyalty points system, tightly integrated with the [Direktt WordPress Plugin](https://direktt.com/).  

It is tightly integrated with the [Direktt WordPress Plugin](https://wordpress.org/plugins/direktt/).  

With Loyalty Program you can:

- **Award/reduce loyalty points** to channel subscribers using Direktt mobile app or wp-admin.
- **Set up customizable automated notifications** to subscribers and admins on point changes and award redemptions.
- **Review complete point transaction history** for every subscriber via Direktt mobile app or wp-admin.
- **Display current loyalty account balance & transaction history** to users within Direktt mobile app.

## Documentation

You can find the detailed plugin documentation, guides and tutorials in the Wiki section:  
https://github.com/direktt/direktt-loyalty-program/wiki

## Requirements

- WordPress 5.6 or higher
- The [Direktt Plugin](https://wordpress.org/plugins/direktt/) (must be active)

## Installation

1. Install and activate the **Direktt** core plugin.
2. Download the direktt-loyalty-program.zip from the latest [release](https://github.com/direktt/direktt-loyalty-program/releases)
2. Upload **direktt-loyalty-program.zip** either through WordPress' **Plugins > Add Plugin > Upload Plugin** or upload the contents of this direktt-loyalty-program.zip to the `/wp-content/plugins/` directory of your WordPress installation.
3. Activate **Direktt Loyalty Program** from your WordPress plugins page.
4. Configure the plugin under **Direktt > Settings > Loyalty Program Settings**.

## Usage

### Plugin Settings

- Find **Direktt > Settings > Loyalty Program Settings** in your WordPress admin menu.
- Configure:
    - Direktt user category/tag allowed to manage loyalty program.
    - Initial points balance for new users.
    - Point rules - admin buttons for increments to add/remove points to and from the user account.
    - Notifications for users and channel admin on point changes, award redemptions or point resets.

### Workflow

- Award new loyalty points to users online or in-store:
    1. User scans QR code online or in-store using Direktt mobile app and gets loyalty points or
    1. User makes a purchase or presents coupon in-store. Salesperson scans user's memebership QR code and adds points to user's loyalty account within Direktt mobile app. 
- Users wants to redeem award:
    1. Salesperson scans user's memebership QR code and verifies current loyalty balance.
    1. Salesperson issues an award and resets / removes respective number of points from user's loyalty account using Direktt mobile app.    
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
