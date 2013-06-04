=== Mozilla Persona (BrowserID) ===
Contributors: stomlinson, Marcel Bokhorst, M66B
Tags: security, admin, authentication, access, widget, login, shortcode, comment, comments, discussion, bbPress, bbPress 2.0, browserid, mozilla, persona
Requires at least: 3.1
Tested up to: 3.5.1
Stable tag: 0.37

Implementation of Mozilla Persona (BrowserID) for WordPress

== Description ==

[Mozilla Persona](https://login.persona.org/ "Mozilla Persona") is an open source identity system from the [Identity Team](http://identity.mozilla.com/ "Identity Team") at [Mozilla](https://mozilla.org/ "Mozilla").

"*As a user of Mozilla Persona (BrowserID), you confirm your email addresses once. Then, you can sign into any web site that supports Mozilla Persona with just two clicks.*"

This plugin allows users to sign into your site using Mozilla Persona. A widget, shortcode and template tags are provided. Custom login and logout links can be created.

**Beta features:**

* Submit comments with Mozilla Persona
* [bbPress 2](http://bbpress.org/ "bbPress") integration: create topics / reply with Mozilla Persona

** Reporting problems:**

Please report any issues on [GitHub](https://github.com/shane-tomlinson/browserid-wordpress/issues).

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

= What is 'Custom login HTML for?' =

Try putting the following into this option:

`<img src="https://login.persona.org/i/browserid_logo_sm.png" />`

Now you will see the Mozilla Persona logo instead of the login button.

= Which server verifies the assertion? =

The assertion is verified by the server at https://login.persona.org/verify.

= I get 'SSL certificate problem, verify that the CA cert is OK' =

Your hosting provider should take a look at the SSL certificates.
You can check the option *Do not verify SSL certificate*, but please realize this isn't entirely safe.

= I get 'Bad Gateway' =

The login.persona.org service is still in testing phase.
Trying again may help.

= I get 'Login failed' =

Only users that registered before can login.
The e-mail address used for Mozilla Persona should match the e-mail address registered with.

= I get 'Verification failed' =

Are you cheating?
If there isn't an error message, turn on debug mode to see the complete response.

= I get 'Verification void' =

Something went terribly wrong.
If there isn't an error message, turn on debug mode to see the complete response.

= I get 'Verification invalid' =

Maybe the time of your hosting server is incorrect.
You could check the option *Do not check valid until time* to solve this.

= Where can I ask questions, report bugs and request features? =

You can write comments on [GitHub](https://github.com/shane-tomlinson/browserid-wordpress/issues).

== Screenshots ==

1. Mozilla Persona logo
1. Mozilla Persona login button
1. WordPress login dialog

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
One new feature, threee improvements

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
[Marcel Bokhorst](http://blog.bokhorst.biz/) is the original author of this plugin. His awesome work has allowed me to continue.


This plugin uses:

* The client side [Mozilla Persona script](https://login.persona.org/include.js "Mozilla Persona script")
