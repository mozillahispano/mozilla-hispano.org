<?php

class Stealth_Publish_Test extends WP_UnitTestCase {

	function tearDown() {
		parent::tearDown();
		c2c_StealthPublish::reset();
		// Ensure the filters get removed
		remove_action( 'pre_get_posts', array( $this, 'filter_on_special_meta' ), 1 );
		remove_filter( 'the_title',     array( $this, 'query_for_posts' ) );
		unset( $GLOBALS['custom_query'] );
	}



	/**
	 * HELPER FUNCTIONS
	 */



	private function stealthify( $post_id ) {
		add_post_meta( $post_id, '_stealth-publish', '1' );
	}



	/**
	 * FUNCTIONS FOR HOOKING ACTIONS/FILTERS
	 */

	public function query_for_posts( $text ) {
		$q = new WP_Query( array( 'post_type' => 'post' ) );
		$GLOBALS['custom_query'] = $q;
		return $text;
	}

	public function filter_on_special_meta( $wpquery ) {
		$wpquery->query_vars['meta_query'][] = array(
			'key'     => 'special',
			'value'   => '1',
			'compare' => '='
		);
	}



	/**
	 * TESTS
	 */



	function test_non_stealth_posts_not_affected_for_home() {
		$post_ids = $this->factory->post->create_many( 5 );

		$this->go_to( home_url() );

		$this->assertTrue( have_posts() );
		$this->assertEquals( 5, count( $GLOBALS['wp_query']->posts ) );
	}

	function test_stealth_post_not_listed_on_home() {
		$post_ids = $this->factory->post->create_many( 5 );

		$this->stealthify( $post_ids[0] );
		$this->go_to( home_url() );

		$this->assertTrue( have_posts() );
		$this->assertEquals( 4, count( $GLOBALS['wp_query']->posts ) );
	}

	function test_stealth_post_not_listed_on_front_page() {
		// Create 5 posts, one of which is stealth published
		$post_ids = $this->factory->post->create_many( 5 );
		$this->stealthify( $post_ids[0] );

		// Create a page to be the front page
		$page_id  = $this->factory->post->create( array( 'post_title' => 'Front', 'post_type' => 'page' ) );
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $page_id );

		// Go to the front page
		$this->go_to( home_url() );

		// Verify that the front page is used
		$this->assertTrue( have_posts() );
		$this->assertEquals( 1, count( $GLOBALS['wp_query']->posts ) );
		$this->assertEquals( $page_id, get_the_ID() );

		// Hook a function to the_title filter which will run a query in the
		// context of the front page.
		add_filter( 'the_title', array( $this, 'query_for_posts' ) );
		$this->assertEquals( 'Front', get_the_title() );

		// Ensure only 4 of the 5 posts get returned from the general query.
		$this->assertEquals( 4, count( $GLOBALS['custom_query']->posts ) );
	}

	function test_disabled_stealth_post_shows_on_home() {
		$post_ids = $this->factory->post->create_many( 5 );

		add_post_meta( $post_ids[0], '_stealth-publish', '0' );
		$this->go_to( home_url() );

		$this->assertTrue( have_posts() );
		$this->assertEquals( 5, count( $GLOBALS['wp_query']->posts ) );
	}

	function test_stealth_post_with_other_meta_query_not_listed_on_home() {
		$post_ids = $this->factory->post->create_many( 5 );

		$this->stealthify( $post_ids[0] );
		add_post_meta( $post_ids[0], 'special', '1' );
		add_post_meta( $post_ids[1], 'special', '1' );
		add_action( 'pre_get_posts', array( $this, 'filter_on_special_meta' ), 1 );
		$this->go_to( home_url() );

		$this->assertTrue( have_posts() );
		$this->assertEquals( 1, count( $GLOBALS['wp_query']->posts ) );
	}

	function test_non_stealth_posts_not_affected_for_feed() {
		$post_ids = $this->factory->post->create_many( 5 );

		$this->go_to( get_feed_link() );

		$this->assertTrue( have_posts() );
		$this->assertEquals( 5, count( $GLOBALS['wp_query']->posts ) );
	}

	function test_stealth_post_not_listed_on_feed() {
		$post_ids = $this->factory->post->create_many( 5 );

		$this->stealthify( $post_ids[0] );
		$this->go_to( get_feed_link() );

		$this->assertTrue( have_posts() );
		$this->assertEquals( 4, count( $GLOBALS['wp_query']->posts ) );
	}

	function test_disabled_stealth_post_shows_on_feed() {
		$post_ids = $this->factory->post->create_many( 5 );

		add_post_meta( $post_ids[0], '_stealth-publish', '0' );
		$this->go_to( get_feed_link() );

		$this->assertTrue( have_posts() );
		$this->assertEquals( 5, count( $GLOBALS['wp_query']->posts ) );
	}

	function test_non_stealth_post_publishes_without_silencing() {
		$post_id = $this->factory->post->create( array( 'post_status' => 'draft' ) );

		wp_publish_post( $post_id );

		$this->assertFalse( defined( 'WP_IMPORTING' ) );
	}

	/* This test must be last since it results in the WP_IMPORTING constant
	   being set. */

	function test_stealth_post_publishes_silently() {
		$post_id = $this->factory->post->create( array( 'post_status' => 'draft' ) );
		$this->stealthify( $post_id );

		// Publishing assumes it's coming from the edit page UI where the
		// checkbox is present to set the $_POST array element to trigger
		// stealth update
		$_POST['stealth_publish'] = '1';

		wp_publish_post( $post_id );

		$this->assertTrue( defined( 'WP_IMPORTING' ) );
	}

}
