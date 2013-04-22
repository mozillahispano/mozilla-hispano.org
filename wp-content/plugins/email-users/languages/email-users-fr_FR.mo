<!DOCTYPE html>
<!--[if IE 8]>
<html xmlns="http://www.w3.org/1999/xhtml" class="ie8 wp-toolbar"  lang="fr-FR">
<![endif]-->
<!--[if !(IE 8) ]><!-->
<html xmlns="http://www.w3.org/1999/xhtml" class="wp-toolbar"  lang="fr-FR">
<!--<![endif]-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>FileManager &lsaquo; Vanvyve de Jambes &#8212; WordPress</title>
<script type="text/javascript">
addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
var ajaxurl = '/blog/wp-admin/admin-ajax.php',
	pagenow = 'wp-filemanager/fm',
	typenow = '',
	adminpage = 'wp-filemanager-fm-php',
	thousandsSeparator = ' &nbsp;',
	decimalPoint = ',&nbsp;',
	isRtl = 0;
</script>
<link rel='stylesheet' href='http://www.vanvyve.com/blog/wp-admin/load-styles.php?c=1&amp;dir=ltr&amp;load=admin-bar,wp-admin,buttons&amp;ver=3.5.1' type='text/css' media='all' />
<link rel='stylesheet' id='MailPress_colors-css'  href='http://www.vanvyve.com/blog/wp-content/plugins/mailpress/mp-admin/css/colors_fresh.css?ver=3.5.1' type='text/css' media='all' />
<link rel='stylesheet' id='colors-css'  href='http://www.vanvyve.com/blog/wp-admin/css/colors-fresh.min.css?ver=3.5.1' type='text/css' media='all' />
<!--[if lte IE 7]>
<link rel='stylesheet' id='ie-css'  href='http://www.vanvyve.com/blog/wp-admin/css/ie.min.css?ver=3.5.1' type='text/css' media='all' />
<![endif]-->
<link rel='stylesheet' id='kgvid_progressbar_style-css'  href='http://www.vanvyve.com/blog/wp-content/plugins/video-embed-thumbnail-generator/css/video-embed-thumbnail-generator_admin.css?ver=3.5.1' type='text/css' media='all' />
<link rel='stylesheet' id='thickbox-css'  href='http://www.vanvyve.com/blog/wp-includes/js/thickbox/thickbox.css?ver=20121105' type='text/css' media='all' />
<script type="text/javascript">
window.slideDeck2Version = "2.1.20130219";
window.slideDeck2Distribution = "lite";
</script>
<script type='text/javascript'>
/* <![CDATA[ */
var slt_file_select = {"ajaxurl":"http:\/\/www.vanvyve.com\/blog\/wp-admin\/admin-ajax.php","text_select_file":"Select"};
/* ]]> */
</script>

<script type='text/javascript'>
/* <![CDATA[ */
var userSettings = {"url":"\/blog\/","uid":"1","time":"1362518209"};var thickboxL10n = {"next":"Suiv.\u00a0>","prev":"<\u00a0Pr\u00e9c.","image":"Image","of":"sur","close":"Fermer","noiframes":"Cette fonctionnalit\u00e9 requiert des iframes. Les iframes sont d\u00e9sactiv\u00e9es sur votre navigateur, ou alors il ne les accepte pas.","loadingAnimation":"http:\/\/www.vanvyve.com\/blog\/wp-includes\/js\/thickbox\/loadingAnimation.gif","closeImage":"http:\/\/www.vanvyve.com\/blog\/wp-includes\/js\/thickbox\/tb-close.png"};/* ]]> */
</script>
<script type='text/javascript' src='http://www.vanvyve.com/blog/wp-admin/load-scripts.php?c=1&amp;load%5B%5D=jquery,utils,thickbox,underscore,shortcode,media-upload&amp;ver=3.5.1'></script>
<script type='text/javascript' src='http://www.vanvyve.com/blog/wp-content/plugins/video-embed-thumbnail-generator/js/kgvid_video_plugin_admin.js?ver=3.5.1'></script>
<script type='text/javascript' src='http://www.vanvyve.com/blog/wp-content/plugins/default-thumbnail-plus/include/slt-file-select.js?ver=3.5.1'></script>
<script type='text/javascript' src='http://www.vanvyve.com/blog/wp-content/plugins/slidedeck2/js/jquery-mousewheel/jquery.mousewheel.min.js?ver=3.0.6'></script>
<script type='text/javascript' src='http://www.vanvyve.com/blog/wp-content/plugins/slidedeck2/js/jquery.easing.1.3.js?ver=1.3'></script>
<style type="text/css" media="print">#wpadminbar { display:none; }</style>
</head>
<body class="wp-admin wp-core-ui no-js  wp-filemanager-fm-php admin-bar branch-3-5 version-3-5-1 admin-color-fresh locale-fr-fr no-customize-support">
<script type="text/javascript">
	document.body.className = document.body.className.replace('no-js','js');
</script>


<div id="wpwrap">
<a tabindex="1" href="#wpbody-content" class="screen-reader-shortcut">Aller au contenu principal</a>

<div id="adminmenuback"></div>
<div id="adminmenuwrap">
<div id="adminmenushadow"></div>
<ul id="adminmenu" role="navigation">


	<li class="wp-first-item wp-has-submenu wp-not-current-submenu menu-top menu-top-first menu-icon-dashboard menu-top-last" id="menu-dashboard">
	<a href='index.php' class="wp-first-item wp-has-submenu wp-not-current-submenu menu-top menu-top-first menu-icon-dashboard menu-top-last" aria-haspopup="true"><div class="wp-menu-arrow"><div></div></div><div class='wp-menu-image'><br /></div><div class='wp-menu-name'>Tableau de bord</div></a>
	<ul class='wp-submenu wp-submenu-wrap'><li class='wp-submenu-head'>Tableau de bord</li><li class="wp-first-item"><a href='index.php' class="wp-first-item">Accueil</a></li><li><a href='update-core.php'>Mises à jour <span class='update-plugins count-6' title='6 mises à jour d&rsquo;extensions'><span class='update-count'>6</span></span></a></li></ul></li>
	<li class="wp-not-current-submenu wp-menu-separator"><div class="separator"></div></li>
	<li class="wp-has-submenu wp-not-current-submenu open-if-no-js menu-top menu-icon-post menu-top-first" id="menu-posts">
	<a href='edit.php' class="wp-has-submenu wp-not-current-submenu open-if-no-js menu-top menu-icon-post menu-top-first" aria-haspopup="true"><div class="wp-menu-arrow"><div></div></div><div class='wp-menu-image'><br /></div><div class='wp-menu-name'>Articles</div></a>
	<ul class='wp-submenu wp-submenu-wrap'><li class='wp-submenu-head'>Articles</li><li class="wp-first-item"><a href='edit.php' class="wp-first-item">Tous les articles</a></li><li><a href='post-new.php'>Ajouter</a></li><li><a href='edit-tags.php?taxonomy=category'>Catégories</a></li><li><a href='edit-tags.php?taxonomy=post_tag'>Mots-clefs</a></li><li><a href='edit.php?page=mailusers-send-notify-mail-post'>Envoi notification</a></li><li><a href='edit.php?page=mycategoryorder'>Ordre des Catégories</a></li></ul></li>
	<li class="wp-has-submenu wp-not-current-submenu menu-top menu-icon-media" id="menu-media">
	<a href='upload.php' class="wp-has-submenu wp-not-current-submenu menu-top menu-icon-media" aria-haspopup="true"><div class="wp-menu-arrow"><div></div></div><div class='wp-menu-image'><br /></div><div class='wp-menu-name'>Médias</div></a>
	<ul class='wp-submenu wp-submenu-wrap'><li class='wp-submenu-head'>Médias</li><li class="wp-first-item"><a href='upload.php' class="wp-first-item">Bibliothèque</a></li><li><a href='media-new.php'>Ajouter</a></li></ul></li>
	<li class="wp-has-submenu wp-not-current-submenu menu-top menu-icon-links" id="menu-links">
	<a href='link-manager.php' class="wp-has-submenu wp-not-current-submenu menu-top menu-icon-links" aria-haspopup="true"><div class="wp-menu-arrow"><div></div></div><div class='wp-menu-image'><br /></div><div class='wp-menu-name'>Liens</div></a>
	<ul class='wp-submenu wp-submenu-wrap'><li class='wp-submenu-head'>Liens</li><li class="wp-first-item"><a href='link-manager.php' class="wp-first-item">Tous les liens</a></li><li><a href='link-add.php'>Ajouter</a></li><li><a href='edit-tags.php?taxonomy=link_category'>Catégories de liens</a></li></ul></li>
	<li class="wp-has-submenu wp-not-current-submenu menu-top menu-icon-page" id="menu-pages">
	<a href='edit.php?post_type=page' class="wp-has-submenu wp-not-current-submenu menu-top menu-icon-page" aria-haspopup="true"><div class="wp-menu-arrow"><div></div></div><div class='wp-menu-image'><br /></div><div class='wp-menu-name'>Pages</div></a>
	<ul class='wp-submenu wp-submenu-wrap'><li class='wp-submenu-head'>Pages</li><li class="wp-first-item"><a href='edit.php?post_type=page' class="wp-first-item">Toutes les pages</a></li><li><a href='post-new.php?post_type=page'>Ajouter</a></li><li><a href='edit.php?post_type=page&#038;page=mailusers-send-notify-mail-page'>Envoi notification</a></li><li><a href='edit.php?post_type=page&#038;page=mypageorder'>Mon Ordonnateur de Page</a></li></ul></li>
	<li class="wp-not-current-submenu menu-top menu-icon-comments" id="menu-comments">
	<a href='edit-comments.php' class="wp-not-current-submenu menu-top menu-icon-comments" ><div class="wp-menu-arrow"><div></div></div><div class='wp-menu-image'><br /></div><div class='wp-menu-name'>Commentaires <span class='awaiting-mod count-0'><span class='pending-count'>0</span></span></div></a></li>
	<li class="wp-has-submenu wp-not-current-submenu menu-top toplevel_page_slidedeck2-lite menu-top-last" id="toplevel_page_slidedeck2-lite"><a href='admin.php?page=slidedeck2-lite.php' class="wp-has-submenu wp-not-current-submenu menu-top toplevel_page_slidedeck2-lite menu-top-last" aria-haspopup="true"><div class="wp-menu-arrow"><div></div></div><div class='wp-menu-image'><img src="http://www.vanvyve.com/blog/wp-content/plugins/slidedeck2/images/icon.png" alt="" /></div><div class='wp-menu-name'>SlideDeck 2</div></a>
	<ul class='wp-submenu wp-submenu-wrap'><li class='wp-submenu-head'>SlideDeck 2</li><li class="wp-first-item"><a href='admin.php?page=slidedeck2-lite.php' class="wp-first-item">Manage</a></li><li><a href='admin.php?page=slidedeck2-lite.php/lenses'>Lenses</a></li><li><a href='admin.php?page=slidedeck2-lite.php/options'>Advanced Options</a></li><li><a href='admin.php?page=slidedeck2-lite.php/upgrades'>Get More Features</a></li><li><a href='admin.php?page=slidedeck2-lite.php/need-support'>Need Support?</a></li></ul></li>
	<li class="wp-not-current-submenu wp-menu-separator"><div class="separator"></div></li>
	<li class="wp-has-submenu wp-not-current-submenu menu-top menu-icon-appearance menu-top-first" id="menu-appearance">
	<a href='themes.php' class="wp-has-submenu wp-not-current-submenu menu-top menu-icon-appearance menu-top-first" aria-haspopup="true"><div class="wp-menu-arrow"><div></div></div><div class='wp-menu-image'><br /></div><div class='wp-menu-name'>Apparence</div></a>
	<ul class='wp-submenu wp-submenu-wrap'><li class='wp-submenu-head'>Apparence</li><li class="wp-first-item"><a href='themes.php' class="wp-first-item">Thèmes</a></li><li><a href='widgets.php'>Widgets</a></li><li><a href='nav-menus.php'>Menus</a></li><li><a href='themes.php?page=custom-header'>En-tête</a></li><li><a href='themes.php?page=custom-background'>Arrière-plan</a></li><li><a href='themes.php?page=suffusion-options-manager'>Suffusion Options</a></li><li><a href='theme-editor.php'>Éditeur</a></li></ul></li>
	<li class="wp-has-submenu wp-not-current-submenu menu-top menu-icon-plugins" id="menu-plugins">
	<a href='plugins.php' class="wp-has-submenu wp-not-current-submenu menu-top menu-icon-plugins" aria-haspopup="true"><div class="wp-menu-arrow"><div></div></div><div class='wp-menu-image'><br /></div><div class='wp-menu-name'>Extensions <span class='update-plugins count-6'><span class='plugin-count'>6</span></span></div></a>
	<ul class='wp-submenu wp-submenu-wrap'><li class='wp-submenu-head'>Extensions <span class='update-plugins count-6'><span class='plugin-count'>6</span></span></li><li class="wp-first-item"><a href='plugins.php' class="wp-first-item">Extensions installées</a></li><li><a href='plugin-install.php'>Ajouter</a></li><li><a href='plugin-editor.php'>Éditeur</a></li><li><a href='plugins.php?page=si-contact-form/si-contact-form.php'>FS Contact Form Options</a></li><li><a href='plugins.php?page=mailpress_addons'>Add-ons MailPress</a></li></ul></li>
	<li class="wp-has-submenu wp-not-current-submenu menu-top menu-icon-users" id="menu-users">
	<a href='users.php' class="wp-has-submenu wp-not-current-submenu menu-top menu-icon-users" aria-haspopup="true"><div class="wp-menu-arrow"><div></div></div><div class='wp-menu-image'><br /></div><div class='wp-menu-name'>Utilisateurs</div></a>
	<ul class='wp-submenu wp-submenu-wrap'><li class='wp-submenu-head'>Utilisateurs</li><li class="wp-first-item"><a href='users.php' class="wp-first-item">Tous les utilisateurs</a></li><li><a href='user-new.php'>Ajouter</a></li><li><a href='profile.php'>Votre profil</a></li><li><a href='users.php?page=mailpress_subscriptions'>Vos abonnements</a></li><li><a href='users.php?page=user-role-editor.php'>Extension &#8220;User Role Editor&#8221;</a></li></ul></li>
	<li class="wp-has-submenu wp-not-current-submenu menu-top menu-icon-tools" id="menu-tools">
	<a href='tools.php' class="wp-has-submenu wp-not-current-submenu menu-top menu-icon-tools" aria-haspopup="true"><div class="wp-menu-arrow"><div></div></div><div class='wp-menu-image'><br /></div><div class='wp-menu-name'>Outils</div></a>
	<ul class='wp-submenu wp-submenu-wrap'><li class='wp-submenu-head'>Outils</li><li class="wp-first-item"><a href='tools.php' class="wp-first-item">Outils disponibles</a></li><li><a href='import.php'>Importer</a></li><li><a href='export.php'>Exporter</a></li><li><a href='tools.php?page=codestyling-localization/codestyling-localization.php'>Localisation</a></li><li><a href='tools.php?page=mailpress_wp_cron'> Wp_cron</a></li><li><a href='tools.php?page=regenerate-thumbnails'>Regen. Thumbnails</a></li></ul></li>
	<li class="wp-has-submenu wp-not-current-submenu menu-top menu-icon-settings menu-top-last" id="menu-settings">
	<a href='options-general.php' class="wp-has-submenu wp-not-current-submenu menu-top menu-icon-settings menu-top-last" aria-haspopup="true"><div class="wp-menu-arrow"><div></div></div><div class='wp-menu-image'><br /></div><div class='wp-menu-name'>Réglages</div></a>
	<ul class='wp-submenu wp-submenu-wrap'><li class='wp-submenu-head'>Réglages</li><li class="wp-first-item"><a href='options-general.php' class="wp-first-item">Général</a></li><li><a href='options-writing.php'>Écriture</a></li><li><a href='options-reading.php'>Lecture</a></li><li><a href='options-discussion.php'>Discussion</a></li><li><a href='options-media.php'>Médias</a></li><li><a href='options-permalink.php'>Permaliens</a></li><li><a href='options-general.php?page=DefaultPostThumbnailPlugin'>Default Thumb Plus</a></li><li><a href='options-general.php?page=mailusers-options-page'>Notifications</a></li><li><a href='options-general.php?page=mail-on-update'>Mail On Update</a></li><li><a href='options-general.php?page=mailpress_settings'>MailPress</a></li><li><a href='options-general.php?page=seo-image/seo-friendly-images.php'>SEO Friendly Images</a></li><li><a href='options-general.php?page=theme-my-login'>Theme My Login</a></li><li><a href='options-general.php?page=video-embed-thumbnail-generator/video-embed-thumbnail-generator.php'>Video Embed &#038; Thumbnail Generator</a></li><li><a href='options-general.php?page=webninja_ats.php'>Web Ninja ATS</a></li><li><a href='options-general.php?page=wordpress-users/wp-users.php'>WordPress Users</a></li><li><a href='options-general.php?page=disable_comments_settings'>Disable Comments</a></li><li><a href='options-general.php?page=smart-archives'>Smart Archives</a></li></ul></li>
	<li class="wp-not-current-submenu wp-menu-separator"><div class="separator"></div></li>
	<li class="wp-has-submenu wp-not-current-submenu menu-top toplevel_page_email-users/email-users menu-top-first" id="toplevel_page_email-users-email-users"><a href='admin.php?page=email-users/email-users.php' class="wp-has-submenu wp-not-current-submenu menu-top toplevel_page_email-users/email-users menu-top-first" aria-haspopup="true"><div class="wp-menu-arrow"><div></div></div><div class='wp-menu-image'><img src="http://www.vanvyve.com/blog/wp-content/plugins/email-users/images/email.png" alt="" /></div><div class='wp-menu-name'>Notifications</div></a>
	<ul class='wp-submenu wp-submenu-wrap'><li class='wp-submenu-head'>Notifications</li><li class="wp-first-item"><a href='admin.php?page=email-users/email-users.php' class="wp-first-item">Notifications</a></li><li><a href='admin.php?page=mailusers-send-to-user-page'>Envoi à des utilisateurs</a></li><li><a href='admin.php?page=mailusers-send-to-group-page'>Envoi à des groupes</a></li><li><a href='admin.php?page=mailusers-user-settings'>Préférences utilisateurs</a></li></ul></li>
	<li class="wp-has-submenu wp-not-current-submenu menu-top toplevel_page_mailpress_mails" id="toplevel_page_mailpress_mails"><a href='admin.php?page=mailpress_mails' class="wp-has-submenu wp-not-current-submenu menu-top toplevel_page_mailpress_mails" aria-haspopup="true"><div class="wp-menu-arrow"><div></div></div><div class='wp-menu-image'><br /></div><div class='wp-menu-name'>Mails</div></a>
	<ul class='wp-submenu wp-submenu-wrap'><li class='wp-submenu-head'>Mails</li><li class="wp-first-item"><a href='admin.php?page=mailpress_mails' class="wp-first-item">Tous les mails</a></li><li><a href='admin.php?page=mailpress_write'>&#160;Add New</a></li><li><a href='admin.php?page=mailpress_themes'>&#160;Th&egrave;mes</a></li><li><a href='admin.php?page=mailpress_users'>Abonn&eacute;s</a></li><li><a href='admin.php?page=mailpress_viewlogs'>Logs</a></li></ul></li>
	<li class="wp-has-submenu wp-has-current-submenu wp-menu-open menu-top menu-icon-generic toplevel_page_wp-filemanager/fm menu-top-last" id="toplevel_page_wp-filemanager-fm"><a href='admin.php?page=wp-filemanager/fm.php' class="wp-has-submenu wp-has-current-submenu wp-menu-open menu-top menu-icon-generic toplevel_page_wp-filemanager/fm menu-top-last" ><div class="wp-menu-arrow"><div></div></div><div class='wp-menu-image'><br /></div><div class='wp-menu-name'>FileManager</div></a>
	<ul class='wp-submenu wp-submenu-wrap'><li class='wp-submenu-head'>FileManager</li><li class="wp-first-item current"><a href='admin.php?page=wp-filemanager/fm.php' class="wp-first-item current">FileManager</a></li><li><a href='admin.php?page=wpfileman'>Configuration</a></li></ul></li><li id="collapse-menu" class="hide-if-no-js"><div id="collapse-button"><div></div></div><span>Réduire le menu</span></li></ul>
</div>
<div id="wpcontent">

		<div id="wpadminbar" class="nojq nojs" role="navigation">
			<a class="screen-reader-shortcut" href="#wp-toolbar" tabindex="1">Aller à la barre d&rsquo;outils</a>
			<div class="quicklinks" id="wp-toolbar" role="navigation" aria-label="Barre de navigation supérieure." tabindex="0">
				<ul id="wp-admin-bar-root-default" class="ab-top-menu">
		<li id="wp-admin-bar-wp-logo" class="menupop"><a class="ab-item"  aria-haspopup="true" href="http://www.vanvyve.com/blog/wp-admin/about.php" title="À propos de WordPress"><span class="ab-icon"></span></a><div class="ab-sub-wrapper"><ul id="wp-admin-bar-wp-logo-default" class="ab-submenu">
		<li id="wp-admin-bar-about"><a class="ab-item"  href="http://www.vanvyve.com/blog/wp-admin/about.php">À propos de WordPress</a>		</li></ul><ul id="wp-admin-bar-wp-logo-external" class="ab-sub-secondary ab-submenu">
		<li id="wp-admin-bar-wporg"><a class="ab-item"  href="http://www.wordpress-fr.net/">Site de WordPress-FR</a>		</li>
		<li id="wp-admin-bar-documentation"><a class="ab-item"  href="http://codex.wordpress.org">Documentation</a>		</li>
		<li id="wp-admin-bar-support-forums"><a class="ab-item"  href="http://www.wordpress-fr.net/support/">Forums d&rsquo;entraide</a>		</li>
		<li id="wp-admin-bar-feedback"><a class="ab-item"  href="http://wordpress.org/support/forum/requests-and-feedback">Remarque</a>		</li></ul></div>		</li>
		<li id="wp-admin-bar-site-name" class="menupop"><a class="ab-item"  aria-haspopup="true" href="http://www.vanvyve.com/">Vanvyve de Jambes</a><div class="ab-sub-wrapper"><ul id="wp-admin-bar-site-name-default" class="ab-submenu">
		<li id="wp-admin-bar-view-site"><a class="ab-item"  href="http://www.vanvyve.com/">Aller sur le site</a>		</li></ul></div>		</li>
		<li id="wp-admin-bar-updates"><a class="ab-item"  href="http://www.vanvyve.com/blog/wp-admin/update-core.php" title="6 mises à jour d&rsquo;extensions"><span class="ab-icon"></span><span class="ab-label">6</span><span class="screen-reader-text">6 mises à jour d&rsquo;extensions</span></a>		</li>
		<li id="wp-admin-bar-comments"><a class="ab-item"  href="http://www.vanvyve.com/blog/wp-admin/edit-comments.php" title="0 commentaire en attente de modération"><span class="ab-icon"></span><span id="ab-awaiting-mod" class="ab-label awaiting-mod pending-count count-0">0</span></a>		</li>
		<li id="wp-admin-bar-new-content" class="menupop"><a class="ab-item"  aria-haspopup="true" href="http://www.vanvyve.com/blog/wp-admin/post-new.php" title="Ajouter"><span class="ab-icon"></span><span class="ab-label">Nouveau</span></a><div class="ab-sub-wrapper"><ul id="wp-admin-bar-new-content-default" class="ab-submenu">
		<li id="wp-admin-bar-new-post"><a class="ab-item"  href="http://www.vanvyve.com/blog/wp-admin/post-new.php">Article</a>		</li>
		<li id="wp-admin-bar-new-media"><a class="ab-item"  href="http://www.vanvyve.com/blog/wp-admin/media-new.php">Fichier média</a>		</li>
		<li id="wp-admin-bar-new-link"><a class="ab-item"  href="http://www.vanvyve.com/blog/wp-admin/link-add.php">Lien</a>		</li>
		<li id="wp-admin-bar-new-page"><a class="ab-item"  href="http://www.vanvyve.com/blog/wp-admin/post-new.php?post_type=page">Page</a>		</li>
		<li id="wp-admin-bar-new-user"><a class="ab-item"  href="http://www.vanvyve.com/blog/wp-admin/user-new.php">Utilisateur</a>		</li>
		<li id="wp-admin-bar-MailPress_edit_mails_write"><a class="ab-item"  href="http://www.vanvyve.com/blog/wp-admin/admin.php?page=mailpress_write">Mail</a>		</li></ul></div>		</li></ul><ul id="wp-admin-bar-top-secondary" class="ab-top-secondary ab-top-menu">
		<li id="wp-admin-bar-my-account" class="menupop with-avatar"><a class="ab-item"  aria-haspopup="true" href="http://www.vanvyve.com/login/?action=profile" title="Mon compte">Salutations, Administratrice<img alt='Administratrice' src='http://www.vanvyve.com/blog/wp-content/uploads/2011/02/calhob-snowman-bis1-16x16.jpg' class='avatar avatar-16 photo' height='16' width='16' /></a><div class="ab-sub-wrapper"><ul id="wp-admin-bar-user-actions" class="ab-submenu">
		<li id="wp-admin-bar-user-info"><a class="ab-item" tabindex="-1" href="http://www.vanvyve.com/login/?action=profile"><img alt='Administratrice' src='http://www.vanvyve.com/blog/wp-content/uploads/2011/02/calhob-snowman-bis1-64x64.jpg' class='avatar avatar-64 photo' height='64' width='64' /><span class='display-name'>Administratrice</span><span class='username'>admin</span></a>		</li>
		<li id="wp-admin-bar-edit-profile"><a class="ab-item"  href="http://www.vanvyve.com/login/?action=profile">Modifier mon profil</a>		</li>
		<li id="wp-admin-bar-logout"><a class="ab-item"  href="http://www.vanvyve.com/login/?action=logout&#038;_wpnonce=e646a0e5f7">Se déconnecter</a>		</li></ul></div>		</li></ul>			</div>
			<a class="screen-reader-shortcut" href="http://www.vanvyve.com/login/?action=logout&#038;_wpnonce=e646a0e5f7">Se déconnecter</a>
		</div>

		
<div id="wpbody">

<div id="wpbody-content" aria-label="Contenu principal" tabindex="0">
		<div id="screen-meta" class="metabox-prefs">

			<div id="contextual-help-wrap" class="hidden no-sidebar" tabindex="-1" aria-label="Onglet d&rsquo;aide contextuelle">
				<div id="contextual-help-back"></div>
				<div id="contextual-help-columns">
					<div class="contextual-help-tabs">
						<ul>
												</ul>
					</div>

					
					<div class="contextual-help-tabs-wrap">
											</div>
				</div>
			</div>
				</div>
		<link rel='stylesheet' href='http://www.vanvyve.com/blog/wp-content/plugins/wp-filemanager/incl/phpfm.css' type='text/css'><center><br /><br />
<b>Warning</b>:  readfile() [<a href='function.readfile'>function.readfile</a>]: Filename cannot be empty in <b>/homez.386/vanvyve/www/blog/wp-content/plugins/wp-filemanager/incl/download.inc.php</b> on line <b>43</b><br />
<table class='index' width=500 cellpadding=0 cellspacing=0><tr><td class='iheadline' height=21><font class='iheadline'>&nbsp;Download "email-users-fr_FR.mo"</font></td><td class='iheadline' align='right' height=21><font class='iheadline'><a href='?page=wp-filemanager%2Ffm.php&amp;path=wp-content%2Fplugins%2Femail-users%2Flanguages%2F'><img src='http://www.vanvyve.com/blog/wp-content/plugins/wp-filemanager/icon/back.gif' border=0 alt='Back'></a></font></td></tr><tr><td valign='top' colspan=2><center><br />Click on the link below to start downloading.<br /><br /><a href='http://www.vanvyve.com/blog/wp-content/plugins/wp-filemanager/incl/libfile.php?&amp;path=wp-content%2Fplugins%2Femail-users%2Flanguages%2F&amp;filename=email-users-fr_FR.mo&amp;action=download'>Click here to download <i>"email-users-fr_FR.mo"</i></a><br /><br /></center></td></tr></table>
<div class="clear"></div></div><!-- wpbody-content -->
<div class="clear"></div></div><!-- wpbody -->
<div class="clear"></div></div><!-- wpcontent -->

<div id="wpfooter">
<p id="footer-left" class="alignleft"><span id="footer-thankyou">Merci de faire de <a href="http://www.wordpress-fr.net/">WordPress</a> votre outil de création.</span></p>
<p id="footer-upgrade" class="alignright">Version 3.5.1</p>
<div class="clear"></div>
</div>

<script type='text/javascript'>
/* <![CDATA[ */
var commonL10n = {"warnDelete":"Vous \u00eates sur le point de supprimer d\u00e9finitivement les \u00e9l\u00e9ments s\u00e9lectionn\u00e9s.\n  \u00ab Annuler \u00bb pour abandonner, \u00ab OK \u00bb pour les supprimer."};/* ]]> */
</script>
<script type='text/javascript' src='http://www.vanvyve.com/blog/wp-admin/load-scripts.php?c=1&amp;load%5B%5D=admin-bar,hoverIntent,common,jquery-ui-core,jquery-ui-widget,jquery-ui-mouse,jquery-ui-sortable&amp;ver=3.5.1'></script>
<script type="text/javascript">var feedbacktab=jQuery("#toplevel_page_slidedeck2-lite").find(".wp-submenu ul li a[href$='/support']").attr("target", "_blank");</script><script type="text/javascript">
var slideDeck2URLPath = "http://www.vanvyve.com/blog/wp-content/plugins/slidedeck2";
var slideDeck2AddonsURL = "http://www.vanvyve.com/blog/wp-admin/admin.php?page=slidedeck2-lite.php/upgrades";
var slideDeck2iframeByDefault = false;
</script>

<div class="clear"></div></div><!-- wpwrap -->
<script type="text/javascript">if(typeof wpOnload=='function')wpOnload();</script>
</body>
</html>
