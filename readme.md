# Direktt Loyalty Program

Direktt Loyalty Program is a WordPress plugin that brings an easy-to-manage loyalty points system right into your Direktt-powered channel. Fully integrated with the [Direktt mobile app](https://direktt.com), it lets you award, redeem, and track loyalty points for your subscribers, automate notifications, and offer a transparent rewards experience — all managed seamlessly via mobile or wp-admin. Perfect for growing customer engagement and keeping your best clients coming back.

It is tightly integrated with the [Direktt WordPress Plugin](https://wordpress.org/plugins/direktt/).  

With Loyalty Program extension you can:

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

- **Award new loyalty points to users online or in-store:**
    1. Using Direktt mobile app, user scans QR code online or in-store and gets loyalty points or
    1. User makes a purchase or presents a coupon in-store. Salesperson scans user's membership QR code and adds points to user's loyalty account using Direktt mobile app. Both user and admin receive automated message notifications.
- **Users wants to redeem an award:**
    - Salesperson scans user's membership QR code and verifies current loyalty balance. Salesperson issues an award and resets / removes respective number of points from user's loyalty account using Direktt mobile app. Both user and admin receive automated message notifications
- **User wants to check its loyaly balance**.
    - User can always check its loyalty account balance using Direktt mobile app 
- All actions are logged in the user’s **transaction history**.

### Shortcode (Front End)

```[direktt_loyalty_program_service]```

Using this shortcode, you can display the current loyalty account balance and recent transaction history to a Direktt user

## Notification Templates

Direktt Message templates support following dynamic placeholders:

- `#change#` — number of loyalty points added/removed in current transaction
- `#points#` — new points balance after current transaction
- `#display_name#` - display name of the subsriber
- `#subscription_id#` - subscription id of the subscriber

## Transaction Logs

For every points change or reset, a log entry is made with the reference to admin user who made the transaction, change amount, current balance, and timestamp.

---

## Updating

The plugin supports updates directly from WordPress admin console.  

You can find all plugin releases in the Releases section of this repository:  
https://github.com/direktt/direktt-loyalty-program/releases.

---

## License

GPL-2.0-or-later

---

## Support

Please use Issues section of this repository for any issue you might have:  
https://github.com/direktt/direktt-loyalty-program/issues.  

Join Direktt Community on Discord - [Direktt Discord Server](https://discord.gg/xaFWtbpkWp)  

Contact [Direktt](https://direktt.com/) for general questions, issues, or contributions.
