<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
?>
	<div id="barra">
		<ul>
			<?php if ( !dynamic_sidebar("events") ) : ?>
			<?php endif; ?>
<script src="http://widgets.twimg.com/j/2/widget.js"></script>
    <script>
    new TWTR.Widget({
      version: 2,
      type: 'search',
      search: '#mozillahispano',
      interval: 6000,
      title: 'Mozilla Hispano',
      subject: '#mozillahispano',
      width: 245,
      height: 600,
      theme: {
        shell: {
          background: 'transparent',
          color: '#444444'
        },
        tweets: {
          background: '#ffffff',
          color: '#444444',
          links: '#264373'
        }
      },
      features: {
        scrollbar: true,
        loop: false,
        live: true,
        hashtags: false,
        timestamp: true,
        avatars: true,
        toptweets: false,
        behavior: 'all'
      }
    }).render().start();
</script>
		</ul>
	</div>

