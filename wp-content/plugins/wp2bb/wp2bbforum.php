<?php

// -------------------------------------------------------------------------------------------------
// PHPBB FUNCTIONS
// -------------------------------------------------------------------------------------------------

  define('IN_PHPBB', true);   
  if (!file_exists($phpbb_root_path . '/config.php')) {error_reporting(E_ERROR);}
  include($phpbb_root_path . 'config.php');
  include($phpbb_root_path . 'includes/utf/utf_tools.php');
  include($phpbb_root_path . 'includes/utf/utf_normalizer.php');
  include($phpbb_root_path . 'includes/db/dbal.php');
  include($phpbb_root_path . 'includes/db/db_tools.php');
  include($phpbb_root_path . 'includes/db/mysql.php');
  include($phpbb_root_path . 'includes/db/mysqli.php');
  include($phpbb_root_path . 'includes/functions.php');
  include($phpbb_root_path . 'includes/constants.php');
  include($phpbb_root_path . 'includes/auth.php');
  include($phpbb_root_path . 'includes/acm/acm_file.php');
  include($phpbb_root_path . 'includes/cache.php');
 

  /**
  * Slightly modified 'Submit Post' function in php_bb
  */
  function submit_post($auth, &$user, $subject, $username, $usercolor, $posttime, &$poll, &$data, $update_message = true)
  {
	global $db, $cache, $config, $phpEx, $template, $phpbb_root_path;
    
	$topic_type=POST_NORMAL;

        // retrieve user id
        $sql = 'SELECT user_id FROM ' . USERS_TABLE . ' WHERE username = "' . $username . '"';
        $result = $db->sql_query($sql);
	
		$topic_row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		$usid = $topic_row['user_id'];

        if($usid=='') {$usid=1; $username='';} //guest user
        $user->data['user_id']=$usid;

		$post_mode = 'post';
		$update_message = true;

		// First of all make sure the subject and topic title are having the correct length.
		// To achieve this without cutting off between special chars we convert to an array and then count the elements.
		$subject = truncate_string($subject);
		$data['topic_title'] = truncate_string($data['topic_title']);

		// Collect some basic information about which tables and which rows to update/insert
		$sql_data = $topic_row = array();
		$poster_id = ($mode == 'edit') ? $data['poster_id'] : (int) $user->data['user_id'];

		// Start the transaction here
		$db->sql_transaction('begin');

		$sql_data[POSTS_TABLE]['sql'] = array(
			'forum_id'			=> ($topic_type == POST_GLOBAL) ? 0 : $data['forum_id'],
			'poster_id'		=> $usid,
			'icon_id'			=> $data['icon_id'],
			'poster_ip'		=> '127.0.0.0',
			'post_time'		=> $posttime,
			'post_approved'	=> 1,
			'enable_bbcode'	=> $data['enable_bbcode'],
			'enable_smilies'	=> $data['enable_smilies'],
			'enable_magic_url'=> $data['enable_urls'],
			'enable_sig'		=> $data['enable_sig'],
			'post_username'	=> $username,
			'post_subject'		=> $subject,
			'post_text'		=> $data['message'],
			'post_checksum'	=> $data['message_md5'],
			'post_attachment'	=> (!empty($data['attachment_data'])) ? 1 : 0,
			'bbcode_bitfield'	=> '',
			'bbcode_uid'		=> $data['bbcode_uid'],
			'post_postcount'	=> ($auth->acl_get('f_postcount', $data['forum_id'])) ? 1 : 0,
			'post_edit_locked'=> $data['post_edit_locked']);


		$post_approved = $sql_data[POSTS_TABLE]['sql']['post_approved'];
		$topic_row = array();

		$sql_data[TOPICS_TABLE]['sql'] = array(
			'topic_poster'			=> 2,
			'topic_time'			=> $posttime,
			'forum_id'			=> ($topic_type == POST_GLOBAL) ? 0 : $data['forum_id'],
			'icon_id'			=> $data['icon_id'],
			'topic_approved'		=> 1,
			'topic_title'			=> $subject,
			'topic_first_poster_name'	=> $username,
			'topic_first_poster_colour'	=> $usercolor,
			'topic_type'			=> $topic_type,
			'topic_time_limit'		=> 0,
			'topic_attachment'		=> (!empty($data['attachment_data'])) ? 1 : 0,
		);

		if (isset($poll['poll_options']) && !empty($poll['poll_options']))
		{
			$sql_data[TOPICS_TABLE]['sql'] = array_merge($sql_data[TOPICS_TABLE]['sql'], array(
				'poll_title'		=> $poll['poll_title'],
				'poll_start'		=> ($poll['poll_start']) ? $poll['poll_start'] : $posttime,
				'poll_max_options'	=> $poll['poll_max_options'],
				'poll_length'		=> ($poll['poll_length'] * 86400),
				'poll_vote_change'	=> $poll['poll_vote_change'])
			);
		}

		$sql_data[USERS_TABLE]['stat'][] = "user_lastpost_time = $posttime" . (($auth->acl_get('f_postcount', $data['forum_id'])) ? ', user_posts = user_posts + 1' : '');
	
		if ($topic_type != POST_GLOBAL)
		{
			if ($auth->acl_get('f_noapprove', $data['forum_id']) || $auth->acl_get('m_approve', $data['forum_id']))
			{
				$sql_data[FORUMS_TABLE]['stat'][] = 'forum_posts = forum_posts + 1';
			}
			$sql_data[FORUMS_TABLE]['stat'][] = 'forum_topics_real = forum_topics_real + 1' . (($auth->acl_get('f_noapprove', $data['forum_id']) || $auth->acl_get('m_approve', $data['forum_id'])) ? ', forum_topics = forum_topics + 1' : '');
		}

		$sql = 'INSERT INTO ' . TOPICS_TABLE . ' ' .
			$db->sql_build_array('INSERT', $sql_data[TOPICS_TABLE]['sql']);

		$db->sql_query($sql);

		$data['topic_id'] = $db->sql_nextid();

		$sql_data[POSTS_TABLE]['sql'] = array_merge($sql_data[POSTS_TABLE]['sql'], array(
			'topic_id' => $data['topic_id'])
		);
		unset($sql_data[TOPICS_TABLE]['sql']);


		$sql = 'INSERT INTO ' . POSTS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_data[POSTS_TABLE]['sql']);
		$db->sql_query($sql);
		$data['post_id'] = $db->sql_nextid();

		$sql_data[TOPICS_TABLE]['sql'] = array(
			'topic_first_post_id'		=> $data['post_id'],
			'topic_last_post_id'		=> $data['post_id'],
			'topic_last_post_time'		=> $posttime,
			'topic_last_poster_id'		=> $usid,
			'topic_last_poster_name'	=> $username,
			'topic_last_poster_colour'	=> $usercolor);

		unset($sql_data[POSTS_TABLE]['sql']);

		$make_global = false;

		// Update the topics table
		if (isset($sql_data[TOPICS_TABLE]['sql']))
		{
			$sql = 'UPDATE ' . TOPICS_TABLE . '
				SET ' . $db->sql_build_array('UPDATE', $sql_data[TOPICS_TABLE]['sql']) . '
				WHERE topic_id = ' . $data['topic_id'];
			$db->sql_query($sql);
		}
	
		// Update the posts table
		if (isset($sql_data[POSTS_TABLE]['sql']))
		{
			$sql = 'UPDATE ' . POSTS_TABLE . '
				SET ' . $db->sql_build_array('UPDATE', $sql_data[POSTS_TABLE]['sql']) . '
				WHERE post_id = ' . $data['post_id'];
			$db->sql_query($sql);
		}


	// we need to update the last forum information
	// only applicable if the topic is not global and it is approved
	// we also check to make sure we are not dealing with globaling the latest topic (pretty rare but still needs to be checked)
	if ($topic_type != POST_GLOBAL && !$make_global && ($post_approved || !$data['post_approved']))
	{
		// the last post makes us update the forum table. This can happen if...
		// We make a new topic
		// We reply to a topic
		// We edit the last post in a topic and this post is the latest in the forum (maybe)
		// We edit the only post in the topic
		// We edit the first post in the topic and all the other posts are not approved
		if (($post_mode == 'post' || $post_mode == 'reply') && $post_approved)
		{
			$sql_data[FORUMS_TABLE]['stat'][] = 'forum_last_post_id = ' . $data['post_id'];
			$sql_data[FORUMS_TABLE]['stat'][] = "forum_last_post_subject = '" . $db->sql_escape($subject) . "'";
			$sql_data[FORUMS_TABLE]['stat'][] = 'forum_last_post_time = ' . $posttime;
			$sql_data[FORUMS_TABLE]['stat'][] = 'forum_last_poster_id = '. $usid;
			$sql_data[FORUMS_TABLE]['stat'][] = "forum_last_poster_name = '". $username."'";
			$sql_data[FORUMS_TABLE]['stat'][] = "forum_last_poster_colour = '" . $db->sql_escape($usercolor) . "'";
		}
	}

	// Update forum stats
	$where_sql = array(POSTS_TABLE => 'post_id = ' . $data['post_id'], TOPICS_TABLE => 'topic_id = ' . $data['topic_id'], FORUMS_TABLE => 'forum_id = ' . $data['forum_id'], USERS_TABLE => 'user_id = ' . $user->data['user_id']);

	foreach ($sql_data as $table => $update_ary)
	{
		if (isset($update_ary['stat']) && implode('', $update_ary['stat']))
		{ 
            // paso de esto, es un post autogenerado
			// $sql = "UPDATE $table SET " . implode(', ', $update_ary['stat']) . ' WHERE ' . $where_sql[$table];
			// $db->sql_query($sql);
		}
	}

	// Committing the transaction before updating search index
	$db->sql_transaction('commit');


	// Index message contents

	if ($update_message && $data['enable_indexing'])
	{
		// Select the search method and do some additional checks to ensure it can actually be utilised
		$search_type = 'fulltext_mysql';

		if (!file_exists($phpbb_root_path . 'includes/search/' . $search_type . '.' . $phpEx))
		{
			trigger_error('NO_SUCH_SEARCH_MODULE');
		}

		if (!class_exists($search_type))
		{
			include("{$phpbb_root_path}includes/search/$search_type.$phpEx");
		}

		$error = false;
		$search = new $search_type($error);

		if ($error)
		{
			trigger_error($error);
		}

		$search->index($mode, $data['post_id'], $data['message'], $subject, $poster_id, ($topic_type == POST_GLOBAL) ? 0 : $data['forum_id']);
	}


	// Topic Notification, do not change if moderator is changing other users posts...
	if ($user->data['user_id'] == $poster_id)
	{
		if (!$data['notify_set'] && $data['notify'])
		{
			$sql = 'INSERT INTO ' . TOPICS_WATCH_TABLE . ' (user_id, topic_id)
				VALUES (' . $user->data['user_id'] . ', ' . $data['topic_id'] . ')';
			$db->sql_query($sql);
		}
		else if ($data['notify_set'] && !$data['notify'])
		{
			$sql = 'DELETE FROM ' . TOPICS_WATCH_TABLE . '
				WHERE user_id = ' . $user->data['user_id'] . '
					AND topic_id = ' . $data['topic_id'];
			$db->sql_query($sql);
		}
	}

	if ($mode == 'post' || $mode == 'reply' || $mode == 'quote')
	{
		// Mark this topic as posted to
		markread('post', $data['forum_id'], $data['topic_id'], $data['post_time']);
	}

	// Mark this topic as read
	// We do not use post_time here, this is intended (post_time can have a date in the past if editing a message)
	markread('topic', $data['forum_id'], $data['topic_id'], time());

	//
	if ($config['load_db_lastread'] && $user->data['is_registered'])
	{
		$sql = 'SELECT mark_time
			FROM ' . FORUMS_TRACK_TABLE . '
			WHERE user_id = ' . $user->data['user_id'] . '
				AND forum_id = ' . $data['forum_id'];
		$result = $db->sql_query($sql);
		$f_mark_time = (int) $db->sql_fetchfield('mark_time');
		$db->sql_freeresult($result);
	}
	else if ($config['load_anon_lastread'] || $user->data['is_registered'])
	{
		$f_mark_time = false;
	}


	if (($config['load_db_lastread'] && $user->data['is_registered']) || $config['load_anon_lastread'] || $user->data['is_registered'])
	{
		// Update forum info
		$sql = 'SELECT forum_last_post_time
			FROM ' . FORUMS_TABLE . '
			WHERE forum_id = ' . $data['forum_id'];
		$result = $db->sql_query($sql);
		$forum_last_post_time = (int) $db->sql_fetchfield('forum_last_post_time');
		$db->sql_freeresult($result);

		update_forum_tracking_info($data['forum_id'], $forum_last_post_time, $f_mark_time, false);
	}

	// Send Notifications
	if ($mode != 'edit' && $mode != 'delete' && ($auth->acl_get('f_noapprove', $data['forum_id']) || $auth->acl_get('m_approve', $data['forum_id'])))
	{
		user_notification($mode, $subject, $data['topic_title'], $data['forum_name'], $data['forum_id'], $data['topic_id'], $data['post_id']);
	}

	$params = $add_anchor = '';

	if ($auth->acl_get('f_noapprove', $data['forum_id']) || $auth->acl_get('m_approve', $data['forum_id']))
	{
		$params .= '&amp;t=' . $data['topic_id'];

		if ($mode != 'post')
		{
			$params .= '&amp;p=' . $data['post_id'];
			$add_anchor = '#p' . $data['post_id'];
		}
	}
	else if ($mode != 'post' && $post_mode != 'edit_first_post' && $post_mode != 'edit_topic')
	{
		$params .= '&amp;t=' . $data['topic_id'];
	}

	$url = (!$params) ? "{$phpbb_root_path}viewforum.$phpEx" : "{$phpbb_root_path}viewtopic.$phpEx";
	$url = append_sid($url, 'f=' . $data['forum_id'] . $params) . $add_anchor;

	$db->sql_transaction('begin');
        $forum=$data['forum_id'];
        //echo '<h1>['.$forum.']</h1>';
        $sql = "UPDATE " . FORUMS_TABLE . " SET forum_topics=forum_topics+1, forum_posts=forum_posts+1, forum_topics_real=forum_topics_real+1 WHERE forum_id=" . $forum;
        $result = $db->sql_query($sql);
	$db->sql_transaction('commit');

	return $data['topic_id'];
}


function truncate_string($string, $max_length = 60, $allow_reply = true, $append = '')
{
	return $string;
}

function generate_text_for_storage(&$text, &$uid, &$bitfield, &$flags, $allow_bbcode = false, $allow_urls = false, $allow_smilies = falsetio)
{
	global $phpbb_root_path, $phpEx;
	$uid = $bitfield = '';

	if (!$text)
	{return;
	}


	$flags = (($allow_bbcode) ? OPTION_FLAG_BBCODE : 0) + (($allow_smilies) ? OPTION_FLAG_SMILIES : 0) + (($allow_urls) ? OPTION_FLAG_LINKS : 0);
	$bitfield = $message_parser->bbcode_bitfield;
return;
}


function update_post($topic, $subject, $text) {
 global $db;
 $subject=str_replace("'","\'",$subject);
 $subject=str_replace('"','\"',$subject);
 $text=str_replace("'","\'",$text);
 $text=str_replace('"','\"',$text);

 $sql = "SELECT MIN(post_id) FROM " . POSTS_TABLE . " WHERE topic_id=" . $topic;
 $result = $db->sql_query($sql);
 $topic_row = $db->sql_fetchrow($result);
 $postid = $topic_row['MIN(post_id)'];

  if($postid) {
     $sql = "UPDATE " . POSTS_TABLE . " SET post_subject='" . $subject."', post_text='" . $text ."' WHERE post_id=" . $postid;
     $result = $db->sql_query($sql);
     $sql = "UPDATE " . TOPICS_TABLE . " SET topic_title='" . $subject."' WHERE topic_id=" . $topic;
     $result = $db->sql_query($sql);
  }

return;
}

// -------------------------------------------------------------------------------------------------
// MORE PHPBB FUNCTIONS
// -------------------------------------------------------------------------------------------------

?>