=== Verified Member for BuddyPress ===
Contributors: themosaurus
Tags: buddypress, bp, verified, verify, member, community, badge
Requires at least: 4.6
Tested up to: 5.2
Requires PHP: 5.6
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Verify your BuddyPress members and display a twitter-like verified badge on the front-end.

== Description ==
This plugin will allow you to manually verify your BuddyPress members from the profile edit screen. Verified members will have a twitter-like "verified" badge displayed on the front-end.

A dedicated settings tab allows you to choose where you want to display the badges:
+ Activities
+ Profiles
+ Member Lists (e.g.: member directory)
+ bbPress Forums
+ WordPress Comments

See the plugin in action in our online demo:

+ [Members Directory](https://classic.gwangi-theme.com/members/).
+ [User Profile and Activity Feed](https://classic.gwangi-theme.com/members/michellie/).

This plugin requires BuddyPress.

== Installation ==
= AUTOMATIC INSTALLATION =
Automatic installation is the easiest option — WordPress will handles the file transfer, and you won’t need to leave your web browser. To do an automatic install of Verified Member for BuddyPress, log in to your WordPress dashboard, navigate to the Plugins menu, and click “Add New.”

In the search field type “Verified Member for BuddyPress,” then click “Search Plugins.” Once you’ve found us, you can view details about it such as the point release, rating, and description. Most importantly of course, you can install it by clicking “Install Now,” and WordPress will take it from there.

= MANUAL INSTALLATION =
Manual installation method requires downloading the Verified Member for BuddyPress plugin and uploading it to your web server via your favorite FTP application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

= UPDATING =
Automatic updates should work smoothly, but we still recommend you back up your site.

== Frequently Asked Questions ==
= How do I mark a member as verified ? =
+ From your admin panel go to *Users > All Users*
+ Click on the user you want to verify
+ Go to the *Extended Profile* tab
+ Under the *Verify Member* box, check "Mark this user as verified" and click the "Update Profile" button

= How do I verify / unverify multiple users at once ? =
+ From your admin panel go to *Users > All Users*
+ Check all the users you want to verify or unverify
+ In the *Bulk Actions* dropdown, choose "Verify" or "Unverify"
+ Click "Apply"

= How do I choose where the verified badge is displayed ? =
+ From your admin panel go to *Settings > BuddyPress*
+ Open the *Verified Member* tab
+ Check the places where you want the badge displayed and click "Save Changes"

= How do I change the color of the verified badge ? =
You can easily change the color of the badge via a color picker in the settings page.
The settings are located in *Settings > BuddyPress* in the *Verified Member* tab.

== Screenshots ==
1. BuddyPress members directory showing some verified users
2. BuddyPress profile page showing a verified user and user activities
3. BuddyPress extended profile showing the "Mark this member as verified" checkbox
4. Plugin settings page

== Changelog ==
= 1.0 =
* Initial release
= 1.0.1 =
* Fix missing files from the plugin package
= 1.0.2 =
* Improve style with BP Nouveau template pack
* Split "Display in Profile" option in two new options: "Display in Profile Fullname" and "Display in Profile Username" to allow for more control and compatibility with themes
* Fix saving issue when trying to save "Verified" status when admin language is different than english
* Remove verified badge HTML from "Public Mention" links
* Prevent loading the plugin and add a warning message if BuddyPress is not installed
= 1.0.3 =
* Fix bug in admin activities screen
* Remove verified badge HTML from "Friends" widget title
= 1.0.4 =
* Prevent errors and warnings that occurred with some widgets
* Prevent double badge display
* Add compatibility with Author Avatars List plugin
= 1.1.0 =
* Add option to change the color of the badge
* Improved options layout in settings page
* Add Bulk Actions in users table to verify / unverify multiple users at once
* Add column in users table to show their verified status
* Add option to display verified badge in WordPress comments
* Add option to display badge in bbPress Topics
* Add option to display badge in bbPress Replies
= 1.1.1 =
* Fix php warning
* Fix user name trimmed in the browser tab title
* Fix compatibility with new bbPress version 2.6
* Fix missing admin.js file
= 1.1.2 =
* Fix various php warnings
