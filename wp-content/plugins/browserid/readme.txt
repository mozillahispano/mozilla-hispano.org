=== Mozilla Persona (BrowserID) ===
Contributors: stomlinson, Marcel Bokhorst, M66B
Tags: security, admin, authentication, access, widget, login, shortcode, comment, comments, discussion, bbPress, bbPress 2.0, browserid, mozilla, persona
Requires at least: 3.1
Tested up to: 3.6
Stable tag: 0.48
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.htm

Implementation of Mozilla Persona (BrowserID) for WordPress

== Description ==

[Mozilla Persona](https://login.persona.org/ "Mozilla Persona") is an open source identity system from the [Identity Team](http://identity.mozilla.com/ "Identity Team") at [Mozilla](https://mozilla.org/ "Mozilla"). More information on Persona can be found at [https://login.persona.org/about](https://login.persona.org/about).

This plugin allows users to sign up, sign in, and comment on your site using Persona.

** Reporting problems: **

Please report any issues on [GitHub](https://github.com/shane-tomlinson/browserid-wordpress/issues).

** Beta features: **

* [bbPress 2](http://bbpress.org/ "bbPress") integration: create topics / reply with Mozilla Persona


== Installation ==

*Using the WordPress dashboard*

1. Login to your weblog
1. Go to Plugins
1. Select Add New
1. Search for Mozilla Persona
1. Select Install
1. Select Install Now
1. Select Activate Plugin

*Manual*

1. Download and unzip the plugin
1. Upload the entire *browserid/* directory to the */wp-content/plugins/* directory
1. Activate the plugin through the Plugins menu in WordPress

== Frequently Asked Questions ==

= Where can I find out more about Persona? =
https://login.persona.org/about

= What is 'Custom login HTML for?' =
Try putting the following into this option:

`<img src="https://login.persona.org/i/persona_sign_in_red.png" />`

Now you will see a red 'Sign in with Persona' button instead of the traditional CSS button.

= Which server verifies the assertion? =

The assertion is verified by the server at https://login.persona.org/verify.

= I get 'Login failed' =

Only users that registered before can login. The e-mail address used for Mozilla Persona should match the e-mail address registered with.

= I get 'Verification failed' =

Are you cheating?
If there isn't an error message, turn on debug mode to see the complete response.

= I get 'Verification void' =

Something went terribly wrong.
If there isn't an error message, turn on debug mode to see the complete response.

= Where can I ask questions, report bugs and request features? =

You can write comments on [GitHub](https://github.com/shane-tomlinson/browserid-wordpress/issues).

== Screenshots ==

1. WordPress login with Persona
2. Wordpress login with "Disable Non-Persona authentication"
3. Login widget
4. Comment with Persona
5. Persona dialog using siteName, siteLogo and backgroundColor

== Getting Involved ==

== Maintainers ==
* [Shane Tomlinson](https://shanetomlinson.com) - shane@shanetomlinson.com or stomlinson@mozilla.com
* [Marcel Bokhorst](http://blog.bokhorst.biz)


== Changelog ==

= Development version =
* ...

Follow these steps to install the development version:

* Download the development version by clicking on [this link](http://downloads.wordpress.org/plugin/browserid.zip)
* Go to *Plugins* on your WordPress dashboard
* *Deactivate* Mozilla Persona
* *Delete* Mozilla Persona (*Yes, delete these files*)
* Click *Add New*
* Click *Upload* (a link at the top)
* Click *Choose file* and select the file you downloaded before
* Click *Install*, then *Activate Plugin*

= 0.48 =
* Bug Fix: Allow signed in users to comment without using Persona. Allow comments from admin panel.

= 0.47 =
* Bug Fix: Disable error reporting - thanks @jonchang!

= 0.46 =
* New Feature: Use the WordPress color picker when selecting a background color - Thanks @janw-oostendorp!
* New Feature: Use the WordPress media picker when selecting the site logo, terms of service and privacy policy.
* New Feature: Automatically convert site logo's into dataURIs so that any site can specify a logo.
* New Feature: Japanese Translations - Thanks @makotokato!
* Improvement: Separate general and advanced settings.
* Improvement: Serve minified Javascript and CSS by default.
* Improvement: massive refactor to make code easier to browse.
* Bug Fix: Fix typo in Privacy Policy description - Thanks @KryDos!
* Bug Fix: Make sure URLs are written to browserid_common.js unescaped.

= 0.45 =
* New Feature: Russian Translations - Thanks Ruslan Bekenev (@KryDos)!
* New Feature: French (CA and FR) - Thanks Fabian Rodriguez (@MagicFab)!
* New Feature: backgroundColor support!
* New Feature: termsOfService and privacyPolicy support!
* New Feature: Select one of 3 Persona button styles
* Improvement: Localize widget buttons
* Improvement: Updated Dutch translation - Thanks @janw-oostendorp!
* Bug Fix: Prevent comments being accepted without assertion
* Bug Fix: Admins can add new users
* Bug Fix: Use Persona button for comments
* Bug Fix: Fix live events not working with jQuery 1.9+ - Thanks @davidmurdoch!
* Bug Fix: Get rid of the warning on the Persona settings page - Thanks @KryDos

= 0.44 =
* Improvement: Commenting for new Persona users is simpler
* Improvement: New member registration with new Persona users is simpler
* Improvement: Separate CSS into its own file for maintainability
* Improvement: Replace .png signin buttons with localizable CSS buttons
* Improvement: Pre-fill input fields with default values in configuration screen
* Improvement: Do not show the lost password link if "Disable non-Persona auth" is selected
* Improvement: Do not show the "default password nag" if "Disable non-Persona auth" is selected
* Improvement: Code Cleanup.
* New feature: A .PO file with all strings has been created for localization
* New Feature: Spanish translations. Thanks Guillermo Movia!
* Bug Fix: site name can now contain ' and &
* Bug Fix: no more static warnings in strict PHP mode
* Bug Fix: remove plugin options from database when de-activated
* Bug Fix: incorrect button link for example button link in the FAQ

= 0.43 =
* Continue with 0.41
* Bug Fix: Fix the missing arguments errors
* Bug Fix: HTML Escape the hostname when printing debug information
* Bug Fix: Logout link from the widget signs the user out of Persona
* Security Improvement: Remove the "Noverify SSL Certificate" option

= 0.42 =
* Revert to 0.37

= 0.41 =
* Bug Fix: Fix the "missing arguments" error due to not declaring the number of expected variables to Set_auth_cookie_action.

= 0.40 =
* New Feature: Add option to disable normal username/password auth.
* Improvement: Convert from navigator.id.get to navigator.id.watch/.request API.
* * New Feature: If user signs out of Persona, they are signed out of the Wordpress site as well.
* New Feature: Easier user signup when using Persona - no email verification required.
* Improvement: Better comment integration, especially for new users.
* Improvement: Update the login/logout widget to match styling of other Wordpress widgets.
* Improvement: Add a "Settings" link to the BrowserID list item in the plugins list.
* Bug Fix: Fix a bug where server clock skew from the Persona servers could prevent users from signing in.
* Improvement: Update "Sign in" buttons to use the new Persona button style.
* Improvement: Unify signin and comment Javascript.

= 0.37 =
* Bump version number for new maintainer info.

= 0.36 =
* Bugfix: *browserid_error*

= 0.35 =
* Bugfix: redirect option, thanks *Lwangaman*!

= 0.34 =
* Added Italian translation by [John R. D'Orazio](http://johnromanodorazio.blogspot.it/ "John R. D'Orazio")

= 0.33 =
* Updated URL to verification server
* Updated Mozilla CA certificates

= 0.32 =
* Fixed notices
* Updated French translation

= 0.31 =
* Renamed Mozilla BrowserID into Mozilla Persona
* New feature: site name/logo in login dialog
* Both by [Shane Tomlinson](https://shanetomlinson.com/), thanks!
* Added French translation
* Updated Dutch and Flemish translations
* Tested with WordPress 3.4.1

= 0.29 =
* Added Swedish (sv\_SE) translation
* Improvement: load scripts at footer by *Marvin Rühe*
* Tested with WordPress 3.4

= 0.28 =
* Improvement: POST assertion by *Marvin Rühe*
* Improvement: included Mozilla CA certificates
* Improvement: included BrowserID logo
* New feature: login button localization
* Added German Translation by *Marvin Rühe*

= 0.27 =
* Bugfix: remember me

= 0.26 =
* New feature: BrowserID for comments (beta, option)
* New feature: bbPress integration (beta, option)
* Improvement: added title/class to BrowserID buttons
* Improvement: files instead of inline JavaScript script
* Improvement: added 'What is?' link
* Improvement: more debug info
* Updated Dutch and Flemish translations
* Updated Norwegian (nb\_NO) translation by [Stein Ivar Johnsen](http://www.idyrøy.no/ "Stein Ivar Johnsen")

= 0.25 =
* Improvement: store debug info only when debugging enabled
* Improvement: add trailing slash to site URL
* Improvement: respect login redirect to parameter
* Improvement: better error messages
* Thanks to [mitcho](http://mitcho.com "mitcho") for the suggestions and testing!

= 0.24 =
* Removed [Sustainable Plugins Sponsorship Network](http://pluginsponsors.com/)

= 0.23 =
* Improvement: compatibility with WordPress 3.3

= 0.22 =
* Re-release of version 0.21, because of a bug in wordpress.org

= 0.21 =
* Bugfix: renamed *valid-until* into *expires*
* Improvement: fixed a few notices

= 0.20 =
* Bugfix: shortcode still not working

= 0.19 =
* Bugfix: widget, shortcode, template tag not working

= 0.18 =
* Improvement: workaround for bug in Microsoft IIS

= 0.17 =
* Improvement: applying filter *login_redirect*

= 0.16 =
* Improvement: only load BrowserID script on login page

= 0.15 =
* **Protocol change**: verification with POST instead of GET
* Improvement: no logout link on login page
* Updated Dutch and Flemish translations
* Updated Norwegian (nb\_NO) translation by [Stein Ivar Johnsen](http://www.idyrøy.no/ "Stein Ivar Johnsen")

= 0.14 =
* New feature: option to redirect to set URL after login

= 0.13 =
* Bug fix: correctly handling WordPress errors

= 0.12 =
* Improvement: check issuer
* Improvement: more debug info

= 0.11 =
* Fixed IDN

= 0.9 =
* New feature: shortcode for login/out button/link: *[mozilla_persona]*
* New feature: template tag for login/out button/link: *mozilla_persona*
* Updated Dutch and Flemish translations
* Updated Norwegian (nb\_NO) translation by [Stein Ivar Johnsen](http://www.idyrøy.no/ "Stein Ivar Johnsen")

= 0.8 =
* New feature: option to set verification server
* Improvement: checking assertion valid until time (can be switch off with an option)
* Improvement: using [idn_to_utf8](http://php.net/manual/en/function.idn-to-utf8.php "idn_to_utf8") when available
* Updated FAQ
* Updated Dutch and Flemish translations

= 0.7 =
* New feature: support for *Remember Me* check box
* Updated Norwegian (nb\_NO) translation by [Stein Ivar Johnsen](http://www.idyrøy.no/ "Stein Ivar Johnsen")

= 0.6 =
* New feature: option *Do not verify SSL certificate*
* Updated Dutch and Flemish translations

= 0.5 =
* Improvement: more debug info
* Tested with WordPress 3.1

= 0.4 =
* Bug fix: using site URL in stead of home URL
* Updated FAQ

= 0.3 =
* Improvement: better error messages
* Improvement: more debug info
* Improvement: support for [internationalized domain names](http://en.wikipedia.org/wiki/Internationalized_domain_name "IDN")
* Updated FAQ
* Added Norwegian (nb\_NO) translation by [Stein Ivar Johnsen](http://www.idyrøy.no/ "Stein Ivar Johnsen"), thanks!

= 0.2 =
* Bugfix: custom HTML for login page
* Added Flemish translation
* Updated Dutch translation

= 0.1 =
* Initial version

= 0.0 =
* Development version

== Upgrade Notice ==

= 0.45 =
Russian, French, Dutch translations. backgroundColor, termsOfService and privacyPolicy support. Multiple Persona button styles. Multiple bug fixes.

= 0.44 =
Spanish translations, 8 improvements, 4 bug fixes

= 0.43 =
Security improvement, three bug fixes

= 0.42 =
Revert to v0.37 until update process is figured out

= 0.41 =
Bug fix for "missing arguments" error

= 0.40 =
Three new features, six improvements, one bug fix

= 0.37 =
Maintainer change - info update

= 0.36 =
One bugfix

= 0.33 =
Updated URL to verification server

= 0.32 =
Fixed notices

= 0.31 =
Renamed Mozilla BrowserID into Mozilla Persona

= 0.29 =
One improvement, one new translation

= 0.28 =
One new feature, three improvements

= 0.27 =
One bugfix

= 0.26 =
Two new features, four improvements, translation updates

= 0.25 =
Four improvements

= 0.24 =
Compliance

= 0.23 =
Compatibility

= 0.21 =
One bugfix, one improvement

= 0.20 =
One bugfix

= 0.19 =
One bugfix

= 0.18 =
One improvement

= 0.17 =
One improvement

= 0.16 =
One improvement

= 0.15 =
Protocol change! Verification with POST instead of GET

= 0.14 =
One new feature

= 0.13 =
One bugfix

= 0.12 =
Two improvements

= 0.11 =
Fixed IDN

= 0.9 =
Two new features, translation update

= 0.8 =
One new feature, two improvements

= 0.7 =
One new feature

= 0.6 =
One new feature

= 0.5 =
One improvement

= 0.4 =
Bugfix

= 0.3 =
Three improvements

= 0.2 =
One bugfix

= 0.1 =
First public release

== Acknowledgments ==
* [Marcel Bokhorst](http://blog.bokhorst.biz/) is the original author of this plugin. His awesome work has allowed me to continue.
* [Guillermo Movia](mailto://deimidis@mozilla-hispano.org) for Spanish translations.
* [Ruslan Bekenev - @KryDos](https://github.com/KryDos) for Russian translations, bug fixes, and continued support.
* [Fabian Rodriguez - @MagicFab](https://github.com/MagicFab) for French and Canadian French translations as well as man bug reports.
* [Edwin Wong @edmoz](http://www.edwinsf.com/blog/) for continued testing.
* [@janw-oostendorp](https://github.com/janw-oostendorp) for updated Dutch translations.
* [David Murdoch @davidmurdoch](https://github.com/davidmurdoch/) fixing jQuery 1.9+ compatability
* [Makoto Kato @makotokato](https://github.com/makotokato) for Japanese translations.
* [Johnathan Chang @jonchang](https://github.com/jonchang) for patch to disable  error reporting.


This plugin uses:

* The client side [Mozilla Persona script](https://login.persona.org/include.js "Mozilla Persona script")
