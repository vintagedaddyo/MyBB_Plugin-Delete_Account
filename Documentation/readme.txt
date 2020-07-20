 * MyBB: Delete Account
 *
 * Authors: MyBB Security Group (Paul H.) & Vintagedaddyo
 *
 * MyBB Version: 1.8
 *
 * Plugin Version: 1.4


 Installation:

 1. Transfer the files from /Upload/ directory to your MyBB Root directory
 
 2. Go to ACP -> Configuration -> Plugins and activate the Delete Account plugin
 
 3. Go to ACP -> Configuration -> Settings -> Delete Account Plugin  Settings if you want to select what usergroupscan delete thier account and also if you want to enable/disable thread/post removal in admin permanent deletion process.
 
 4. Go to ACP -> User & Groups -> Deleted Accounts -> Here you can view/restore all accounts deleted by the Delete Account plugin.


Enjoy :D



Changelog:

* version 1.4 for MyBB 1.8.x usage by Vintagedaddyo done on mybb 1.8.23

* current localization

- english
- englishgb

7/20/20 @minor edits:

- addition of setting to enable/disable thread/post deletion in admin permanent deletion process.

Ie:

"By default the admin permanent deletion process does not remove the deleted user threads & posts and instead retains them and marks them as the deleted user posted as a guest. If you would like to enable thread and post deletion during the admin permanent deletion process you can enable it here by selecting yes or disable it by selecting no."


* version 1.3 for MyBB 1.8.x usage by Vintagedaddyo done on mybb 1.8.22

* current localization

- english
- englishgb


7/16/20 @minor edits:

- minor tweaks for 1.8.x usage done on php 7.x
- cleaned up lang files ** mostly frontend lang cleanup for removal of acp lang specific content 

- Added current pkg file dir to pkg dir (adding upload & doc primary's for initial edit dist share)
- minor edits to get restore option that wasn't already running re-running
- added dist readme file in documentation (1)
- added dist license file in documentation (1)
- added basic preview files (2)
- added file wide header code commenting for more descriptive file info within dist dir
