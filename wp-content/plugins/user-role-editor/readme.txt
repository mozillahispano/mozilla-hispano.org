=== User Role Editor ===
Contributors: shinephp
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=vladimir%40shinephp%2ecom&lc=RU&item_name=ShinePHP%2ecom&item_number=User%20Role%20Editor%20WordPress%20plugin&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: user, role, editor, security, access, permission, capability
Requires at least: 3.2
Tested up to: 3.5.1
Stable tag: trunk

User Role Editor WordPress plugin makes the role capabilities changing easy. You can change any standard WordPress user role.

== Description ==

With User Role Editor WordPress plugin you can change user role (except Administrator) capabilities easy, with a few clicks.
Just turn on check boxes of capabilities you wish to add to the selected role and click "Update" button to save your changes. That's done. 
Add new roles and customize its capabilities according to your needs, from scratch of as a copy of other existing role. 
Unnecessary self-made role can be deleted if there are no users whom such role is assigned.
Role assigned every new created user by default may be changed too.
Capabilities could be assigned on per user basis. Multiple roles could be assigned to user simultaneously.
You can add new capabilities and remove unnecessary capabilities which could be left from uninstalled plugins.
Multi-site support is provided.

To subscribe for Premium support provided by User Role Editor plugin author Vladimir Garagulya visit [role-editor.com](htpp://role-editor.com)

To read more about 'User Role Editor' visit [this page](http://www.shinephp.com/user-role-editor-wordpress-plugin/) at [shinephp.com](http://shinephp.com)

Русская версия этой статьи доступна по адресу [ru.shinephp.com](http://ru.shinephp.com/user-role-editor-wordpress-plugin-rus/)


== Installation ==

Installation procedure:

1. Deactivate plugin if you have the previous version installed.
2. Extract "user-role-editor.zip" archive content to the "/wp-content/plugins/user-role-editor" directory.
3. Activate "User Role Editor" plugin via 'Plugins' menu in WordPress admin menu. 
4. Go to the "Users"-"User Role Editor" menu item and change your WordPress standard roles capabilities according to your needs.

== Frequently Asked Questions ==
- Does it work with WordPress 3.3 in multi-site environment?
Yes, it works with WordPress 3.3 multi-site. By default plugin works for every blog from your multi-site network as for locally installed blog.
To update selected role globally for the Network you should turn on the "Apply to All Sites" checkbox. You should have superadmin privileges to use User Role Editor under WordPress multi-site.

To read full FAQ section visit [this page](http://www.shinephp.com/user-role-editor-wordpress-plugin/#faq) at [shinephp.com](shinephp.com).

== Screenshots ==
1. screenshot-1.png User Role Editor main form
2. screenshot-2.png Add/Remove roles or capabilities
3. screenshot-3.png User Capabilities link
4. screenshot-4.png User Capabilities Editor

To read more about 'User Role Editor' visit [this page](http://www.shinephp.com/user-role-editor-wordpress-plugin/) at [shinephp.com](shinephp.com).


== Special Thanks to ==
* [Lorenzo Nicoletti](http://www.extera.com) - For the code enhancement. CUSTOM_USER_META_TABLE constant is used now for more compatibility with core WordPress API.
* Marcin - For the code enhancement. This contribution allows to not lose new custom capability if it is added to other than 'Administrator' role.
* [FullThrottle](http://fullthrottledevelopment.com/how-to-hide-the-adminstrator-on-the-wordpress-users-screen) - For the code to hide administrator role at admin backend.

= Translations =
* Arabic: [Yaser](http://www.englize.com/)
* Brasilian Portuguese: André Gama, [Onbiz](http://www.onbiz.com.br/), [Rafael Galdencio](http://www.arquiteturailustrada.com.br)
* Dutch: [Frank Groeneveld](http://ivaldi.nl), [Rémi Bruggeman](http://www.remisan.be)
* French: [Presse et Multimedia](http://presse-et-multimedia.fr/blog), [Whiler](http://blogs.wittwer.fr/whiler)
* German: [Peter](http://www.becker-heidmann.de)
* Hebrew: [Aryo Digital](http://www.aryo.co.il), [Sagive](http://www.sagive.co.il)
* Hindi: [Outshine Solutions](http://outshinesolutions.com)
* Italian: [Tristano Ajmone](http://www.zenfactor.org), [Umberto Sartori](http://venezialog.net)
* Lithuanian: [Vincent G](http://host1free.com)
* Persian: [Parsa](http://parsa.ws), [Good Life](http://good-life.ir), Amir Khalilnejad
* Polish: [TagSite](http://www.tagsite.eu), [Bartosz](www.digitalfactory.pl)
* Russian: [Vladimir Garagulya](http://shinephp.com)
* Serbian: [Diana](http://wpcouponshop.com)
* Spanish: [Victor Ricardo Díaz (INFOMED)](http://www.sld.cu)
* Swedish: [Christer Dahlbacka](www.startlinks.eu), [Andréas Lundgren](http://adevade.com/)
* Traditional Chinese (Jingxin Lai)
* Turkish: [Muhammed YILDIRIM](http://ben.muhammed.im)
* -----------------------------------------------------
* translations below are included to the package, but all of them are outdated and every file needs to be updated. You are welcome!
* Finnish, Japanese, Belorussian, Chinese, Hungarian


Dear plugin User!
If you wish to help me with this plugin translation I very appreciate it. Please send your language .po and .mo files to vladimir[at-sign]shinephp.com email. Do not forget include you site link in order I can show it with greetings for the translation help at shinephp.com, plugin settings page and in this readme.txt file.
If you have better translation for some phrases, send it to me and it will be taken into consideration. You are welcome!
Share with me new ideas about plugin further development and link to your site will appear here.


== Changelog ==
= 3.12 =
* 01.05.2013
* Critical update: persistent cross-site scripting vulnerability is fixed.
* WordPress built-in constants, like WP_PLUGIN_URL are not used in order to provide compatibility with sites which use SSL. plugin_dir_url(), plugin_dir_path() functions are used to define paths to the plugin's files instead. 
* "Greetings" section is removed from the plugin's main page. All that content is still available at [plugin page](http://shinephp.com/user-role-editor-wordpress-plugin)


= 3.11 =
* 24.03.2013
* Required WordPress version checking is moved to plugin activation hook.
* Administrator can now exclude non-core (custom) capabilities from his role. It is useful if you need to fully remove some capability as capability deletion is prohibited while it is used at least one role.
* bbPress compatibility issue is fixed: capabilities created by bbPress dinamically are excluded from the capabilities set in User Role Editor to not store them in the database as persistent WP roles data.
* Additional roles are assigned to user without overriding her primary WordPress role and bbPress role.
* Changing Wordpress user primary role at user profile doesn't clear additonal roles assigned with User Role Editor earlier.
* Brasilian Portuguese translation is updated.

= 3.10 =
* 04.02.2013
* You can assign to user multiple roles simultaneously. Use user level roles and capabilities editor for that. You can click 'Capabilities' link under selected user row at users list or 'Assign Roles and Additional Capabilities' link at user profile.
* Critical bug fix: hidden deprecated WordPress core capabilities had turned on after any update made to the role. Deprecated capabilities are not currently in use by WordPress itself. But old plugins or themes could still use them. If you use some outdated code I recommend you to check all roles, you modified with User Role Editor, and turn off unneeded deprecated capabilities there.
* User with Administrator role is secured better from editing, deletion by user with lower capabilities.

= 3.9 =
* 07.01.2013
* Compatibility with bbPress 2.2 new user roles model is provided. More details about the reason of such update at http://shinephp.com/bbpress-user-role-editor-conflict-fix/
* "Reset" button works differently now. It restores WordPress roles data to its 1st, default state, exactly that, what WordPress has just after fresh install/latest version update. Be careful with it, make database backup copy before fulfill this operation. Some plugin could require reactivation to function properly after roles reset. 
* Arabic translation is added. Thanks to [Yaser](http://www.englize.com/)
* Slovak translation is added. Thanks to [Branco](http://webhostinggeeks.com/blog/)

= 3.8.3 =
* 14.12.2012
* Compatibility issue with WordPress 3.5 was found (thanks to Sonja) and fixed: $wpdb->prepare() was called without 2nd $args parameter - removed.

= 3.8.2 =
* 02.12.2012
* load_plugin_textdomain() call moved to the 'plugins_loaded' hook for higher compatibility with translation plugins.
* Traditional Chinese translation is added. Thanks to Jingxin Lai.

= 3.8.1 =
* 21.10.2012
* Fix: URE taked roles names from the database directly and ignored changes made to roles names on the fly by other plugins or themes, names, which were cached by WordPress internally, but were not written to the database. URE uses WordPress internal cache now.
* Roles names translation update: if URE translation file doesn't exist for blog default language, URE uses WordPress internal translation now.
* Serbian translation is added. Thanks to [Diana](http://wpcouponshop.com).

= 3.8 =
* 01.09.2012
* Bug fix: Some times URE didn't show real changes it made to the database. The reason was that direct update of database did not invalidate data stored at WordPress cache. Special thanks to [Knut Sparhell](http://sparhell.no/knut/) for the help to detect this critical issue.
* WordPress core capabilities are shown separately from capabilities added by plugins and manually.
* If you configured URE to show you 'Administrator' role, you will see its capabilities, but you can not exclude any capability from it. I may just add capabilities to the Administrator role now. The reason - Administrator role should have all existing capabilities included.
* Brasilian Portuguese translation is updated. Thanks to [Onbiz](http://www.onbiz.com.br/).

Click [here](http://www.shinephp.com/user-role-editor-wordpress-plugin-changelog)</a> to look at [the rest part](http://www.shinephp.com/user-role-editor-wordpress-plugin-changelog) of User Role Editor changelog.


== Additional Documentation ==

You can find more information about "User Role Editor" plugin at [this page](http://www.shinephp.com/user-role-editor-wordpress-plugin/)

I am ready to answer on your questions about plugin usage. Use [ShinePHP forum](http://shinephp.com/community/forum/user-role-editor/) or [plugin page comments](http://www.shinephp.com/user-role-editor-wordpress-plugin/) for it please.
