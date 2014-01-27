=== Plugin Name ===
Contributors: fredericktownes
Tags: wpo, web performance optimization, performance, availability, scaling, scalability, user experience, cache, caching, page cache, css cache, js cache, db cache, disk cache, disk caching, database cache, http compression, gzip, deflate, minify, cdn, content delivery network, media library, performance, speed, multiple hosts, css, merge, combine, unobtrusive javascript, compress, optimize, optimizer, javascript, js, cascading style sheet, plugin, yslow, yui, google, google rank, google page speed, mod_pagespeed, new relic, newrelic, aws, s3, cloudfront, sns, elasticache, rds, flash media server, amazon web services, cloud files, rackspace, akamai, max cdn, netdna, limelight, cloudflare, mod_cloudflare, microsoft, microsoft azure, iis, nginx, litespeed, apache, varnish, xcache, apc, eacclerator, wincache, mysql, w3 total cache, batcache, wp cache, wp super cache, quick cache, wp minify, bwp-minify, buddypress
Requires at least: 3.2
Tested up to: 3.5.1
Stable tag: 0.9.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easy Web Performance Optimization (WPO) using caching: browser, page, object, database, minify and content delivery network support.

== Description ==

The **only** WordPress Performance Optimization (WPO) framework; designed to improve user experience and page speed.

Recommended by web hosts like: Page.ly, Synthesis, DreamHost, MediaTemple, Go Daddy, Host Gator and countless more.

Trusted by countless companies like: AT&T, stevesouders.com, mattcutts.com, mashable.com, smashingmagazine.com, makeuseof.com, yoast.com, kiss925.com, pearsonified.com, lockergnome.com, johnchow.com, ilovetypography.com, webdesignerdepot.com, css-tricks.com and tens of thousands of others.

W3 Total Cache improves the user experience of your site by increasing server performance, reducing the download times and providing transparent content delivery network (CDN) integration.

An inside look:

http://www.youtube.com/watch?v=rkmrQP8S5KY

Benefits:

* At least 10x improvement in overall site performance (Grade A in [YSlow](http://developer.yahoo.com/yslow/) or significant [Google Page Speed](http://code.google.com/speed/page-speed/) improvements) **when fully configured**
* Improved conversion rates and "[site performance](http://googlewebmastercentral.blogspot.com/2009/12/your-sites-performance-in-webmaster.html)" which [affect your site's rank](http://googlewebmastercentral.blogspot.com/2010/04/using-site-speed-in-web-search-ranking.html) on Google.com
* "Instant" subsequent page views: browser caching
* Optimized progressive render: pages start rendering quickly
* Reduced page load time: increased visitor time on site; visitors view more pages
* Improved web server performance; sustain high traffic periods
* Up to 80% bandwidth savings via minify and HTTP compression of HTML, CSS, JavaScript and feeds

Features:

* Compatible with shared hosting, virtual private / dedicated servers and dedicated servers / clusters
* Transparent content delivery network (CDN) management with Media Library, theme files and WordPress itself
* Mobile support: respective caching of pages by referrer or groups of user agents including theme switching for groups of referrers or user agents
* Caching of (minified and compressed) pages and posts in memory or on disk or on CDN (mirror only)
* Caching of (minified and compressed) CSS and JavaScript in memory, on disk or on CDN
* Caching of feeds (site, categories, tags, comments, search results) in memory or on disk or on CDN (mirror only)
* Caching of search results pages (i.e. URIs with query string variables) in memory or on disk
* Caching of database objects in memory or on disk
* Caching of objects in memory or on disk
* Minification of posts and pages and feeds
* Minification of inline, embedded or 3rd party JavaScript (with automated updates)
* Minification of inline, embedded or 3rd party CSS (with automated updates)
* Browser caching using cache-control, future expire headers and entity tags (ETag) with "cache-busting"
* JavaScript grouping by template (home page, post page etc) with embed location control
* Non-blocking JavaScript embedding
* Import post attachments directly into the Media Library (and CDN)
* WP-CLI support for cache purging, query string updating and more

Improve the user experience for your readers without having to change WordPress, your theme, your plugins or how you produce your content.

== Frequently Asked Questions ==

= Why does speed matter? =

Speed is among the most significant success factors web sites face. In fact, your site's speed directly affects your income (revenue) &mdash; it's a fact. Some high traffic sites conducted research and uncovered the following:

* Google.com: **+500 ms** (speed decrease) -> **-20% traffic loss** [[1](http://home.blarg.net/~glinden/StanfordDataMining.2006-11-29.ppt)]
* Yahoo.com: **+400 ms** (speed decrease) -> **-5-9% full-page traffic loss** (visitor left before the page finished loading) [[2](http://www.slideshare.net/stoyan/yslow-20-presentation)]
* Amazon.com: **+100 ms** (speed decrease) -> **-1% sales loss** [[1](http://home.blarg.net/~glinden/StanfordDataMining.2006-11-29.ppt)]

A thousandth of a second is not a long time, yet the impact is quite significant. Even if you're not a large company (or just hope to become one), a loss is still a loss. However, there is a solution to this problem, take advantage.

Search engines like Google, measure and factor in the speed of web sites in their ranking algorithm. When they recommend a site they want to make sure users find what they're looking for quickly. So in effect you and Google should have the same objective.

Many of the other consequences of poor performance were discovered more than a decade ago:

* Lower perceived credibility (Fogg et al. 2001)
* Lower perceived quality (Bouch, Kuchinsky, and Bhatti 2000)
* Increased user frustration (Ceaparu et al. 2004)
* Increased blood pressure (Scheirer et al. 2002)
* Reduced flow rates (Novak, Hoffman, and Yung 200)
* Reduced conversion rates (Akamai 2007)
* Increased exit rates (Nielsen 2000)
* Are perceived as less interesting (Ramsay, Barbesi, and Preece 1998)
* Are perceived as less attractive (Skadberg and Kimmel 2004)

There are a number of [resources](http://www.websiteoptimization.com/speed/tweak/psychology-web-performance/) that have been documenting the role of performance in success on the web, W3 Total Cache exists to give you a framework to tune your application or site without having to do years of research.

= Why is W3 Total Cache better than other cache plugins? =

**It's a complete framework.** Most cache plugins available do a great job at achieving a couple of performance aims. Our plugin remedies numerous performance reducing aspects of any web site going far beyond merely reducing CPU usage (load) and bandwidth consumption for HTML pages alone. Equally important, the plugin requires no theme modifications, modifications to your .htaccess (mod_rewrite rules) or programming compromises to get started. Most importantly, it's the only plugin designed to optimize all practical hosting environments small or large. The options are many and setup is easy.

= I've never heard of any of this stuff; my site is fine, no one complains about the speed. Why should I install this? =

Rarely do readers take the time to complain. They typically just stop browsing earlier than you'd prefer and may not return altogether. This is the only plugin specifically designed to make sure that all aspects of your site are as fast as possible. Google is placing more emphasis on the [speed of a site as a factor in rankings](http://searchengineland.com/site-speed-googles-next-ranking-factor-29793); this plugin helps with that too.

It's in every web site owner's best interest is to make sure that the performance of your site is not hindering its success.

= Which WordPress versions are supported? =

To use all features in the suite, a minimum of version WordPress 2.8 with PHP 5 is required. Earlier versions will benefit from our Media Library Importer to get them back on the upgrade path and into a CDN of their choosing.

= Why doesn't minify work for me? =

Great question. W3 Total Cache uses several open source tools to attempt to combine and optimize CSS, JavaScript and HTML etc. Unfortunately some trial and error is required on the part of developers is required to make sure that their code can be successfully minified with the various libraries W3 Total Cache supports. Even still, if developers do test their code thoroughly, they cannot be sure that interoperability with other code your site may have. This fault does not lie with any single party here, because there are thousands of plugins and theme combinations that a given site can have, there are millions of possible combinations of CSS, JavaScript etc.

A good rule of thumb is to try auto mode, work with a developer to identify the code that is not compatible and start with combine only mode (the safest optimization) and increase the optimization to the point just before functionality (JavaScript) or user interface / layout (CSS) breaks in your site.

We're always working to make this more simple and straight forward in future releases, but this is not an undertaking we can realize on our own. When you find a plugin, theme or file that is not compatible with minification reach out to the developer and ask them either to provide a minified version with their distribution or otherwise make sure their code is minification-friendly.

= Who do you recommend as a CDN (Content Delivery Network) provider? =

That depends on how you use your site and where most of your readers read your site (regionally). Here's a short list:

* [MaxCDN](http://www.maxcdn.com/), [Discount Coupon Code](http://tracking.maxcdn.com/c/15753/3982/378?u=https%3A%2F%2Fsecure.maxcdn.com%2F%3Fpackage%3Dstarter%26coupon%3Dw3tc)
* [EdgeCast / MediaTemple ProCDN](http://www.edgecast.com/)
* [Amazon Cloudfront](http://aws.amazon.com/cloudfront/)
* [Rackspace Cloud Files](http://www.rackspace.com/cloud/files/)
* [Limelight Networks](http://www.limelight.com/)
* [Akamai](http://www.akamai.com/)

= What about comments? Does the plugin slow down the rate at which comments appear? =

On the contrary, as with any other action a user can perform on a site, faster performance will encourage more of it. The cache is so quickly rebuilt in memory that it's no trouble to show visitors the most current version of a post that's experiencing Digg, Slashdot, Drudge Report, Yahoo Buzz or Twitter effect.

= Will the plugin interfere with other plugins or widgets? =

No, on the contrary if you use the minify settings you will improve their performance by several times.

= Does this plugin work with WordPress in network mode? =

Indeed it does.

= Does this plugin work with BuddyPress (bbPress)? =

Yes.

= Will this plugin speed up WP Admin? =

Yes, indirectly - if you have a lot of bloggers working with you, you will find that it feels like you have a server dedicated only to WP Admin once this plugin is enabled; the result, increased productivity.

= Which web servers do you support? =

We are aware of no incompatibilities with [apache](http://httpd.apache.org/) 1.3+, [IIS](http://www.iis.net/) 5+ or [litespeed](http://litespeedtech.com/products/webserver/overview/) 4.0.2+. If there's a web server you feel we should be actively testing (e.g. [lighttpd](http://www.lighttpd.net/)), we're [interested in hearing](http://www.w3-edge.com/contact/).

= Is this plugin server cluster and load balancer friendly? =

Yes, built from the ground up with scale and current hosting paradigms in mind.

= What is the purpose of the "Media Library Import" tool and how do I use it? =

The media library import tool is for old or "messy" WordPress installations that have attachments (images etc in posts or pages) scattered about the web server or "hot linked" to 3rd party sites instead of properly using the media library.

The tool will scan your posts and pages for the cases above and copy them to your media library, update your posts to use the link addresses and produce a .htaccess file containing the list of of permanent redirects, so search engines can find the files in their new location.

You should backup your database before performing this operation.

= How do I find the JS and CSS to optimize (minify) them with this plugin? =

Use the "Help" button available on the Minify settings tab. Once open, the tool will look for and populate the CSS and JS files used in each template of the site for the active theme. To then add a file to the minify settings, click the checkbox next to that file. The embed location of JS files can also be specified to improve page render performance. Minify settings for all installed themes can be managed from the tool as well by selecting the theme from the drop down menu. Once done configuring minify settings, click the apply and close button, then save settings in the Minify settings tab.

= I don't understand what a CDN has to do with caching, that's completely different, no? =

Technically no, a CDN is a high performance cache that stores static assets (your theme files, media library etc) in various locations throughout the world in order to provide low latency access to them by readers in those regions.

= What if I don't want to work with a CDN right now, is there any other use for this feature? =

Yes! You can take advantage of the [pipelining](http://www.mozilla.org/projects/netlib/http/pipelining-faq.html) support in some browsers by creating a sub-domain for the static content for your site. So you could select the "Origin Push / Self-hosted" method of the General Settings tab. Create static.domain.com on your server (and update your DNS zone) and then specify the FTP details for it in the plugin configuration panel and you're done. If you disable the scripting options on your server you'll find that your server will actually respond slightly faster from that sub-domain because it's just sending files and not processing them.

= How do I use an Origin Pull (Mirror) CDN? =
Login to your CDN providers control panel or account management area. Following any set up steps they provide, create a new "pull zone" or "bucket" for your site's domain name. If there's a set up wizard or any troubleshooting tips your provider offers, be sure to review them. In the CDN tab of the plugin, enter the hostname your CDN provider provided in the "replace site's hostname with" field. You should always do a quick check by opening a test file from the CDN hostname, e.g. http://cdn.domain.com/favicon.ico. Troubleshoot with your CDN provider until this test is successful.

Now go to the General tab and click the checkbox and save the settings to enable CDN functionality and empty the cache for the changes to take effect.

= How do I configure Amazon Simple Storage Service (Amazon S3) or Amazon CloudFront as my CDN? =

First [create an S3 account](http://aws.amazon.com/); it may take several hours for your account credentials to be functional. Next, you need to obtain your "Access key ID" and "Secret key" from the "Access Credentials" section of the "[Security Credentials](http://aws-portal.amazon.com/gp/aws/developer/account/index.html?action=access-key)" page of "My Account." Make sure the status is "active." Next, make sure that "Amazon Simple Storage Service (Amazon S3)" is the selected "CDN type" on the "General Settings" tab, then save the changes. Now on the "Content Delivery Network Settings" tab enter your "Access key," "Secret key" and enter a name (avoid special characters and spaces) for your bucket in the "Create a bucket" field by clicking the button of the same name. If using an existing bucket simply specify the bucket name in the "Bucket" field. Click the "Test S3 Upload" button and make sure that the test is successful, if not check your settings and try again. Save your settings.

Unless you wish to use CloudFront, you're almost done, skip to the next paragraph if you're using CloudFront. Go to the "General Settings" tab and click the "Enable" checkbox and save the settings to enable CDN functionality. Empty the cache for the changes to take effect. If preview mode is active you will need to "deploy" your changes for them to take effect.

To use CloudFront, perform all of the steps above, except select the "Amazon CloudFront" "CDN type" in the "Content Delivery Network" section of the "General Settings" tab. When creating a new bucket, the distribution ID will automatically be populated. Otherwise, proceed to the [AWS Management Console](https://console.aws.amazon.com/cloudfront/) and create a new distribution: select the S3 Bucket you created earlier as the "Origin," enter a [CNAME](http://docs.amazonwebservices.com/AmazonCloudFront/latest/DeveloperGuide/index.html?CNAMEs.html) if you wish to add one or more to your DNS Zone. Make sure that "Distribution Status" is enabled and "State" is deployed. Now on "Content Delivery Network" tab of the plugin, copy the subdomain found in the AWS Management Console and enter the CNAME used for the distribution in the "CNAME" field.

You may optionally, specify up to 10 hostnames to use rather than the default hostname, doing so will improve the render performance of your site's pages. Additional hostnames should also be specified in the settings for the distribution you're using in the AWS Management Console.

Now go to the General tab and click the "Enable" checkbox and save the settings to enable CDN functionality and empty the cache for the changes to take effect. If preview mode is active you will need to "deploy" your changes for them to take effect.

= How do I configure Rackspace Cloud Files as my CDN? =

First [create an account](http://www.rackspacecloud.com/cloud_hosting_products/files). Next, in the "Content Delivery Network" section of the "General Settings" tab, select Rackspace Cloud Files as the "CDN Type." Now, in the "Configuration" section of the "Content Delivery Network" tab, enter the "Username" and "API key" associated with your account (found in the API Access section of the [rackspace cloud control panel](https://manage.rackspacecloud.com/APIAccess.do)) in the respective fields. Next enter a name for the container to use (avoid special characters and spaces). If the operation is successful, the container's ID will automatically appear in the "Replace site's hostname with" field. You may optionally, specify the container name and container ID of an [existing container](https://manage.rackspacecloud.com/CloudFiles.do) if you wish. Click the "Test Cloud Files Upload" button and make sure that the test is successful, if not check your settings and try again. Save your settings. You're now ready to export your media library, theme and any other files to the CDN.

You may optionally, specify up to 10 hostnames to use rather than the default hostname, doing so will improve the render performance of your site's pages.

Now go to the General tab and click the "Enable" checkbox and save the settings to enable CDN functionality and empty the cache for the changes to take effect.  If preview mode is active you will need to "deploy" your changes for them to take effect.

= My YSlow score is low because it doesn't recognize my CDN, what can I do? =

Rule 2 says to use a content delivery network (CDN). The score for this rule is computed by checking the hostname of each component against the list of known CDNs. Unfortunately, the list of "known CDNs" are the ones used by Yahoo!. Most likely these are not relevant to your web site, except for potentially yui.yahooapis.com. If you want an accurate score for your web site, you can add your CDN hostnames to YSlow using Firefox's preferences. Here are the steps to follow:

* Go to about:config in Firefox. You'll see the current list of preferences.
* Right-click in the window and choose New and String to create a new string preference.
* Enter extensions.yslow.cdnHostnames for the preference name.
* For the string value, enter the hostname of your CDN, for example, mycdn.com. Do not use quotes. If you have multiple CDN hostnames, separate them with commas.

If you specify CDN hostnames in your preferences, they'll be shown under the details for Rule 2 in the Performance view.

= What is the purpose of the "modify attachment URLs" button? =

If the domain name of your site has changed, this tool is useful in updating your posts and pages to use the current addresses. For example, if your site used to be www.domain.com, and you decided to change it to domain.com, the result would either be many "broken" images or many unnecessary redirects (which slow down the visitor's browsing experience). You can use this tool to correct this and similar cases. Correcting the URLs of your images also allows the plugin to do a better job of determining which images are actually hosted with the CDN.

As always, it never hurts to back up your database first.

= Is this plugin comptatible with TDO Mini Forms? =

Captcha and recaptcha will work fine, however you will need to prevent any pages with forms from being cached. Add the page's URI to the "Never cache the following pages" box on the Page Cache Settings tab.

= Is this plugin comptatible with GD Star Rating? =

Yes. Follow these steps:

1. Enable dynamic loading of ratings by checking GD Star Rating -> Settings -> Features "Cache support option"
1. If Database cache enabled in W3 Total Cache add `wp_gdsr` to "Ignored query stems" field in the Database Cache settings tab, otherwise ratings will not updated after voting
1. Empty all caches

= I see garbage characters instead of the normal web site, what's going on here? =

If a theme or it's files use the call `php_flush()` or function `flush()` that will interfere with the plugins normal operation; making the plugin send cached files before essential operations have finished. The `flush()` call is no longer necessary and should be removed.

= How do I cache only the home page? =

Add `/.+` to page cache "Never cache the following pages" option on the page cache settings tab.

= I'm getting blank pages or 500 error codes when trying to upgrade on WordPress in network mode =

First, make sure the plugin is not active (disabled) network-wide. Then make sure it's deactivated network-wide. Now you should be able to successful upgrade without breaking your site.

= A notification about file owner appears along with an FTP form, how can I resolve this? =

The plugin uses WordPress FileSystem functionality to write to files. It checks if the file owner, file owner group of created files match process owner. If this is not the case it cannot write or modify files.

Typically, you should tell your web host about the permission issue and they should be able to resolve it.

You can however try adding <em>define('FS_METHOD', 'direct');</em> to wp-config.php to circumvent the file and folder checks.

= This is too good to be true, how can I test the results? =
You will be able to see it instantly on each page load, but for tangible metrics, consider the following tools:

* [Mozilla Firefox](http://www.mozilla.com/firefox/) + [Firebug](http://getfirebug.com/) + [Yahoo! YSlow](http://developer.yahoo.com/yslow/)
* [Mozilla Firefox](http://www.mozilla.com/firefox/) + [Firebug](http://getfirebug.com/) + [Google Page Speed](http://code.google.com/speed/page-speed/)
* [Mozilla Firefox](http://www.mozilla.com/firefox/) + [Firebug](http://getfirebug.com/) + [Hammerhead](http://stevesouders.com/hammerhead/)
* [Google Chrome](http://www.google.com/chrome) + [Google Speed Tracer](http://code.google.com/webtoolkit/speedtracer/)
* [Pingdom](http://tools.pingdom.com/)
* [WebPagetest](http://www.webpagetest.org/test)
* [Gomez Instant Test Pro](http://www.gomez.com/instant-test-pro/)
* [Resource Expert Droid](http://redbot.org/)
* [Web Caching Tests](http://www.procata.com/cachetest/)
* [Port80 Compression Check](http://www.port80software.com/tools/compresscheck.asp)
* [A simple online web page compression / deflate / gzip test tool](http://www.gidnetwork.com/tools/gzip-test.php)

= I don't have time to deal with this, but I know I need it. Will you help me? =

Yes! Please [reach out to us](http://www.w3-edge.com/contact/) and we'll get you acclimated so you can "set it and forget it."

Install the plugin to read the full FAQ on the plugins FAQ tab.

== Installation ==

1. Deactivate and uninstall any other caching plugin you may be using. Pay special attention if you have customized the rewrite rules for fancy permalinks, have previously installed a caching plugin or have any browser caching rules as W3TC will automate management of all best practices. Also make sure wp-content/ and wp-content/uploads/ (temporarily) have 777 permissions before proceeding, e.g. in the terminal: `# chmod 777 /var/www/vhosts/domain.com/httpdocs/wp-content/` using your web hosting control panel or your FTP / SSH account.
1. Login as an administrator to your WordPress Admin account. Using the "Add New" menu option under the "Plugins" section of the navigation, you can either search for: w3 total cache or if you've downloaded the plugin already, click the "Upload" link, find the .zip file you download and then click "Install Now". Or you can unzip and FTP upload the plugin to your plugins directory (wp-content/plugins/). In either case, when done wp-content/plugins/w3-total-cache/ should exist.
1. Locate and activate the plugin on the "Plugins" page. Page caching will **automatically be running** in basic mode. Set the permissions of wp-content and wp-content/uploads back to 755, e.g. in the terminal: `# chmod 755 /var/www/vhosts/domain.com/httpdocs/wp-content/`.
1. Now click the "Settings" link to proceed to the "General Settings" tab; in most cases, "disk enhanced" mode for page cache is a "good" starting point.
1. The "Compatibility Mode" option found in the advanced section of the "Page Cache Settings" tab will enable functionality that optimizes the interoperablity of caching with WordPress, is disabled by default, but highly recommended. Years of testing in hundreds of thousands of installations have helped us learn how to make caching behave well with WordPress. The tradeoff is that disk enhanced page cache performance under load tests will be decreased by ~20% at scale.
1. *Recommended:* On the "Minify Settings" tab, all of the recommended settings are preset. If auto mode causes issues with your web site's layout, switch to manual mode and use the help button to simplify discovery of your CSS and JS files and groups. Pay close attention to the method and location of your JS group embeddings. See the plugin's FAQ for more information on usage.
1. *Recommended:* On the "Browser Cache" tab, HTTP compression is enabled by default. Make sure to enable other options to suit your goals.
1. *Recommended:* If you already have a content delivery network (CDN) provider, proceed to the "Content Delivery Network" tab and populate the fields and set your preferences. If you do not use the Media Library, you will need to import your images etc into the default locations. Use the Media Library Import Tool on the "Content Delivery Network" tab to perform this task. If you do not have a CDN provider, you can still improve your site's performance using the "Self-hosted" method. On your own server, create a subdomain and matching DNS Zone record; e.g. static.domain.com and configure FTP options on the "Content Delivery Network" tab accordingly. Be sure to FTP upload the appropriate files, using the available upload buttons.
1. *Optional:* On the "Database Cache" tab, the recommended settings are preset. If using a shared hosting account use the "disk" method with caution, the response time of the disk may not be fast enough, so this option is disabled by default. Try object caching instead for shared hosting.
1. *Optional:* On the "Object Cache" tab, all of the recommended settings are preset. If using a shared hosting account use the "disk" method with caution, the response time of the disk may not be fast enough, so this option is disabled by default. Test this option with and without database cache to ensure that it provides a performance increase.
1. *Optional:* On the "User Agent Groups" tab, specify any user agents, like mobile phones if a mobile theme is used. 

== What users have to say: ==

* Read [testimonials](https://twitter.com/w3edge/favorites) from W3TC users.

== Press: Mentions, Tutorials &amp; Reviews ==

**January 2013:**

* [How to Speed Up Your WordPress Site (Quickly and Easily)](http://www.wpexplorer.com/how-to-speed-up-wordpress), Tom Ewer
* [The Periodic Table of WordPress Plugins](http://www.wpexplorer.com/the-periodic-table-of-wordpress-plugins-and-my-top-5/), Tom Ewer
* [WordPress Optimization Guide](http://gtmetrix.com/wordpress-optimization-guide.html)

**December 2012:**

* [The 25 Best WordPress Plugins Ever](http://www.websitemagazine.com/content/blogs/posts/archive/2012/12/05/the-25-best-wordpress-plugins-ever.aspx), Michael Garrity

**November 2012:**

* [What You Should Know About Using a CDN With WordPress](http://themefuse.com/blog/what-you-should-know-about-using-a-cdn-with-wordpress/), Karol Krol

**October 2012:**

* [The ManageWP Guide to Speeding Up Your WordPress Site with Plugins](http://managewp.com/how-to-speed-up-your-wordpress-website), Tom Ewer


**September 2012:**

* [Secrets Of High-Traffic WordPress Blogs](http://wp.smashingmagazine.com/2012/09/12/secrets-high-traffic-wordpress-blogs/), Siobhan McKeown
* [10 essential WordPress plugins](http://www.computerworld.com/s/article/9230638/10_essential_WordPress_plugins), Ken Gagne

**August 2012:**

* [#114: Let’s Do Simple Stuff to Make Our Websites Faster](http://css-tricks.com/video-screencasts/114-lets-do-simple-stuff-to-make-our-websites-faster/), Chris Coyier
* [The Plugins I Run](http://pippinsplugins.com/the-plugins-i-run/), Pippin Williamson
* [WordPress Cache: WP Super Cache vs W3 Total Cache](http://www.wpbrix.com/wordpress-seo/wordpress-cache-wp-super-cache-vs-w3-total-cache/)
* [15 Tips to Speed Up Your Website](http://www.seomoz.org/blog/15-tips-to-speed-up-your-website), Armin Jalili

**July 2012:**

* [Using Liquid Web's CDN with WordPress via W3 Total Cache](http://lw.rrfaae.com/2012/07/using-liquid-webs-cdn-with-wordpress-via-w3-total-cache/), Jason Gillman Jr.
* [Tips for Server Admins &amp; Website Owners](http://www.tharunpkarun.com/2012/07/tips-for-server-admins-website-owners/), Tharun Karun
* [Step-by-Step guide to WordPress Optimization using W3 Total Cache](http://pthakkar.com/category/website-optimization/w3-total-cache/),Pankaj Thakkar
* [Top 10 Most Essential WordPress Plugins, Must Install](http://omgtoptens.com/web/wordpress/top-10-most-essential-wordpress-plugins-must-install/)
* [W3 Total Cache Vs WP Super Cache: Best Caching Plugin?](http://bloggerspassion.com/w3-total-cache-vs-wp-super-cache/), Anil Agarwal
* [How to Install and Configure W3 Total Cache](http://www.techlila.com/wordpress-plugins/how-to-install-configure-w3-total-cache-worpress/)

**June 2012:**

* [The Meaning of Plugins and The Best WordPress Plugins](http://www.bloggersdeck.com/the-meaning-of-plugins-and-the-best-wordpress-plugins/), Reginald Chan
* [6 Essential Plugins for New WordPress Sites](http://www.presstrends.io/6-essential-plugins-for-new-wordpress-sites/), Mitch Monsen

**May 2012:**

* [﻿W3 Total Cache Settings](﻿http://techod.com/w3-total-cache-settings/), ﻿Jason Graham
* [Leverage Browser Caching with W3 Total Cache](http://www.mvestormedia.com/leverage-browser-caching-w3-total-cache/), Ian Rogers

**April 2012:**

* [Apache Traffic Server as a Reverse Proxy](http://kpayne.me/2012/04/10/apache-traffic-server-as-a-reverse-proxy/), Kurt Payne
* [Essential WordPress Plugins For Every WordPress Blog](http://www.softlovers.com/essential-wordpress-plugins-for-every-wordpress-blog/), Shashank Johri
* [First Steps On A Fresh WordPress Install](http://wpshout.com/first-steps-on-a-fresh-install/), Alex Denning
* [Essential WordPress Plugins](http://the-hate-project.blogspot.com/2012/04/essential-wordpress-plugins.html)
* [15 Ridiculously Useful WordPress Plugins for Travel Bloggers](http://travelbloggeracademy.com/wordpress-travel-bloggers/), Adam Costa
* [10 plugins every new WordPress blog should have](http://kcclaveria.com/2012/04/10-wordpress-plugins/)
* [Caching and Synthesis Managed WordPress Hosting](http://websynthesis.com/caching-and-synthesis-managed-wordpress-hosting/)
* [How to: Boost WordPress performance with W3 Total Cache and APC](http://www.neilturner.me.uk/2012/04/30/how-to-boost-wordpress-performance-with-w3-total-cache-and-apc.html), Neil Turner
* [Supercharge WordPress Blog With Cloudflare, MaxCDN and W3 Total Cache](http://hellboundbloggers.com/2012/04/24/supercharge-wordpress-cloudflare-maxcdn-w3-total-cache/)
* [W3 Total Cache Setup for Rackspace Cloud Sites with Cloud Files](http://thecustomizewindows.com/2012/04/w3-total-cache-setup-for-rackspace-cloud-sites-with-cloud-files/), Abhishek Ghosh
* [11 Steps To Speed Up Your WordPress Site](http://www.paulund.co.uk/11-steps-to-speed-up-your-wordpress-site)
* [Speeding Up WordPress With The W3 Total Cache Plugin](http://invenioblog.com/2012/04/speeding-up-wordpress-with-the-w3-total-cache-plugin/)
* [11 of Our Favorite Wordpress Plugins](http://www.putontheglasses.com/blog/11-of-our-favorite-wordpress-plugins)
* [Leverage Browser Caching with W3 Total Cache](http://www.mvestormedia.com/leverage-browser-caching-w3-total-cache/), Ian Rogers
* [Speeding Up WordPress Websites](http://designershq.net/wordpress-plugins/speeding-up-wordpress-websites/), Craig Butcher
* [How I Increased My Blog Loading Speed by 500%](http://www.asiogroups.com/how-i-increased-my-blog-loading-speed/)
* [5 WordPress Plugins I Can’t Live Without – 2012 Edition](http://bit51.com/5-wordpress-plugins-i-cant-live-without-2012-edition/), Chris Wiegman
* [Setting Up and Optimizing W3 Total Cache - Up to v0.9.2.4](http://tentblogger.com/w3-total-cache/)
* [W3 Total Cache hilft bei WordPress Optimierung](http://blog.stenki.eu/2012/04/wordpress-optimierung-mit-w3-total-cache/)

**March 2012:**

* [The Plugins and Code Behind WPForce 2.0](http://wpforce.com/plugins-and-code-behind-wpforce/), Jonathan Dingman
* [﻿5 Must Have Plugins for WordPress Bloggers](﻿http://igaret.com/wordpress/5-must-have-plugins-for-wordpress-bloggers), ﻿Garet McKinley
* [10 Million hits a day with WordPress using a $15 server](http://www.ewanleith.com/blog/900/10-million-hits-a-day-with-wordpress-using-a-15-server), Ewan Leith via [Hacker News](http://news.ycombinator.com/item?id=3775715)
* [High-Performance WordPress with W3 Total Cache and Nginx](http://elivz.com/blog/single/wordpress_with_w3tc_on_nginx/), Eli Van Zoeren
* [How To Speed Up Your WordPress Blog](http://managewp.com/how-to-speed-up-your-wordpress-blog), Vladimir Prelovac
* [﻿The Lab + W3 Total Cache](﻿http://www.purpleturtleproductions.ca/the-lab/new-category-the-lab-w3-total-cache/), Simon
* [5 Powerful WordPress Plugins To Increase Sharing Of Your Articles](http://blog.kissmetrics.com/5-powerful-wordpress-plugins/)
* [﻿How I increased Websitez.com’s performance by an additional 20%](﻿http://websitez.com/how-i-increased-websitez-coms-performance-by-an-additional-20/), ﻿Eric Stolz
* [﻿WordPress with W3 Total Cache on Nginx with APC ](﻿http://chrisgilligan.com/wordpress/wordpress-with-w3-total-cache-on-nginx-with-apc-virtualmin/), ﻿Chris Gilligan
* [﻿Screencast Tuesday: Using W3 Total Cache Part 1](﻿http://headwaythemes.com/screencast-tuesday-using-w3-total-cache-part-1/), AJ Morris
* [Screencast Tuesday: Using W3 Total Cache Part 2 – Implementing a CDN](http://headwaythemes.com/screencast-tuesday-using-w3-total-cache-part-2-implementing-a-cdn/), AJ Morris
* [A Quick Bit About WordPress Caching](http://blog.ryantoohil.com/2012/03/a-quick-bit-about-wordpress-caching.php), Ryan Toohil
* [How To Find Out Which WordPress Plugins Are Making Your Site Slow](http://www.business2community.com/blogging/how-to-find-out-which-wordpress-plugins-are-making-your-site-slow-0138577), Kim Castleberry
* [Top 20 Plugins Used Across 30,000+ WordPress Sites](http://www.presstrends.io/top-20-plugins-used-across-30000-wordpress-sites/), George Ortiz
* [10 Plugins That Will Speed Up Your WordPress Site](http://blog.brainhost.com/10-plugins-that-will-speed-up-your-wordpress-site/)
* [Using nRelate to Engage Readers & Make Money](http://imstash.com/using-nrelate-engage-readers-make-money/)
* [Our Top 10 Favorite WordPress Plugins](http://sitefox.com/blog/top-10-favorite-wordpress-plugins/), Samir Balwani
* [Screencast Tuesday: Using W3 Total Cache Part 1](http://headwaythemes.com/screencast-tuesday-using-w3-total-cache-part-1/), AJ Morris
* [Build the WordPress wholly stations CDN utilize the caching plugin W3TC filmed cloud](http://www.84tt.com/web/2012/03/608.html)
* [3 Tutorials For Speeding Up Your WordPress Site](http://blog.asmallorange.com/3-tutorials-for-speeding-up-your-wordpress-site/), McKinney Brown
* [WordPress SEO & Security](http://www.albertawebsitemarketing.com/wordpress-seo-and-security/)
* [CDN – WTF?](http://talkingwordpress.com/cdn-wtf/)
* [Supercharge WordPress, Part 3](http://linuxaria.com/article/supercharge-wordpress-part-3?lang=en)
* [7 Effective Ways To Make Your WordPress Blog Load Faster](http://www.uniquetipsonline.com/7-effective-ways-to-make-your-wordpress-blog-load-faster/)
* [WordPress Plugins to Speed Up Your Website](http://blog.templatemonster.com/2012/03/01/wordpress-plugins-speed-up-blog-website/), Edward Korcheg
* [CloudFlare and W3TC (Part I)](http://elimcmakin.com/cloudflare-and-w3tc-not-just-another-cdn-part-i/)
* [CloudFlare and W3TC (Part II)](http://elimcmakin.com/cloudflare-and-w3tc-part-ii/)
* [How to speed up WordPress – my experience with slow WordPress](http://bachutha.com/how-to-speed-up-wordpress-my-experience-with-slow-wordpress/)
* [Make Your WordPress Site Lightning Fast](http://www.helpeverybodyeveryday.com/websocial-media/848-make-your-wordpress-site-lightning-fast), Matt Handal
* [Speed Up WordPress Websites](http://wordpress-tips.info/speed-up-wordpress-websites/2012/03/27/)
* [W3 Total Cache, caché y compresión para optimizar WordPress](http://www.gamblingprofesional.com/noticias/otros1/w3-total-cache-optimizando-wordpress-de-forma-sencilla/)
* [Velocizzare WordPress](http://www.evemilano.com/2012/03/velocizzare-wordpress/), Giovanni Sacheli
* [21 Wicked WordPress Plugins](http://garinkilpatrick.com/21-wicked-wordpress-plugins/), Garin Kilpatrick
* [How to fix high-CPU load in WordPress](http://support.ecenica.com/wordpress/fix-high-cpu-load-wordpress/)
* [How much (cache) is too much?](http://blog.gingerlime.com/2012/how-much-cache-is-too-much/), Yoav Aner
* [10 Must Have WordPress Plugins Of 2012 Every Blogger Should Know About](http://www.jeffbullas.com/2012/03/13/10-must-have-wordpress-plugins-of-2012-every-blogger-should-know-about/), Leo Widrich
* [Speed up your WordPress Blog: 5 Easy Steps](http://techwalls.com/blogging/speed-up-wordpress-blog-easy-steps/), Sanjib Saha
* [7 Tips to Test Website Speed and Decrease WordPress Load Time](http://natedevore.com/7-tips-to-test-website-speed-and-decrease-wordpress-load-time/), Nate Devore
* [How I increased Websitez.com’s performance by an additional 20%](http://websitez.com/how-i-increased-websitez-coms-performance-by-an-additional-20/), Eric Stolz
* [WordPress with W3 Total Cache on Nginx with APC](http://chrisgilligan.com/wordpress/wordpress-with-w3-total-cache-on-nginx-with-apc-virtualmin/), Chris Gilligan
* [Speed Up Your Website With The "W3 Total Cache" Wordpress Plugin](http://www.youtube.com/watch?v=Luf2Y_F23l0)
* [WordPress W3 Total Cache Eklentisi Ayarları](http://www.55dakika.com/wordpress-w3-total-cache-eklentisi-ayarlari-34124.html)
* [9 Essential WordPress Plugins](http://www.ostraining.com/blog/wordpress/9-essential-wordpress-plugins/), Ed Andrea
* [15 Must Have WordPress Plugins for the New Blogger](http://avgjoegeek.net/15must-have-wordpress-plugins/), Jason Mathes
* [10 Must Have WordPress Plugins For Your Blog](http://www.tricksmachine.com/2012/03/10-must-have-wordpress-plugins.html)
* [How To Speed Up Your WordPress Blog](https://managewp.com/how-to-speed-up-your-wordpress-blog), Tom Ewer
* [5 Best Plug-ins for WordPress Blog](http://www.thegeeksclub.com/5-plugins-wordpress-blog/), Jonathan Acabo
* [Easy W3 Total Cache WordPress Plugin Configuration](http://www.mvestormedia.com/easy-w3-total-cache-configuration/), Ian Rogers
* [Ideal W3 Total Cache Settings For Shared Hosting](http://www.kidnapcustomers.com/how-to-make-a-website/w3-total-cache-settings-wordpress-speed/)

**February 2012:**

* [Get Google Page Speed Report Via W3 Total Cache](http://www.myh3r3.com/code/get-google-page-speed-report-via-w3-total-cache/), Aayush Nagar
* [3 WordPress Plugins To Speed Up Your Blog](http://www.wordpressninja.com/2012/02/speed-up-your-wordpress-blog/), Missy Diaz
* [Top 5 Plugins for WordPress](http://creativetech.me/2012/02/top-5-plugins-wordpress/), Phill Fernandes
* [Geeking out with WordPress, CDNs, and Amazon CloudFront](http://polizeros.com/2012/02/17/geeking-out-with-wordpress-cdns-and-amazon-cloudfront/), Bob Morris
* [Top 10 Essential WordPress Plugins that Your Website Needs](http://impressthemes.com/2012/02/top-10-essential-wordpress-plugins-that-your-website-needs/)
* [How to start your own blog using WordPress](http://blog.kualo.com/how-to-start-your-own-blog-using-wordpress/)
* [Top 10 Must Have WordPress Plugins In Your Blog](http://www.learnblogtips.com/top-10-must-have-wordpress-plugins-in-your-blog/), Rahul Kuntala
* [Improve WordPress Speed with W3 Total Cache](http://www.webcitz.org/wordpress-tips/improve-wordpress-speed-with-w3-total-cache.html)
* [WordPress Plugins for 2012](http://proudlypowered.com/wordpress-plugins-2012/)
* [How to Install Memcached and Use It with Your WordPress via W3 Total Cache on CentOS](http://www.bleuken.com/how-to-install-memcached-and-use-it-with-your-wordpress-via-w3-total-cache-on-centos-20120220/)
* [How to reduce WordPress CPU usage using Cache and P3 plugin](http://wpmarketing.org/2012/02/how-to-reduce-wordpress-cpu-usage-using-cache-and-p3-plugin/)
* [Speeding up your WordPress site with MaxCDN (Advanced users)](http://wpverse.com/2012/02/speeding-up-your-wordpress-site-with-maxcdn-advanced-users/), Noel Saw
* [Cache WordPress With W3 Total Cache](http://wphostingreviews.com/plugins/cache-wordpress-with-w3-total-cache-1768/), David Blane
* [Cache Plugin For Improving Wordpress Speed](http://www.youtube.com/watch?v=70TqVjmtXYU), Mark Washburn
* [De snelheid van je website verbeteren](http://www.webtaurus.nl/amersfoort-webdesign/tag/w3-total-cache)
* [WordPress Plugins that Power Digital Inspiration](http://www.labnol.org/software/must-have-wordpress-plugins/14034/)
* [9 Essential WordPress Plugins To Install On Your Site](http://www.haikarela.com/9-wordpress-plugins-to-install/)
* [Some Other Tricks For A Speedy WordPress](http://wp.tutsplus.com/tutorials/some-other-tricks-for-a-speedy-wordpress/), Ariff Shah
* [5 Essential WordPress Plug-ins You Should Be Using](http://thinktraffic.net/5-essential-wordpress-plug-ins-you-should-be-using), Caleb Wojcik
* [14 Free WordPress Plugins I Installed, Before Doing Anything Else](http://www.kidnapcustomers.com/how-to-make-a-website/wordpress-plugins-must-have/)
* [Install Memcached to speed up your WordPress site](http://www.wpinsite.com/articles/install-memcached-speed-wordpress-site)
* [Cara Membuat CDN dengan W3 Total Cache pada Subdomain](http://www.youtube.com/watch?v=XB1sry9ns4Y), Hadie Danker
* [Top 10 WordPress Plugins To Improve a WordPress Blog](http://www.bloggingtips.com/2012/02/05/top-10-wordpress-plugins-to-improve-a-wordpress-blog/), Zac Johnson
* [The 10 Best Plugins for Online Entreprenuers](http://www.webdesigndev.com/make-money-online/the-10-best-wordpress-plugins-for-online-entrepreneurs)
* [Time to Smarten up Your WordPress Blog – 15 Must Have Plugins](http://www.multyshades.com/2012/02/time-to-smarten-up-your-wordpress-blog-15-must-have-plugins/), Muhammad Haroon
* [Best WordPress SEO Plugins For Boost Your Traffic](http://www.redfoxmagazine.com/best-wordpress-seo-plugins-for-boost-your-traffic/)
* [Speed Up WordPress Load Times and Grab More Visitors](http://www.matthewhooper.com/speed-up-wordpress/)Matthew Hooper
* [Use a windows azure blob storage account with your wordpress blog](http://www.gep13.co.uk/blog/use-a-windows-azure-blob-storage-account-with-your-wordpress-blog/), Gary Ewan Park
* [How To Find Out Which WordPress Plugins Are Making Your Site Slow](http://just-ask-kim.com/wordpress-plugins-site-slow/#.URBVYVpdec8)
* [How to Install and Configure W3 Total Cache Plugin](http://www.youtube.com/watch?v=Lbs3pRL4XQg)
* [Day 8: Speed Up your WordPress Site with W3 Total Cache](http://wplifeguard.com/day-8-speed-up-your-wordpress-site-with-w3-total-cache/)
* [10 Ways to Improve WordPress Performance on Windows](http://jesscoburn.com/archives/2012/02/25/10-ways-to-improve-wordpress-performance-on-windows/), Jess Coburn
* [W3 Total Cache - Accélérer votre site Wordpress](http://www.youtube.com/watch?v=Hx-_e8wTUEw)
* [WordPress on Nginx, Part 1: Preparing VPS the Debian Way](http://www.linuxforu.com/2012/02/wordpress-nginx-part-1-preparing-vps-debian-linux/), Atanu Datta
* [WordPress on Nginx, Part 2: vhost, MySQL & APC Configurations](http://www.linuxforu.com/2012/02/wordpress-nginx-part-2-domain-vhost-config-migrating-files-fine-tuning-mysql-apc/), Atanu Datta

**January 2012:**

* [﻿Speed up WordPress with W3 Total Cache and Amazon CloudFront (CDN)](﻿http://richwp.com/wordpress-cdn-total-cache-amazon-cloudfront/), Dustin
* [﻿The Ultimate Quickstart Guide to Speeding Up Your WordPress Site](﻿http://wp.tutsplus.com/tutorials/the-ultimate-quickstart-guide-to-speeding-up-your-wordpress-site/), ﻿Matt Pilott
* [Speed Up WordPress with W3 Total Cache](http://www.doitwithwp.com/speed-up-wordpress-with-w3-total-cache/), Dave Clements
* [Speed Up your WordPress Site with W3 Total Cache](http://wplifeguard.com/day-8-speed-up-your-wordpress-site-with-w3-total-cache/)
* [﻿How to Speed Up Your WordPress Site and Reduce Your Hosting Costs](﻿http://www.washingtonsblog.com/2012/01/how-to-speed-up-your-wordpress-site-and-reduce-your-hosting-costs.html)
* [Why I Switched From Drupal to WordPress](http://eagereyes.org/blog/2012/why-i-switched-drupal-wordpress), Robert Kosara
* [Top 10 Best WordPress Plugins](http://ebusinessmatters.com/top10-best-wordpress-plugins/), Brian Duffy
* [WordPress Plug-ins](http://www.crazyxhtml.com/blog/wordpress-plug-ins/)
* [Los mejores plugins para WordPress](http://zonawordpress.com/los-mejores-plugins-para-wordpress/)
* [10 Essential Plugins for Your WordPress Blog](http://sproutsocial.com/insights/2012/01/wordpress-plugins/), Jennifer Beese
* [10 Must-Have WordPress Plugins on Your Site](http://realityco.com/blog/?p=23&option=com_wordpress&Itemid=168)
* [3 Must Have Free Smashing WordPress SEO Plugins For Your Blog](http://techtipsportal.com/wordpress-seo-plugins-for-blog/), Kushal Biswas
* [Best WordPress SEO Plugins For 2012](http://www.iblogzone.com/2012/01/the-best-wordpress-seo-plugins-for-2012-part-1-html.html)
* [5 Simple Tips for Making WordPress Run Faster](http://headway101.com/5-simple-tips-for-making-wordpress-run-faster/)
* [Tips on speeding up your WordPress site](http://www.edwardxwu.com/2012/01/tips-on-speeding-up-your-wordpress-site/), Edward Xwu
* [W3 Total Cache Kurulumu](http://kenancanol.com/2012/w3-total-cache-kurulumu/)
* [Speed Up WordPress with W3 Total Cache](http://www.doitwithwp.com/speed-up-wordpress-with-w3-total-cache/), Dave Clements
* [The Ultimate Quickstart Guide to Speeding Up Your WordPress Site](http://wp.tutsplus.com/tutorials/the-ultimate-quickstart-guide-to-speeding-up-your-wordpress-site/), Matt Pilott
* [Speed up WordPress with W3 Total Cache and Amazon CloudFront (CDN)](http://richwp.com/wordpress-cdn-total-cache-amazon-cloudfront/)
* [Configure/Install MaxCDN with W3 Total Cache WordPress Plugin](http://www.youtube.com/watch?v=OYqOG7_TIk8), Mrinmay Bhattacharjee
* [How to Install and Configure W3 Total Cache Plugin](http://www.dailymotion.com/video/xnx1cl_how-to-install-and-configure-w3-total-cache-plugin_tech#.UQ_ujlpdec8)
* [How to setup a CDN in WordPress using W3 Total Cache & Amazon CloudFront](http://pedrodasilva.co.uk/how-to-setup-content-delivery-network-wordpress-using-w3tc-amazon-cloudfront/), Pedro Da Silva
* [WordPress plugin – W3 Total Cache](http://www.naschenweng.info/2012/01/16/wordpress-plugin-w3-total-cache/)
* [Using YUI Compressor with the W3 Total Cache plugin](http://secure.delishost.com/knowledgebase/11/Using-YUI-Compressor-with-the-W3-Total-Cache-plugin.html)
* [(mt):Install APC for use with W3 Total Cache](http://wiki.mediatemple.net/w/(mt):Install_APC_for_use_with_W3_Total_Cache)
* [The Perfect APC Configuration](http://gregrickaby.com/2012/01/the-perfect-apc-configuration.html), Greg Rickaby
* [CloudFlare – A Review](http://complete-concrete-concise.com/web-tools/cloudflare-a-review)
* [W3 Total Cache Kurulumu ve Ayarları](http://bcakir.com/w3-total-cache-kurulumu-ve-ayarlari.html)

**December 2011:**

* [A shout-out to my tech partners of 2011!](http://www.jimcarroll.com/2011/12/a-shout-out-to-my-tech-partners-of-2011/), Jim Carroll
* [15 Top WordPress Plugins Of 2011](http://smashinghub.com/15-top-wordpress-plugins-of-2011.htm), Ali Qayyum
* [Google Speed Online API widget integration with W3 Total Cache in WordPress](http://shivasoft.in/blog/webtech/seo/google-speed-online-api-widget-integration-with-w3-total-cache-in-wordpress/), Jitendra Zaa
* [Modern WordPress Development for 2012](http://sachagreif.com/modern-wordpress-development-in-2012/), Sacha Greif
* [Set up W3 Total Cache with Amazon CloudFront CDN](http://www.doitwithwp.com/set-up-w3-total-cache-with-amazon-cloudfront-cdn/), Dave Clements
* [Installing & Configuring XCache for W3 Total Cache on a cPanel Server](http://www.thewebhostinghero.com/tutorials/xcache-w3tc-on-a-cpanel-server.html)
* [How-To Install W3 Total Cache: Part One -- What is Page Caching?](http://www.youtube.com/watch?v=HvQ5aVPdHX8), Dorie Scarlet
* [Must Use: W3 Total Cache WordPress Plugin](http://www.dailyblogtips.com/must-use-w3-total-cache-wordpress-plugin/), Daniel Scocco
* [10 Best WordPress Plugins](http://www.anhosting.com/blog/2011/12/10-best-wordpress-plugins/), AN Hosting
* [How to Install & Configure W3 Total Cache Plugin](http://marketgurus.org/how-to-install-configure-w3-total-cache-plugin/)
* [Website Speed Part 3 – Caching WordPress](http://speckyboy.com/2011/12/14/website-speed-part-3-caching-wordpress/), Andy Killen
* [WP Super Cache 1.0 Released](http://www.wptavern.com/wp-super-cache-1-0-released), Jeffro
* [5 Must Have WordPress Plugins](http://www.stormcode.net/marketing/5-must-have-wordpress-plugins/), Brendan
* [12 Essential WordPress Plugins](https://docs.google.com/a/placester.com/viewer?a=v&q=cache:GGcWVwJTMOUJ:myathleticlife.com/wp-content/uploads/2011/12/12-Essential-Wordpress-Plugins.pdf+&hl=en&gl=us&pid=bl&srcid=ADGEEShOSbWoVFwJiNrmfGCe_rqA48eDO7pjwfdqzzK3CUJevx39BA9vdTm4OzMNSNLlyvBDgJoYIypFnVm3oHF6scJJJB2a-DCgZERkyI9ExrSpSMmI_1AqxmcMOf2FZY0Z6njFouX9&sig=AHIEtbQT-oErlhUCsEOh56YNJVlhLu1AxA)
* [Measuring impact of plugins on WordPress loading](http://wpdojo.net/tag/woocommerce/page/7/), MillaN
* [Recommended WordPress Plugins](http://www.cinntech.com/recommended-wordpress-plugins/)
* [Recommended WordPress Plugins For Any Serious Blogger](http://getbusylivingblog.com/recommended-wordpress-plugins-for-any-serious-blogger/), Benny Hsu
* [Top 10 WordPress Plugins for 2011](http://y2kemo.com/2011/12/top-10-wordpress-plugins-for-2011/)
* [Measuring impact of plugins on WordPress loading](http://www.dev4press.com/2011/blog/benchmark/measuring-impact-of-plugins-on-wordpress-loading/)

**November 2011:**

* [10+ Best WordPress Plugins You must have to start a Blog/Website](http://www.wokay.com/technology/10-best-wordpress-plugins-to-use-in-2011-must-have-awesome-blog-website-start-56197.html), Barnabas Reilly
* [5 Must Have WordPress Plugins](http://sayfsolutions.com/2011/5-must-have-wordpress-plugin/), Abdullah Hashim
* [Plug 'em in: WordPress plugins I've completely fallen for…](http://bookbagsandcatnaps.com/2011/11/plug-em-in-wordpress-plugins-ive-completely-fallen-for/), Donna Brown
* [8 WordPress Plugins you have to Install](http://www.123-reg.co.uk/blog/tips-tutorials/8-wordpress-plugins-you-have-to-install/), Alex Moss
* [Installing and Configuring the W3 Total Cache Plugin on WordPress](http://help.godaddy.com/article/7299?isc=u&locale=en)
* [Installing CSS Tidy For WordPress W3 Total Cache Plugin](http://hungred.com/how-to/installing-css-tidy-wordpress-w3-total-cache-plugin/), Clay Lua

**October 2011:**

* [10 WordPress Plugins Your Small Business Website Needs](http://searchenginewatch.com/article/2115338/10-WordPress-Plugins-Your-Small-Business-Website-Needs), October 7, 2011

**September 2011:**

* [How to Reduce Bandwidth Usage and Optimize Website Performance with W3 Total Cache and Amazon CloudFront](http://helgeklein.com/blog/2011/09/how-to-reduce-bandwidth-usage-and-optimize-website-performance-with-w3-total-cache-and-amazon-cloudfront/), Helge Klein
* [Speeding Up WordPress, Part 1: Basic W3 Total Cache Configuration and Content Delivery Network](http://www.mattstratton.com/tech-tips/speed-up-wordpress), Matt Stratton
* [Boost WordPress Blog Performance: Minify & CDN with W3TC](http://sangatpedas.com/boosting-increase-wordpress-performance-w3tc-minify-cdn/)
* [W3 TOTAL CACHE VS QUICK CACHE FOR WORDPRESS](http://thinkpaid.com/2011/09/reviews/w3-total-cache-vs-quick-cache), Andrew Summers
* [10 Free WordPress Plugins you must have](http://www.webprocyon.com/2011/09/10-free-wordpress-plugins-you-must-have.html)

**August 2011:**

* [Matt Mullenweg: State of the Word 2011 (4:49)](http://wordpress.tv/2011/08/14/matt-mullenweg-state-of-the-word-2011/), Matt Mullenweg
* [http://freenuts.com/increase-your-page-speed-to-a-new-level/](Increase Your Page Speed To A New Level)
* [7 Plugins That Every WordPress Blog Must Have](http://coderbay.com/7-wordpress-plugins-that-every-wordpress-blog-must-have/), Karim Pattoki
* [Speeding Up Your WordPress Site With W3 Total Cache](http://www.sohailtech.com/2011/08/10/speeding-wordpress-site-w3-total-cache/)
* [W3 Total Cache Setup with CloudFlare and CDN : Complete Tutorial Guide](http://thecustomizewindows.com/2011/08/w3-total-cache-setup-with-cloudflare-and-cdn-complete-tutorial-guide/), Abhishek Ghosh

**July 2011:**

* [Speeding Up Your Blog - Part II: WordPress & Cloudflare Integration](http://www.thewebhostinghero.com/tutorials/wordpress-cloudflare.html)
* [How Your Website Loses 7% of Potential Conversions](http://www.clickz.com/clickz/column/2097323/website-loses-potential-conversions), Bryan Eisenberg
* [How to Integrate Google Page Speed with W3 Total Cache](http://geekscalling.com/google/how-to-integrate-google-page-speed-with-w3-total-cache), Anish
* [22 WordPress Plugins for Content Marketers](http://www.business2community.com/content-marketing/22-wordpress-plugins-for-content-marketers-040787), Brody Dorland

**June 2011:**

* [5 Types of WordPress Plugins for Real Estate Sites](http://www.mytechopinion.com/2011/06/5-types-of-wordpress-plugins-for-your-real-estate-site.html), Nicole Nicolay
* [WordPress Optimization Results: Varnish/Nginx/APC + W3 Total Cache + Amazon S3 + CloudFlare](http://danielmiessler.com/blog/wordpress-optimization-results-varnishnginxapc-w3-total-cache-amazon-s3-cloudflare), Daniel Miessler
* [Case Study: WordPress, MaxCDN, CloudFlare and W3 Total Cache Integration](http://www.thewebhostinghero.com/articles/case-study-wp-maxcdn-cloudflare.html), Ritesh Sanap

**May 2011:**

* [HOW TO: Get Google Page Speed Report Via W3 Total Cache](http://hellboundbloggers.com/2011/05/10/get-google-page-speed-report-in-w3-total-cache/), S.Pradeep Kumar
* [Optimizing WordPress with Nginx, Varnish, APC, W3 Total Cache, and Amazon S3 (With Benchmarks)](http://danielmiessler.com/blog/optimizing-wordpress-with-nginx-varnish-w3-total-cache-amazon-s3-and-memcached), Daniel Miessler
* [Poll: Best Caching Plugin for WordPress?](http://digwp.com/2011/05/best-caching-plugin-wordpress/), Jeff Starr
* [Page Speed Online has a shiny new API](http://googlecode.blogspot.com/2011/05/page-speed-online-has-shiny-new-api.html), Andrew Oates and Richard Rabbat
* [Use W3 Total Cache to Speed Up Your WordPress Site](http://www.ostraining.com/blog/wordpress/w3-total-cache/), Steve Burge

**April 2011:**

* [Setting Up and Optimizing W3 Total Cache](http://tentblogger.com/w3-total-cache/), John Saddington
* [How To Configure The Various W3TC Plugin Settings For Your WordPress Blog](http://www.makeuseof.com/tag/configure-w3tc-plugin-wordpress/), James Bruce
* [Speeding Up Your WordPress Website: 11 Ways to Improve Your Load Time](http://wpmu.org/speeding-up-your-wordpress-website-11-ways-to-improve-your-load-time/), Siobhan Ambrose
* [Recipe for Baked WordPress](http://carpeaqua.com/2011/04/05/recipe-for-baked-wordpress/), Justin Williams
* [WordPress + W3 Total Cache + CDN story](http://translate.google.com/translate?hl=en&sl=auto&tl=en&u=http%3A%2F%2Fblog.gaspanik.com%2Factivate-cdn-option-on-w3totalcache), Mori Masako
* [SETTING UP W3 TOTAL CACHE PART 2](http://www.geekforhim.com/setting-up-w3-total-cache-part-2/), Matthew Snider
* [SETTING UP W3 TOTAL CACHE PART 1](http://www.geekforhim.com/setting-up-w3-total-cache-part-1/), Matthew Snider

**March 2011:**

* [WPML with W3TC for Fast and Efficient Multilingual Websites](http://wpml.org/2011/03/wpml-with-w3tc/), Amir

**February 2011:**

* [Optimizing WordPress with Nginx, Varnish, W3 Total Cache, Amazon S3, and Memcached (With Benchmarks)](http://danielmiessler.com/blog/optimizing-wordpress-with-nginx-varnish-w3-total-cache-amazon-s3-and-memcached), Daniel Miessler
* [My WordPress site loads in 2 seconds... does yours?](http://labsecrets.com/blog/2011/02/14/my-wordpress-site-loads-in-two-seconds-does-yours/)

**January 2011:**

* [Caching a Dynamic Website. Does it Make a Difference for Loading Speed?](http://bacsoftwareconsulting.com/blog/index.php/web-programming/caching-a-dynamic-website-does-it-make-a-difference-for-loading-speed/)
* [11 Important Steps to Optimize WordPress and Increase Performance](http://www.bernskiold.com/2011/01/10/11-important-steps-to-optimize-wordpress-and-increase-performance/), Erik Bernskiold
* [Speed up WordPress with the W3 Total Cache Plugin](http://wplift.com/speed-up-wordpress-with-the-w3-total-cache-plugin), Oliver Dale
* [How to Make Your Blog Load Faster than ProBlogger](http://www.problogger.net/archives/2011/01/04/how-to-make-your-blog-load-faster-than-problogger/), Pro Blogger
* [WP Honors Winner, Free Plugin Category](http://wpcandy.com/reports/the-2010-wphonors-award-winners), WPCandy.com

**December 2010:**

* [Best blog plugins](http://www.blog.web6.org/best-blog-plugins/)
* [How To Make Your WordPress Blog Load Faster](http://www.johnchow.com/how-to-make-your-wordpress-blog-load-faster/), John Chow
* [Unleash the Power of WordPress Using Plugin Combos](http://freelancefolder.com/unleash-the-power-of-wordpress-using-plugin-combos/), Paul de Wouters
* [Rackspace Cloud Files for WordPress](http://sporkmarketing.com/blog/1095/rackspace-cloud-files-wordpress/), Jason Lancaster

**November 2010:**

* [Make your blog super fast with W3 Total Cache plugin](http://laspas.gr/2010/11/26/make-blog-super-fast-w3-total-cache-plugin/), Stratos Laspas
* [10 WordPress Plugins I'm Thankful For (And Cannot Live Without)](http://wpmu.org/10-wordpress-plugins-im-thankful-for-and-cannot-live-without/), Sarah Gooding
* [Subjective Results of Installing W3 Total Cache Plugin](http://www.codyhatch.com/administriva/subjective-results-of-installing-w3-total-cache-plugin/), Cody Hatch
* [13 Plugins Your WordPress Site Might Need](http://www.jonbishop.com/2010/11/13-plugins-your-wordpress-site-might-need/), Jon Bishop
* [Best WordPress Plugins that Marketers Use](http://www.nicoleonthenet.com/6390/best-wordpress-plugins-marketers-use/), Nicole Dean
* [WordPress Fat-Loss Diet to Speed Up & Ease Load](http://superbeachbody.com/12528/wordpress-fat-loss-diet-to-speed-up-ease-load/), Erhald Bergman
* [10 WordPress Plugins I'm Thankful For (And Cannot Live Without)](http://wpmu.org/10-wordpress-plugins-im-thankful-for-and-cannot-live-without/), Sarah Gooding
* [Subjective Results of Installing W3 Total Cache Plugin](http://www.codyhatch.com/administriva/subjective-results-of-installing-w3-total-cache-plugin/), Cody Hatch
* [W3 Total Cache Plugin](http://www.xenritech.com/w3-total-cache-plugin.html/)

**October 2010:**

* [20 Wordpress Plugins for Successful Internet Marketers](http://www.incomediary.com/20-wordpress-plugins-for-successful-internet-marketers/), Michael Dunlop
* [Failure Under Load](http://blog.hybridindie.com/2010/10/20/failure-load/), John Brien
* [W3 Total Cache and site response time (as measured by Pingdom)](http://www.pauldavidolson.com/blog/2010/w3-total-cache-and-site-response-time-as-measured-by-pingdom/), Paul David Olson
* [Overhauling WordPress Performance](http://brianegan.com/overhauling-wordpress-performance/), Brian Egan
* [How to Make WordPress Run Faster](http://www.stevecoursen.com/498/how-to-make-wordpress-run-faster/), Stephen Coursen
* [Give Your Wordpress Blog Lightning Fast Speeds With W3 Total Cache](http://www.makeuseof.com/tag/give-wordpress-blog-lightning-fast-speeds-w3-total-cache/), Steven Campbell
* [W3 Total Cache and site response time (as measured by Pingdom)](http://www.pauldavidolson.com/blog/2010/w3-total-cache-and-site-response-time-as-measured-by-pingdom/), Paul David Olson
* [11 Ways to Make Your WordPress Site Faster and Leaner](http://wpmu.org/11-ways-to-make-your-wordpress-site-faster-and-leaner/), Sarah Gooding
* [The Top 10 of Your Top 5 Plugins](http://weblogtoolscollection.com/archives/2010/10/04/the-top-10-of-your-top-5-plugins/), James Huff
* [Integrating memcached to wordpress](http://www.ruchirablog.com/intergrating-memcached-to-wordpress/), Ruchira Sahan
* [Make WordPress Faster (on the Rackspace Cloud)](http://www.mattytemple.com/2010/10/make-wordpress-faster-on-the-rackspace-cloud/), Matt Temple

**September 2010:**

* [Review: W3 Total Cache [WordPress Plugin]](http://sokkz.com/2010/09/29/review-w3-total-cache-wordpress-plugin/)
* [Plugins to Power-Up Your WordPress Installation](http://www.afiffattouh.com/web-design/plugins-to-power-up-your-wordpress-installation), Afif Fattouh
* [Reduce Page Loading Time by 300% With W3 Total Cache](http://c3mdigital.com/2010/09/reduce-page-loading-time-w3-total-cache/), Chris Olbekson
* [Performance Unleashed: How To Optimize Websites For Speed](http://diythemes.com/thesis/improve-website-pagespeed/), Willie Jackson
* [5 Best WordPress Plugins To Improve The Loading Speed Of a Blog](http://www.gadgetcage.com/2010/09/5-best-wordpress-plugins-to-improve-the-loading-speed-of-a-blog.html/)
* [WordPress Fat-Loss Diet to Speed Up & Ease Load](http://www.daljinskapodrska.com/wordpress-fat-loss-diet-to-speed-up-ease-load/)

**August 2010:**

* [WordPress Speed and Optimization Guide](http://thesocialmediaguide.com.au/2010/08/30/wordpress-speed-and-optimization-guide/), Matthew Tommasi
* [How to configure WordPress Blogs Search Engine Friendly](http://solvater.com/2010/09/how-to-configure-wordpress-blog-search-engine-friendly-complete-beginners-guide-for-wordpress-seo/), Arafath Hashmi
* [How to Install and Setup W3 Total Cache for Beginners](http://www.wpbeginner.com/plugins/how-to-install-and-setup-w3-total-cache-for-beginners/)
* [20 Most Useful WordPress Plugins](http://zemalf.posterous.com/20-most-useful-wordpress-plugins), Antti Kokkonen
* [Speed up, compress and optimise WordPress using W3 Total Cache](http://thisishelpful.com/speed-compress-optimise-wordpress-w3-total-cache.html)
* [W3 Total Cache - Further optimization of the blog](http://www.bhoffmeier.de/2010/08/21/w3-total-cache-weitere-optimierung-des-blogs/), Bernd Hoffmeier
* [W3 Total Cache Fixes Bugs, Adds Features with Update](http://www.whoishostingthis.com/blog/2010/08/04/first-draft-w3-total-cache-fixes-bugs-adds-features-with-update/), Jonathan
* [The Quickest Way To Make Your Blog Load Faster](http://www.peterleehc.com/blog/work-from-home/the-quickest-way-to-make-your-blog-load-faster), Peter Lee

**July 2010:**

* [Getting W3 Total Cache and a mobile plugin to work in WordPress](http://blog.trasatti.it/2010/07/getting-w3-total-cache-and-a-mobile-plugin-to-work-in-wordpress.html), Andrea Trasatti
* [Improve Your WordPress Performance With W3 Total Cache](http://maketecheasier.com/improve-wordpress-performance-with-w3-total-cache/2010/07/21), Damien Oh
* [Four Simple Steps For Big Gains In Page Speed](http://www.dailyblogtips.com/four-simple-steps-for-big-gains-in-page-speed/), Greg Hayes
* [How to use Content Delivery Network on Shared Hosting for WordPress](http://solvater.com/2010/07/content-delivery-network-shared-hosting-wordpress-configuration-with-w3-total-cache-in-wordpress/), Arafath Hashmi
* [How to Use Google Webmaster Tools to Diagnose and Improve WordPress Page Speed](http://wpmu.org/how-to-use-google-webmaster-tools-to-diagnose-and-improve-wordpress-page-speed/), Sarah Gooding
* [Caching Wordpress - Preparing Your Blog For The Mainstream](http://bradblogging.com/how-to/caching-wordpress-preparing-your-blog-for-the-mainstream/), Brad Ney
* [11 Ways to Speed Up WordPress](http://mashable.com/2010/07/19/speed-up-wordpress/), Cyrus Patten
* [How To Decrease Page Loading Time Of Your WordPress Blog By 75%](http://bloggingwithsuccess.net/decrease-loading-times), Ishan Sharma
* [Top 10 Wordpress Plugins which I use on DailyBlogging](http://www.dailyblogging.org/wordpress/top-10-wordpress-plugins-which-i-use-on-dailyblogging/), Mani Viswanathan
* [Install and Configure W3 Total Cache in 7 Easy Steps](http://zemalf.com/1443/w3-total-cache/), Antti Kokkonen
* [How to Reduce the Loading Time of Your Blog](http://www.admixweb.com/2010/07/09/how-to-reduce-the-loading-time-of-your-blog/), Rishabh Agarwal
* [5 Wordpress Plugins You Need To Know About](http://thenextweb.com/apps/2010/07/06/5-wordpress-plugins-you-need-to-know-about/), James Hicks

**June 2010:**

* [Speed Up Your Wedding Photography Website in less than 5 minutes](https://www.zippykid.com/2010/06/07/speed-up-your-wedding-photography-website/), Vid Luther
* [12 Ways to Improve Wordpress Page Load Time](http://myblog2day.com/12-ways-to-improve-wordpress-page-load-time.php), Lee Ka Hoong
* [Significantly Speed Up Your WordPress Blog in 9 Easy Steps](http://www.bloggingpro.com/archives/2010/06/21/significantly-speed-up-your-wordpress-blog-in-9-easy-steps/), Robyn-Dale Samuda
* [Speed 'Em Up: Wordpress &amp; W3 Total Cache](http://translate.google.com/translate?js=y&prev=_t&hl=en&ie=UTF-8&layout=1&eotf=1&u=http://www.andilicious.com/blog/1473/20100610/wordpress-beschleunigen-grundlagen-w3-total-cache-page-speed&sl=auto&tl=en),  Andi Licious

**May 2010:**

* [Make Your Blog 10x Faster With W3 Total Cache Plug-in](http://www.strictlyonlinebiz.com/blog/speed-up-wordpress-with-w3-total-cache/1231/), Udegbunam Chukwudi
* [xCache v1.3.0 Now Available](http://webcache.googleusercontent.com/search?q=cache%3Ahttp%3A%2F%2Fresellr.net%2Fxcache-now-available%2F&rls=com.microsoft:en-us&ie=UTF-8&oe=UTF-8&startIndex=&startPage=1&rlz=1I7GGIE_en&redir_esc=&ei=NO49TNaAFIH60wS2zuXLDg), Michael
* [Maximize WordPress and BuddyPress Performance With W3 Total Cache](http://wpmu.org/maximize-wordpress-and-buddypress-performance-with-w3-total-cache/), Sarah Gooding
* [Is Your Wordpress Blog Slow to Load?](http://homenotion.com/blog/blogs/is-your-wordpress-blog-slow-to-load/), Elizabeth McGee

**April 2010:**

* [WordPress Optimization: How I Reduced Page Load Time by 75%](http://www.kadavy.net/blog/posts/wordpress-optimization-dreamhost-rackspace/), David Kadavy
* [Top 10 Wordpress Plugins Your Blog Should Have (Video)](http://www.blogsuccessjournal.com/blog-tips-and-advice/wordpress-tips-advice/top-10-wordpress-plugins-your-blog-should-have-video/), Dan &amp; Jennifer
* [Super or Total? Money Talks But Cache Rules](http://website-in-a-weekend.net/website-maintenance/super-total-money-talks-cache-rules/), Dave Thackeray
* [W3 Total Cache, the most comprehensive cache plugin in WordPress](http://blogandweb.com/wordpress/w3-total-cache-plugin-cache-wordpress/), Francisco Oliveros
* [10 OF THE BEST WORDPRESS PLUGINS IN 2010](http://www.sitesketch101.com/best-wordpress-plugins), Nicholas Cardot

**March 2010:**

* [Howto: Speed up WordPress sites by using Amazon Cloudfront](http://www.jitscale.com/howto-speed-up-wordpress-sites-by-using-amazon-cloudfront/), Niek Waarbroek
* [Wordpress Cache Plugin Benchmarks](http://cd34.com/blog/scalability/wordpress-cache-plugin-benchmarks/), Chris Davies
* [Wordpress + W3 Total Cache + MaxCDN How-To](http://rackerhacker.com/2010/02/13/wordpress-w3-total-cache-maxcdn/), Major Hayden

**February 2010:**

* [Blog Building: How To Dramatically Speed Up Your WordPress Site with W3 Total Cache](http://nimopress.com/pressed/blog-building-how-to-dramatically-speed-up-your-wordpress-site-with-w3-total-cache/), Nicholas Ong
* [Wordpress + W3 Total Cache + MaxCDN How-To](http://rackerhacker.com/2010/02/13/wordpress-w3-total-cache-maxcdn/), Major Hayden
* [Utilizing W3 Total Cache](http://www.reviewkin.com/utilizing-w3-total-cache/), Anangga Pratama
* [Shared Hosting vs. Cloud Hosting](http://gregrickaby.com/2010/02/shared-hosting-vs-cloud-hosting.html), Greg Rickaby
* [My Thoughts on Premium Plugins](http://weblogtoolscollection.com/archives/2010/02/04/my-thoughts-on-premium-plugins/), Ronald Huereca
* [W3 Total Cache Plugin for Wordpress Eats WP Super Cache's Lunch!](http://human3rror.com/w3-total-cache-plugin-for-wordpress-eats-wp-super-caches-lunch/), John Saddington

**January 2010:**

* [WordPress Cacheing with W3 Total Cache](http://blog.whoishostingthis.com/2010/01/19/wordpress-cacheing-w3-total-cache/), Jonathan
* [Configuring W3 Total Cache for WordPress](http://translate.google.com/translate?js=y&prev=_t&hl=en&ie=UTF-8&layout=1&eotf=1&u=http://da.clausheinrich.com/w3-total-cache-wordpress/&sl=auto&tl=en)
* [Wordpress load test part 2 - amendment](http://loadimpact.com/blog/wordpress-load-test-part-2-amendment), Erik Torsner
* [Wordpress - Accelerate your site with W3 Total Cache](http://translate.google.com/translate?hl=en&sl=auto&tl=en&u=http://www.egonomik.com/2010/01/wordpress-w3-total-cache-ile-sitenizi-hizlandirin-sunucunuzu-rahatlatin/), Caner Phenix

**December 2009:**

* [WordPress Plugin &mdash; Best of 4 Caching Plugins](http://nimopress.com/pressed/wordpress-plugin-best-of-4-caching-plugins/), Nicholas Ong
* [Speed Up Your Blog With W3 Total Cache &amp; Amazon](http://www.freedomtarget.com/w3-total-cache-with-amazon-s3-and-cloudfront), Kevin McKillop
* [W3 Total Cache with Amazon S3 and CloudFront](http://kovshenin.com/archives/w3-total-cache-with-amazon-s3-and-cloudfront/), Konstantin Kovshenin

**November 2009:**

* [How to Boost Ad Revenue: Speed is Your Secret Weapon](http://blog.buysellads.com/2009/11/how-to-boost-ad-revenue-speed-is-your-secret-weapon/), Todd Garland

**October 2009:**

* [Plugin: WordPress Caching with CDN Integration](http://www.blogperfume.com/plugin-wordpress-caching-with-cdn-integration/)
* [8 Powerful Wordpress Plugins You Probably Don't Use But Should](http://www.smashingapps.com/2009/10/19/8-powerful-wordpress-plugins-you-probably-dont-use-but-should.html), AN Jay
* [Beyond Super Cache: W3 Total Cache](http://www.webmaster-source.com/2009/10/15/beyond-super-cache-w3-total-cache/), Matt Harzewski

**September 2009:**

* [Why Noupe.com is Loading So Much Faster?](http://209.85.129.132/search?q=cache:PgY8haU_0I4J:www.noupe.com/spotlight/why-noupe-com-is-loading-pretty-fast.html+http://www.noupe.com/spotlight/why-noupe-com-is-loading-pretty-fast.html&cd=1&hl=en&ct=clnk&gl=it), Noura Yehia

**August 2009:**

* [W3 Total Cache Plugin](http://dougal.gunters.org/blog/2009/08/26/w3-total-cache-plugin), Dougal Campbell

**July 2009:**

* [W3 Total Cache](http://weblogtoolscollection.com/pluginblog/2009/07/27/w3-total-cache/)

== Who do I thank for all of this? ==

It's quite difficult to recall all of the innovators that have shared their thoughts, code and experiences in the blogosphere over the years, but here are some names to get you started:

* [Steve Souders](http://stevesouders.com/)
* [Steve Clay](http://mrclay.org/)
* [Ryan Grove](http://wonko.com/)
* [Nicholas Zakas](http://www.nczonline.net/blog/2009/06/23/loading-javascript-without-blocking/)
* [Ryan Dean](http://rtdean.livejournal.com/)
* [Andrei Zmievski](http://gravitonic.com/)
* George Schlossnagle
* Daniel Cowgill
* [Rasmus Lerdorf](http://toys.lerdorf.com/)
* [Gopal Vijayaraghavan](http://t3.dotgnu.info/)
* [Bart Vanbraban](http://eaccelerator.net/)
* [mOo](http://xcache.lighttpd.net/)

Please reach out to all of these people and support their projects if you're so inclined.

== Changelog ==

= 0.9.3 =
* Added support for extensions
* Added support for WordPress SEO image filter and CDN
* Added file exclusions for media query string logic
* Added user agents to user agents groups
* Added CDN FTP path / host test
* Fixed object cache and database cache for localization plugins
* Fixed chinese filenames when using CDN
* Fixed removal of stale cached files
* Fixed missing slashes in inline HTML, JS and CSS files when using CDN
* Fixed auto mode of minify filename length test
* Fixed NetDNA / MaxCDN testing when domain does not match domain zone settings
* Fixed CurlException and NetDNA / MaxCDN
* Fixed pull zone dropdown not showing or showing wrong zone
* Fixed trailing slash and redirect with apache
* Fixed false notification for page cache rules verification
* Fixed duplicate notifications for FTP
* Fixed empty FTP form
* Fixed add-in file validation
* Fixed browser cache headers for proxy cases
* Fixed wrong slash in Minify filepaths on windows based sites
* Fixed settings link in minify test failure and multisite
* Fixed missing param in canonical link generation
* Fixed PHP 5.2 compatibility
* Fixed handling of minify in preview mode
* Fixed order of operation issue on install tab for nginx
* Fixed translatable strings handling
* Fixed page cache debug mode issues
* Fixed home URL handling in multisite
* Fixed manual minify mode and path based file source for sub-directory installations
* Fixed path not set in disk enhanced caching
* Fixed page cache rewrite rule detection
* Improved security with esc_* usage
* Improved backend performance with extensive refactoring

= 0.9.2.11 =
* Fixed issue with MaxCDN and authorization key
* Fixed issue with WordPress in subfolder
* Fixed issue with moved/renamed wp-content and plugin folder
* Fixed issue with CDN and Minify Auto in Network with site wide CDN settings
* Fixed issue with late init
* Fixed minor UI issue
* Fixed issue with possible OAuth name conflict
* Fixed issue with CDN and cnames replacement of minify urls
* Fixed issue with HTML debug comments being added to unsupported content
* Improved minify auto filename for single install
* Added minor improvement to NFS optimization with Page Cache
* Added option to disable the Minify Auto filename length test and use a manual value
* Removed unused file

= 0.9.2.10 =
* Added NetDNA / MaxCDN dashboard widget
* Added zone dropdown for NetDNA and MaxCDN CDNs
* Added create pull zone for NetDNA and MaxCDN CDNs
* Added notification(s) if incompatible plugins are detected
* Added "Disable CDN on the following pages" setting
* Added "Disable CDN on SSL pages" setting
* Added late init support for page cache's fragment caching
* Added POT file
* Added translations NL, PL
* Fixed setenvif issue in htaccess rules
* Fixed failed rewrite (404) and not caching feeds in nginx
* Fixed NetDNA / MaxCDN CNAME purging issues and "Test CDN" functionality
* Fixed Azure container handling
* Fixed New Relic verification
* Fixed theme preview
* Fixed wrong path issue with w3tc-wp-loader.php
* Fixed auto mode of Minify and filename lengths
* Fixed purging monthly archives with Varnish
* Fixed insert, delete, update quries purging with database cache
* Fixed HTTP compression when dynamic page fragments are used
* Fixed PHP_STRING and other warnings
* Fixed case sensitive header detection
* Fixed HEAD request handling
* Fixed event scheduling propagation in Multi Site
* Improved CDN custom file paths
* Improved handling of legacy config files
* Improved NetDNA / MaxCDN alias, key, secret string handling (backwards compatible)
* Improved auto mode for minify file naming which was causing unncessary issues
* Improved handling of hardcoded WordPress paths
* Improved the file management functionality (FTP support, file permissions etc)
* Improved disk cache directory usage
* Improved garbage collection of expired cache files for disk enhanced method of page caching
* Improved drop-in verification, only adds required drop-ins
* Improved New Relic error notification handling
* Improved New Relic "app name" settings with Multi Site
* Improved cacheable mime type detection in Page Cache
* Improved ob_callback performance and reduced number of output buffer calls
* Improved minify handling with CDN and Multi Site
* Improved Object Cache purge policy performance
* Improved Database Cache purge policy performance
* Improved handling of request methods other than GET, POST for page caching
* Removed setting of New Relic app name in htaccess; set manually or use the dynamic setting on the "Monitoring" tab

= 0.9.2.9 =
* Fixed saving settings issue with CloudFlare on activation
* Fixed issue with DbCache and Object Cache flushing
* Improved security for CloudFlare API calls
* Improved security for mfunc, now disabled by default and requires security string in order to execute: CVE-2013-2010
* Improved behavior of GET string, used for detecting blogs in disk enhanced page caching mode for multi site to prevent redirect loops and other issues

= 0.9.2.8 =
* Fixed /tmp file creation issue when saving settings
* Fixed auto minify
* Fixed New Relic API key verification
* Fixed uncommon issue with require ObjectCache failing
* Fixed uncommon open_basedir verification issue
* Improved notification handling by modified WordPress FileSystem API implementation
* Added PHP file handling to support cases not covered by default WordPress FileSystem API

= 0.9.2.7 =
* Fixed config file write in more hosting environments
* Fixed legacy config file import on servers with hardened file permissions
* Fixed page cache write cache rules in some environments
* Fixed HiDPI images
* Fixed false positive notifications when permissions are not changed or same
* Fixed minify help
* Improved upgrade notifications
* Improved page cache and minify notifications

= 0.9.2.6 =
* Added support for grouping cache entries for easier flushing
* Added more options for placing minified files on pages. Custom placements using HTML comments.
* Added CSS inclusion for auto / manual minify. If present its used: <!-- W3TC-include-css -->
* Added JS inclusion for minify auto mode. If present its used: <!-- W3TC-include-js-head -->,
* Added JS inclusion for minify manual mode. If present they are used when selecting correspong location in placement dropdown: <!-- W3TC-include-js-head -->, <!-- W3TC-include-js-body-start -->, <!-- W3TC-include-js-body-end -->
* Added new minification inclusion options, async, defer, extsrc and asyncsrc
* Added flushing sitemaps
* Added flushing custom pages
* Added flush the current blog when flushing in MultiSite
* Added flushing home page and/or post page if static
* Added flushing older pages (page/2, page/3) on purge requests
* Added flushing a limited amount of pagenumbers
* Added purging caches for deleted blogs in WordPress MultiSite
* Added uninstall process to the plugin for easier cleanup
* Added upgrade support that cleans up and removes old cache folders
* Added message when disk enhanced page caching is not working properly
* Added custom wp-content name and locations
* Added CDN feature "Dont use CDN for specific roles"
* Added "Purge from cache" link on edit post/page pages
* Added permalink suffixes (.html, .htm etc)
* Added uploading blogs.dir files when using MultiSite and CDN
* Added notification about IAM when using Amazon services (SNS, CloudFront, S3 etc)
* Added 'Don't minify JS files' 'Don't minify CSS files' options for minify 
* Added Access-Control-Allow-Origin support for CDNs
* Added hooks clean_post_cache and menu changes
* Added caching JSON mime-type. Other mime-types can be added using array filter 'w3tc_is_cacheable_content_type'
* Added automatic reloading of config files when APC apc.stat is disabled
* Added request reload of APC file over HTTP
* Added priming of post on publish
* Added single config file (by default) when using WordPress MultiSite
* Added switch_to_blog() support
* Added wp_cache_decr, wp_cache_incr support
* Added wp_cache_switch_to_blog support
* Added fragment caching functionality that extends the transient caching methods in WordPress core
* Added network activation and network policy management
* Added control for comment cookie lifetime
* Added Go Daddy SUBDOMAIN_DOCUMENT_ROOT directive support
* Added JSON to mod_deflate configuration
* Added JavaScript mime-type variations to mod_deflate configuration
* Added WP-CLI support for purging cache, purging pull CDNs, reloading APC files, clearing APC, deleting pgcache files, and updating browser query string
* Added .htc mime type
* Added Rackspace CloudFiles container location selection
* Added rejected terms in database cache, to allow for deeper database caching capability
* Added Akamai CDN origin pull support
* Added system cache purge when apc.stat is disabled
* Added origin rel canonical support for CDNs
* Added AT&T origin pull CDN
* Added full-page mirroring and purging for origin pull CDNs
* Added compatibility mode improve disk enhanced page caching performance ~20% for users that don't care about interoperability
* Added nginx example files
* Added varnish example files
* Added basic application monitoring support using New Relic
* Added application monitoring widget using New Relic
* Added plugin dashboard page to display widgets etc
* Added purge from cache link to admin bar dropdown while browser posts/pages on front-end
* Added purge CDN completely link to admin bar dropdown
* Added CDN SSL checkbox "Disable CDN on SSL page"
* Added pull CDN support to MultiSite
* Fixed caching of the "Front Page" and added an option for the same
* Fixed w3_url_format function was not included error
* Fixed security vulnerability with file cache: disk basic page caching, database or object caching to disk. CVEs: CVE-2012-6077, CVE-2012-6078, CVE-2012-6079
* Fixed issue with concurrent writes to single config file
* Fixed Preview mode and deploy button
* Fixed varnish purging (See varnish config example file in ini folder)
* Fixed false positive notification on Page Cache: Disc Enhanced
* Fixed broken images for feedburner in dashboard on HTTPS sites
* Fixed minify ID generation causing notices
* Fixed false positive on minification related to group configuration among other
* Fixed default wp-content path and CDN uploads when WordPress is installed in directory
* Fixed pages not flushing when making changes in WordPress Admin
* Fixed mod_filter being used on unsupported Apache versions
* Fixed WordPress upgrade issue caused by W3TC remaining active
* Fixed minify path when using WordPress MultiSite and blogs in sub-directories
* Fixed user interface for "Dont cache specific roles"
* Fixed CDN and minify to file for storage
* Fixed feed_link filters interferring with feed purging
* Fixed false positive with minify rewrite test and WordPress MultiSite sub-directory mode
* Fixed WordPress Multisite in sub-directory mode and minify paths
* Fixed caching posts page when using static home page
* Fixed object cache not being initialised before getting options
* Fixed locating document root for minify in certain environments
* Fixed saving settings if permalinks are turned off
* Fixed "Install" page displaying rules that is not required
* Fixed minify cache being empty while page is cached so no minified files are generated, minified files auto generation no longer depend on pages not being cached
* Fixed unnecessary flushing with SSL, user agents and referrer not being activated
* Fixed "Purge from Page Cache" not purging varnish or CDN when enabled
* Fixed CSS minification and relative paths in external files
* Fixed login / logout and compatibility  with BuddyPress
* Fixed flushing same posts many times if status is trashed or restored
* Fixed handling of .html fancy permalinks with nginx
* Fixed handling of missing xcache opcode cache
* Fixed Amazon S3 allowing LIST permissions to everyone
* Fixed handling of .xml mime type
* Fixed numerous object cache compatibility issues
* Fixed memcached port support issue
* Fixed HTTP_HOST not defined warning issue
* Fixed xdebug not configured halt issue
* Fixed minified files not being mirrored by push CDNs
* Fixed uploading to CDNs multiple times even if force overwrite is disabled
* Improved CloudFlare support: purging, dev mode, minification options, automatic IP range updates, rocket loader feature, security levels
* Improved support for the Bad Behavior plugin
* Improved object cache and MultiSite compatibility
* Improved flushing behavior with better post and pages detection. To change behavior hook into filter 'w3tc_flushable_post'
* Improved XML user interface when using Page Cache Disc: Enhanced
* Improved minify filename path generation
* Improved minify custom placement usage
* Improved PHP 5 compatibility by removing deprecated functionality
* Improved WordPress 3.4 and 3.5 compatibility by removing deprecated functions usage
* Improved plugin by removing unnecessary ob_starts
* Improved minify usage by returning old minify files while generating new
* Improved update procedure by removing need to manually deactivate and reactivate plugin
* Improved CDN paths and URL generation using both single and multisite installs
* Improved Admin Bar menu with more flushing actions
* Improved minify auto by adding support for downloading and combining external files
* Improved descriptions for various "Do not cache ..." textareas
* Improved header settings so plugin header settings override WordPress default header where appropriate
* Improved debug logging by adding a debug folder constant: W3TC_DEBUG_DIR
* Improved support for WPTouch plugin
* Improved custom post type support and flushing
* Improved cache key format and handling
* Improved minify support on Windows
* Improved purging functionality for CDN providers
* Improved compatibility wp-fb-autoconnect plugin
* Improved support of WordPress installed when in a sub-directory
* Improved support of non-default WordPress folder setups
* Improved use of HTTP API and FileSystem API in WordPress. HTTP API usage improves minify and CDN functionality. FileSystem API integration improves activation, deactivation and configuration changes
* Improved execution time across all caching engines
* Improved cache miss performance for page caching
* Improved notification handling
* Improved cache priming algorithm
* Improved minify error reporting
* Improved varnish purging capability
* Improved memory caching logic and purging speed
* Improved support for auto minify in more hosting environments
* Improved support for use of sockets in memcached
* Improved handling of old settings upon update
* Improved detection of document root on additional hosting providers
* Improved handling of eTags
* Improved browser cache policy options
* Improved bug submission form implementation
* Improved NetDNA / MaxCDN support by using their new API
* Disabled minify if CloudFlare is active
* Removed PHP 4 support
* Removed cookie used for user agent groups unless the feature is used
* Removed cookie used for referrer unless the feature is used
* Removed duplicate inclusion of JSON library

= 0.9.2.5 =
* Fixed security issue that can occur if using database caching to disk. If using database caching to disk with a web server with directory listing or web accessible wp-content/w3tc/dbcache/* directories. This patch works for all hosting environments / types where PHP is properly configured, i.e. .htaccess modifications (or other web server configuration changes) are *not* necessary to ensure proper security. Empty the database cache after performing the update if you use database caching to disk.

= 0.9.2.4 =
* Added support for Microsoft SQL Server
* Added API support for MediaTemple ProCDN (EdgeCast)
* Added set_time_limit to self test
* Fixed LiteSpeed web server support issues
* Fixed native hostname 301 redirect
* Fixed redundant object origin push export
* Fixed WSOD (white screen of death) caused by minify in some hosting configurations
* Fixed text encoding in feeds
* Fixed incorrect mime-type in feeds (which caused feedburner anomalies)
* Fixed array to string notices
* Fixed expires header support for AWS
* Fixed minification of font-family
* Fixed object cache write issue in WP Admin
* Improved (reduced) memory utilization by up to 70%
* Improved disk enhanced page caching performance
* Improved object caching performance
* Improved activation reliability
* Improved reliability of minify auto mode
* Improved security (added nonces, no directory indexing, prevent direct file access)
* Improved compatibility with network based file systems

= 0.9.2.3 =
* Added additional CloudFlare IP ranges
* Fixed html tidy encoding
* Fixed NetDNA / MaxCDN purging
* Improved handling of markers in .htaccess files - easier upgrades
* Improved cache busting logic
* Improved numerous notifications and user interface behaviors
* Improved AWS S3 and Cloudfront reliability
* Improved reliability of minify auto mode

= 0.9.2.2 =
* Fixed minify directives, e.g.: "File param is missing," causing minify caching to fail
* Fixed document root detection for IIS server
* Fixed HTTP compression when using CloudFlare
* Fixed HTML validation with JavaScript embed tags
* Fixed fancy permalinks, sites with or without trailing slashes can now cache pages using disk enhanced
* Fixed appending WP_CACHE define into wp-config.php for some users
* Fixed path to JSON.php
* Fixed listing of buckets error with AWS S3
* Improved compatibility with WordPress SEO by Yoast, 404 error exception list sitemap value changed to: sitemap(_index|[0-9]+)?\.xml(\.gz)?

= 0.9.2.1 =
* Fixed existing installation upgrades: set minify to manual mode by default
* Fixed unsuccessful transfer queue button
* Fixed background in lightbox
* Fixed handling of local http requests being blocked on some hosts
* Disabled CDN for minify files when auto mode is selected and the CDN method is origin push

= 0.9.2 =
* Added support for nginx web server
* Added support for CloudFlare
* Added origin pull support for Amazon Cloudfront
* Added Microsoft Azure Storage support for CDN
* Added WinCache opcode cache support
* Added additional minifier engines for HTML, CSS and JS including: HTMLtidy, CSStidy, Closure Compiler, YUI Compressor
* Added Google Page Speed integration
* Added support for @import processing
* Added controls for page cache purging policy
* Added auto mode for minify (not compatible with CDN)
* Added support for set cookie domain setting
* Added reliability improvements for Amazon Web Services
* Added referrer group management for uniquely caching these cases
* Added Amazon S3 bucket location selection control
* Added support CNAMEs confguration support for Amazon Cloudfront
* Added purge tool
* Added support of custom wp-config.php location
* Added cache busting support
* Improved object caching performance when no plugins are active
* Improved non-blocking JS embedding implementation
* Improved reliability of CDN export operations
* Improved implementation of headers for all cache engines
* Improved minify help (recommendations) tool
* Improved handling of .htaccess directive changes
* Improved support of IIS web server
* Improved varnish support
* Fixed bugs with API changes with Rackspace Cloudfiles
* Fixed bugs with origin push content delivery network methods
* Fixed HTML encoding
* Fixed emptying cache for various cache keys
* Fixed rejected CDN file support
* Fixed HTTPS mode in WP Admin
* Fixed relative document root for disk enhanced page cache
* Fixed trailing slash for disk enhanced page cache
* Fixed minify template group settings being lost upon upgrade
* Fixed division by zero error
* Fixed object cache clones
* Moved browser cache rules to site root instead of document root

= 0.9.1.3 =
* Improved error messages with AWS S3 CDN
* Added SSL support for CDN
* Added control for CDN queue upload interval
* Added option for 404 file exceptions list in browser cache
* Added exception for NextGen Gallery flash image rotator to CDN settings
* Fixed external file imports
* Fixed document root detection for CDN
* Fixed minify file search
* Fixed bugs with AWS CloudFront distribution creation and saving
* Fixed Rackspace Cloud Files API

= 0.9.1.2 =
* Improved media library import compatibility
* Improved various notifications
* Changed expires implementation to last accessed instead of last modified
* Resolved Apache 1.3 compatibility issue
* Fixed issues with document root detection on some servers
* Fixed an issue with minification of script tags with HTML comments inside
* Fixed minify gzip compression
* Fixed cache-control headers
* Fixed empty fatal error notification on network activation
* Fixed minify when https is active
* Fixed fatal error upon activation when uploads path does not exist

= 0.9.1.1 =
* Added an additional notification to help users identify incomplete installations upon activation
* Reverted previous Cloud Files workaround
* Fixed preview mode buttons
* Fixed duplicate entries appearing when using minify help tool more than once
* Fixed browser cache rules generation for media files

= 0.9.1 =
* Improved Rackspace Cloud Files implementation
* Improved frequently asked questions implementation to support incomplete PHP distributions
* Fixed 500 Internal Server Error when upgrading with Disk enhanced mode enabled
* Fixed notification issues with preview mode
* Fixed an issue with fatal errors with minify and memcache(d) caching engine

= 0.9 =
* Added preview feature so all cache settings can be reviewed prior to deployment
* Added minify configuration wizard (help button on minify tab)
* Added "never cache the following pages" to database and object cache
* Added minify option to JavaScript embed after &lt;body&gt;
* Added minify error notifications
* Added drag and drop dependency resolution for minify CSS / JavaScript groups
* Added object caching
* Added option to automatically page cache prime (preload) with or without XML sitemap
* Added support for multiple CNAMEs to CDN
* Added support for minifcation of any respective theme installed a single site
* Added support for page caching of multiple themes for various user agent groups
* Added support for theme switching / redirection based on groups of user agents
* Added compatibility with WP Super Cache fragment caching method (disk basic mode only)
* Added HTTP compression and headers for AWS S3
* Added ignored comment stems field, with Google AdSense default value to HTML minify
* Added support for varnish purging
* Added Rackspace Cloud Files support
* Added native NetDNA / MaxCDN integration
* Added option to cache 404 pages
* Added changed files auto-upload to CDN
* Added option to handle 404 errors for static objects directly with the web server
* Added support for gravity forms to database cache default settings
* Added changed file auto-upload to CDN
* Database connection errors now return internal server (500) error response and are thus not cached
* Incomplete plugin installation / removal no longer generates public errors
* Unterminated string errors no longer display publicly
* Support tab improvements
* Install tab improvements
* Resolved conflicts with disk enhanced .htaccess directives insertion
* Improved compatibility with all mobile plugins
* Improved AWS reliability
* Improved browser caching support and management
* Improved directory management for disk caching methods for increased performance
* Improved handling of missing minify files
* Improved Media Library import
* Improved Multi Site support
* Improved SSL interoperability

= 0.8.5.2 =
* Added support for Bad Behavior plugin
* Added support for eAccelerator 0.9.5.3 and XCache opcode caches
* Added support for rewriting href attribute of anchors for images that use the CDN
* Added deflate http compression support to minify
* Added setting of file modification time after FTP upload
* Added check of file modification time and file size before FTP upload
* Added check of file hash before uploading to Amazon S3
* Added option to force replacement of uploaded attachments on CDN
* Added option to toggle feed minification
* Added combine only option for CSS minification to overcome invalid CSS files
* Added configuration file import / export
* Database caching now disabled by default to improve compatibility with some shared hosting environments
* Default page caching method now disk enhanced
* Improved HTTP Compression inter-operability
* Improved compatibility with Multi Site Manager
* Improved apache compatibility
* Improved .htaccess directives
* Improved header management for posts / pages
* Improved notifications
* Improved minify handling of external files
* Improved minify memory limit to avoid blank pages when document size is large
* Improved minify reliability by reducing comment removal options
* Improved Media Library Import versatility
* Improved reliability of plugin activation in WordPress MU
* Improved security handling for some operations
* Improved reliability of handling file names containing spaces in CDN functionality
* Improved non-blocking embedding reliability
* Improved memcached detection by supporting only PECL memcache
* Fixed disk enhanced method of page cache in WordPress MU
* Fixed false match of page cache files in disk enhanced method of page cache
* Fixed cron anomalies caused by caching of wp_options table
* Fixed missing trailing slash issue when using disk enhanced page caching
* Fixed auto-embed bug CSS and JS when already manually embedded

= 0.8.5.1 =
* Added option to CDN Settings to skip specified directories
* Added option to allow for full control of HTTP compression options for page cache (some WordPress installations have issues with deflate)
* Added sql_calc_found_rows to default auto reject SQL list
* Added more notification cases identified and configured
* Added new mobile user agents for Japanese market
* Page cache performance improvements for disk enhanced mode
* Improved FAQ and option descriptions
* Improved apache directives for minify headers
* Improved handling of redirects
* Improved name space to avoid issues with other plugins
* Improved handling of incomplete installations, caching runs with default options if custom settings file does not exist
* Fixed anomalies with memcached-client.php in some environments
* Fixed another interface bug with management of minify files
* Fixed minor bug with table column length for some MySQL versions
* Fixed minify bug with CRLF
* Fixed minify bug with handling of zlib compression enabled
* Fixed handling of pages with CDN Media Library import

= 0.8.5 =
* Added "enhanced" disk caching mode for page cache, a 160% performance improvement over basic mode
* Added disk caching as an option for Database Cache
* Added CDN support for Amazon S3 and CloudFront
* Added mobile user agent rejection and redirect fields to page cache for handling mobile user agents
* Added Submit Bug Report tab
* Added support for detection of custom templates for minify groups
* Added separate controls expiration time field for minify and page cache settings
* Added PHP4 Support Notification to handle fatal errors on activation
* Improved database caching by 45%
* Improved handling of cache-control HTML headers
* Improved handing of 3rd Party CSS file minification
* Improved media library import reliability
* Improved handling of `DOCUMENT_ROOT` on some servers
* Improved garbage collection routine
* Improved handling of `<pre>` and `<textarea>` minification
* Improved handling of regular expressions in custom file list in CDN settings
* Improved handling of media library attachments in RSS feeds
* Improved handing of subdomains for CDN settings
* Improved various notifications and error messages
* Improved optional .htaccess directives
* Fixed JS minifcation saving group settings
* Fixed false positives for duplicate CSS or JS in minify settings
* Fixed bug causing settings to be lost on upgrade
* Fixed attachment URI when CDN mode enabled
* Fixed small bug with FTP upload when CDN Method is Mirror (Origin Pull)
* Fixed the URI for wlwmanfiest.xml when CDN enabled 
* Fixed handling of HTTPS objects according to options
* Fixed emptying disk cache under various obscure permutations
* Fixed handling of obscure open_basedir restrictions
* Fixed various bugs with emptying cache under various obscure permutations
* Fixed installations deeper than document root

= 0.8 =
* Added disk as method for page caching
* Added support for mirror (origin pull) content delivery networks
* Added options to specify minify group policies per template
* Added options for toggling inline CSS and JS minification to improve minify reliability
* Added option to update Media Library attachment hostnames (when migrating domains etc)
* Added "Empty Cache" buttons to respective tabs
* Added additional file download fallback methods for minify
* Improved cookie handling
* Improved header handling
* Improved reliability of Media Library import
* "Don't cache pages for logged in users" is now the default page cache setting
* Fixed minify bug with RSS feeds
* Fixed minify bug with rewriting of url() URI in CSS
* Addressed more page cache invalidity cases
* Addressed rare occurrence of PHP fatal errors when saving post or comments
* Addressed CSS bug on wp-login.php
* Addressed rare MySQL error when uploading attachments to Media Library
* Modified plugin file/directory structure
* Confirmed compatibility with varnish and squid

= 0.7.5.2 =
* Added warning dialog to minify tab about removal of query strings locally hosted object URIs
* Added empty (memcached) cache button to each tab
* Improved reliability of memcache flush
* Minified files now (optionally) upload automatically according to update interval (expiry time)
* Changed directory of minify working files to wp-content/w3tc-cache/
* Fixed parsing memcached server strings
* Fixed minify sometimes not creating files as it should
* Addressed WordPress network activation/deactivation issues
* Provided memcache.ini directives updated to improve network throughput

= 0.7.5.1 =
* Added memcached test button for convenience
* Added option to concatenate any script to header or footer with non-blocking options for scripts that cannot be minified (e.g. obfuscated scripts)
* Added options to concatenate JS files only in header or footer (for use with obfuscated scripts)
* Improved notification handling
* Improved compatibility with suPHP
* Improved reliability of Media Library Export
* Fixed database cache that caused comment counts to become out of date
* Fixed minor issue with URI with CDN functionality enabled
* Removed unnecessary minify options
* Minification error dialogs now disabled when JS or CSS minify settings disabled
* Normalized line endings with /n as per minify author's direction
* Resolved bug in the minify library preventing proper permission notification messages

= 0.7.5 =
* Added handling for magic_quotes set to on
* Fixed issue with auto-download/upgrade and additional error checking
* Fixed bug preventing minify working properly if either CSS or JS minification was disabled
* Improved handling of inline comments and JavaScript in HTML documents
* Improved handing of @import CSS embedding
* Addressed privilege control issue
* Resolved warnings thrown in various versions of WordPress
* Memcached engine logic modified to better support clustering and multiple memcached instances
* Eliminated false negatives in a number of gzip / deflate compression analysis tools
* Total plugin file size reduced

= 0.7 =
* Added minify support for URIs starting with /
* WordPress network mode support bug fixes
* Minor CDN uploader fixes
* Minor error message improvements

= 0.6 =
* Added "Debug Mode" listing all settings and queries with statistics
* Improved error message notifications
* Improved cache stability for large objects
* FAQ and installation instructions corrections/adjustments
* Support for multiple wordpress installations added
* Resolved bug in minification of feeds

= 0.5 =
* Initial release

== Upgrade Notice ==

= 0.9.2.10 =
Thanks for using W3 Total Cache! As always, this update is very strongly recommended. This release includes performance improvements for every type of caching and numerous bug fixes and stability / compatbility improvements. Make sure to keep W3TC updated to ensure optimal reliability and security.

= 0.9.2.8 =
Thanks for using W3 Total Cache! As always, this update is very strongly recommended. The recent releases attempted to use WordPress' built in support for managing files and folders and clearly has not worked. Since W3TC is a caching plugin, file management is a critical issue that will cause lots of issues if it doesn't work perfectly. This release is hopefully the last attempt to restore file management back to the reliability of previous versions (0.9.2.4 etc). We realize that having *any* problems is not acceptable, but caching means changing server behavior so while this plugin is still in pre-release we're trying to work on learning the lessons.