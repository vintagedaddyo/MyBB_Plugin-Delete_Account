<?php
/*
 * MyBB: Delete Account
 *
 * File: deleteaccount.php
 *
 * Authors: MyBB Security Group (Paul H.) & Vintagedaddyo
 *
 * MyBB Version: 1.8
 *
 * Plugin Version: 1.4
 *
 */

// Disallow direct access to this file for security reasons

if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");	
}


//add our hooks

$plugins->add_hook("usercp_start", "deleteaccount");

$plugins->add_hook("pre_output_page", "deleteaccount_menu");

//admin

$plugins->add_hook('admin_load', 'deleteaccount_admin');

$plugins->add_hook('admin_user_menu', 'deleteaccount_admin_user_menu');

$plugins->add_hook('admin_user_action_handler', 'deleteaccount_admin_user_action_handler');


//basic plugin info for ACP

function deleteaccount_info()
{
	global $db, $lang;

	$lang->load("deleteaccount");

$lang->da_delete_account_desc = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="float:right;">' .
        '<input type="hidden" name="cmd" value="_s-xclick">' . 
        '<input type="hidden" name="hosted_button_id" value="AZE6ZNZPBPVUL">' .
        '<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">' .
        '<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">' .
        '</form>' . $db->escape_string($lang->da_delete_account_desc);
	
	return array(
		"name"			=> $db->escape_string($lang->da_delete_account_name),
		"description"	=> $lang->da_delete_account_desc,
		"website"		=> "https://github.com/vintagedaddyo/MyBB_Plugin-Delete_Account",
		"author"		=> "MyBB Security Group & Vintagedaddyo",
		"authorsite"	=> "https://github.com/vintagedaddyo/MyBB_Plugin-Delete_Account",
		"version"		=> "1.4",
		"guid" 			=> "",
		"compatibility" => "18*"
	);
}

function deleteaccount_install()
{
	global $db,$lang;
	
	$lang->load("deleteaccount");
	
	//settings group

	$group = array(
		'gid'			=> '0',
		'name'			=> 'deleteaccount',
		'title'			=> $db->escape_string($lang->da_settings_name),
		'description'	=> $db->escape_string($lang->da_settings_desc),
		'disporder'		=> '0',
		'isdefault'		=> '0',
	);

	$db->insert_query('settinggroups', $group);

	$gid = $db->insert_id();
	
	//settings

	$setting = array(
		'name'			=> 'deleteaccount_normgroups',
		'title'			=> $db->escape_string($lang->da_settings_normgroup_name),
		'description'	=> $db->escape_string($lang->da_settings_normgroup_desc),
		'optionscode'	=> 'text',
		'value'			=> '2,3,4,6',
		'disporder'		=> 1,
		'gid'			=> intval($gid),
	);

	$db->insert_query('settings', $setting);

	$setting = array(
		'name'			=> 'deleteaccount_tpdeletion',
		'title'			=> $db->escape_string($lang->da_settings_tpdeletion_name),
		'description'	=> $db->escape_string($lang->da_settings_tpdeletion_desc),
		'optionscode'	=> 'yesno',
		'value'			=> '0',
		'disporder'		=> 2,
		'gid'			=> intval($gid),
	);

	$db->insert_query('settings', $setting);
					
	rebuild_settings();

	//make our DB table - a clone of the defualt MyBB users table

	$db->write_query("CREATE TABLE ".TABLE_PREFIX."deleted_users (
  uid int unsigned NOT NULL auto_increment,
  username varchar(120) NOT NULL default '',
  password varchar(120) NOT NULL default '',
  salt varchar(10) NOT NULL default '',
  loginkey varchar(50) NOT NULL default '',
  email varchar(220) NOT NULL default '',
  postnum int(10) unsigned NOT NULL default '0',
  threadnum int(10) unsigned NOT NULL default '0',
  avatar varchar(200) NOT NULL default '',
  avatardimensions varchar(10) NOT NULL default '',
  avatartype varchar(10) NOT NULL default '0',
  usergroup smallint unsigned NOT NULL default '0',
  additionalgroups varchar(200) NOT NULL default '',
  displaygroup smallint unsigned NOT NULL default '0',
  usertitle varchar(250) NOT NULL default '',
  regdate int unsigned NOT NULL default '0',
  lastactive int unsigned NOT NULL default '0',
  lastvisit int unsigned NOT NULL default '0',
  lastpost int unsigned NOT NULL default '0',
  website varchar(200) NOT NULL default '',
  icq varchar(10) NOT NULL default '',
  skype varchar(75) NOT NULL default '',
  google varchar(75) NOT NULL default '',
  birthday varchar(15) NOT NULL default '',
  birthdayprivacy varchar(4) NOT NULL default 'all',
  signature text NOT NULL,
  allownotices tinyint(1) NOT NULL default '0',
  hideemail tinyint(1) NOT NULL default '0',
  subscriptionmethod tinyint(1) NOT NULL default '0',
  invisible tinyint(1) NOT NULL default '0',
  receivepms tinyint(1) NOT NULL default '0',
  receivefrombuddy tinyint(1) NOT NULL default '0',
  pmnotice tinyint(1) NOT NULL default '0',
  pmnotify tinyint(1) NOT NULL default '0',
  buddyrequestspm tinyint(1) NOT NULL default '1',
  buddyrequestsauto tinyint(1) NOT NULL default '0',
  threadmode varchar(8) NOT NULL default '',
  showimages tinyint(1) NOT NULL default '0',
  showvideos tinyint(1) NOT NULL default '0',
  showsigs tinyint(1) NOT NULL default '0',
  showavatars tinyint(1) NOT NULL default '0',
  showquickreply tinyint(1) NOT NULL default '0',
  showredirect tinyint(1) NOT NULL default '0',
  ppp smallint(6) unsigned NOT NULL default '0',
  tpp smallint(6) unsigned NOT NULL default '0',
  daysprune smallint(6) unsigned NOT NULL default '0',
  dateformat varchar(4) NOT NULL default '',
  timeformat varchar(4) NOT NULL default '',
  timezone varchar(5) NOT NULL default '',
  dst tinyint(1) NOT NULL default '0',
  dstcorrection tinyint(1) NOT NULL default '0',
  buddylist text NOT NULL,
  ignorelist text NOT NULL,
  style smallint unsigned NOT NULL default '0',
  away tinyint(1) NOT NULL default '0',
  awaydate int(10) unsigned NOT NULL default '0',
  returndate varchar(15) NOT NULL default '',
  awayreason varchar(200) NOT NULL default '',
  pmfolders text NOT NULL,
  notepad text NOT NULL,
  referrer int unsigned NOT NULL default '0',
  referrals int unsigned NOT NULL default '0',
  reputation int NOT NULL default '0',
  regip varbinary(16) NOT NULL default '',
  lastip varbinary(16) NOT NULL default '',
  language varchar(50) NOT NULL default '',
  timeonline int unsigned NOT NULL default '0',
  showcodebuttons tinyint(1) NOT NULL default '1',
  totalpms int(10) unsigned NOT NULL default '0',
  unreadpms int(10) unsigned NOT NULL default '0',
  warningpoints int(3) unsigned NOT NULL default '0',
  moderateposts tinyint(1) NOT NULL default '0',
  moderationtime int unsigned NOT NULL default '0',
  suspendposting tinyint(1) NOT NULL default '0',
  suspensiontime int unsigned NOT NULL default '0',
  suspendsignature tinyint(1) NOT NULL default '0',
  suspendsigtime int unsigned NOT NULL default '0',
  coppauser tinyint(1) NOT NULL default '0',
  classicpostbit tinyint(1) NOT NULL default '0',
  loginattempts smallint(2) unsigned NOT NULL default '0',
  loginlockoutexpiry int unsigned NOT NULL DEFAULT '0',
  usernotes text NOT NULL,
  sourceeditor tinyint(1) NOT NULL default '0',
  deletedtime bigint(30) NOT NULL default '0',
  deletereason text NOT NULL,
  UNIQUE KEY username (username),
  KEY usergroup (usergroup),
  KEY regip (regip),
  KEY lastip (lastip),
  PRIMARY KEY (uid)
) ENGINE=MyISAM;");	

}

function deleteaccount_is_installed()
{
	global $db;

	//it's installed if the DB table exists

	return $db->table_exists("deleted_users");
}

function deleteaccount_activate()
{
	require_once MYBB_ROOT."/inc/adminfunctions_templates.php";

	find_replace_templatesets("usercp_nav_profile", "#".preg_quote('{$changesigop}')."#i", '{$changesigop}{delete_account_link}');
}

function deleteaccount_deactivate()
{
	require_once MYBB_ROOT."/inc/adminfunctions_templates.php";

	find_replace_templatesets("usercp_nav_profile", "#".preg_quote('{delete_account_link}')."#i", '');
}

function deleteaccount_uninstall()
{
	global $db;
	
	$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN ('deleteaccount_normgroups')");

	$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN ('deleteaccount_tpdeletion')");
	
	$db->query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name='deleteaccount'");

	rebuild_settings();

	$db->write_query("DROP TABLE ".TABLE_PREFIX."deleted_users");			
}


function deleteaccount()
{
	global $lang,$mybb,$db,$header,$footer,$headerinclude,$theme,$lang,$usercpnav,$usercpmenu;
	
	$lang->load("deleteaccount");
	
	if($mybb->input['action'] == "deleteaccount")
	{
		//if you can't delete your account you can't see this!

		if(!can_delete_account()) {error_no_permission();}
			
		add_breadcrumb($lang->da_delete_account);		

		//form

		$page = "{$lang->da_are_you_sure} <br /> <br />
                         {$lang->da_are_you_sure_note} <br /> <br />
			<form action=\"usercp.php\" method=\"post\">
			<textarea id=\"message\" name=\"message\" rows=\"5\" cols=\"50\">{$lang->da_why}</textarea><br /><br />
			<input type=\"hidden\" name=\"my_post_key\" value=\"{$mybb->post_code}\" />
			<input type=\"hidden\" name=\"action\" value=\"do_deleteaccount\" />		
			<input type=\"submit\" value=\"{$lang->da_yes}\" />	
			</form>";

		//basic template

		$template = "<html>
<head>
<title>{$lang->user_cp}</title>
{$headerinclude}
</head>
<body>
{$header}
<table width=\"100%\" border=\"0\" align=\"center\">
<tr>
{$usercpnav}
<td valign=\"top\">
<table border=\"0\" cellspacing=\"{$theme['borderwidth']}\" cellpadding=\"{$theme['tablespace']}\" class=\"tborder\">
<tr>
<td class=\"thead\" colspan=\"{$colspan}\"><strong>{$lang->da_delete_account}</strong></td>
</tr>
<tr>
<td class=\"trow1\" align=\"center\">{$page}</td>
</tr>
</table>
</td>
</tr>
</table>
{$footer}
</body>
</html>";

		//spit out the page!

		output_page($template);
	}

	if($mybb->input['action'] == 'do_deleteaccount')
	{
		//no CSRF here!

		verify_post_check($mybb->input['my_post_key']);
		
		//get the user	

		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."users WHERE uid='{$mybb->user['uid']}'");

		$array = $db->fetch_array($query);
		
		//we need to do this in case there are any custom fields, e.g. newpoints, that aren't in the deleted_users table

		$key = 'uid'; $iarray[$key] = $array[$key];
		$key = 'username'; $iarray[$key] = $array[$key];
		$key = 'password'; $iarray[$key] = $array[$key];
		$key = 'salt'; $iarray[$key] = $array[$key];
		$key = 'loginkey'; $iarray[$key] = $array[$key];
		$key = 'email'; $iarray[$key] = $array[$key];
		$key = 'postnum'; $iarray[$key] = $array[$key];
		$key = 'threadnum'; $iarray[$key] = $array[$key];
		$key = 'avatar'; $iarray[$key] = $array[$key];
		$key = 'avatardimensions'; $iarray[$key] = $array[$key];
		$key = 'avatartype'; $iarray[$key] = $array[$key];
		$key = 'usergroup'; $iarray[$key] = $array[$key];
		$key = 'additionalgroups'; $iarray[$key] = $array[$key];
		$key = 'displaygroup'; $iarray[$key] = $array[$key];
		$key = 'usertitle'; $iarray[$key] = $array[$key];
		$key = 'regdate'; $iarray[$key] = $array[$key];
		$key = 'lastactive'; $iarray[$key] = $array[$key];
		$key = 'lastvisit'; $iarray[$key] = $array[$key];
		$key = 'lastpost'; $iarray[$key] = $array[$key];
		$key = 'website'; $iarray[$key] = $array[$key];
		$key = 'icq'; $iarray[$key] = $array[$key];
		$key = 'skype'; $iarray[$key] = $array[$key];
		$key = 'google'; $iarray[$key] = $array[$key];
		$key = 'birthday'; $iarray[$key] = $array[$key];
		$key = 'birthdayprivacy'; $iarray[$key] = $array[$key];
		$key = 'signature'; $iarray[$key] = $array[$key];
		$key = 'allownotices'; $iarray[$key] = $array[$key];
		$key = 'hideemail'; $iarray[$key] = $array[$key];
		$key = 'subscriptionmethod'; $iarray[$key] = $array[$key];
		$key = 'invisible'; $iarray[$key] = $array[$key];
		$key = 'receivepms'; $iarray[$key] = $array[$key];
		$key = 'receivefrombuddy'; $iarray[$key] = $array[$key];
		$key = 'pmnotice'; $iarray[$key] = $array[$key];
		$key = 'pmnotify'; $iarray[$key] = $array[$key];
		$key = 'buddyrequestspm'; $iarray[$key] = $array[$key];
		$key = 'buddyrequestsauto'; $iarray[$key] = $array[$key];
		$key = 'threadmode'; $iarray[$key] = $array[$key];
		$key = 'showimages'; $iarray[$key] = $array[$key];
		$key = 'showvideos'; $iarray[$key] = $array[$key];
		$key = 'showsigs'; $iarray[$key] = $array[$key];
		$key = 'showavatars'; $iarray[$key] = $array[$key];
		$key = 'showquickreply'; $iarray[$key] = $array[$key];
		$key = 'showredirect'; $iarray[$key] = $array[$key];
		$key = 'ppp'; $iarray[$key] = $array[$key];
		$key = 'tpp'; $iarray[$key] = $array[$key];
		$key = 'daysprune'; $iarray[$key] = $array[$key];
		$key = 'dateformat'; $iarray[$key] = $array[$key];
		$key = 'timeformat'; $iarray[$key] = $array[$key];
		$key = 'timezone'; $iarray[$key] = $array[$key];
		$key = 'dst'; $iarray[$key] = $array[$key];
		$key = 'dstcorrection'; $iarray[$key] = $array[$key];
		$key = 'buddylist'; $iarray[$key] = $array[$key];
		$key = 'ignorelist'; $iarray[$key] = $array[$key];
		$key = 'style'; $iarray[$key] = $array[$key];
		$key = 'away'; $iarray[$key] = $array[$key];
		$key = 'awaydate'; $iarray[$key] = $array[$key];
		$key = 'returndate'; $iarray[$key] = $array[$key];
		$key = 'awayreason'; $iarray[$key] = $array[$key];
		$key = 'pmfolders'; $iarray[$key] = $array[$key];
		$key = 'notepad'; $iarray[$key] = $array[$key];
		$key = 'referrer'; $iarray[$key] = $array[$key];
		$key = 'referrals'; $iarray[$key] = $array[$key];
		$key = 'reputation'; $iarray[$key] = $array[$key];
		$key = 'regip'; $iarray[$key] = $array[$key];
		$key = 'lastip'; $iarray[$key] = $array[$key];
		$key = 'language'; $iarray[$key] = $array[$key];
		$key = 'timeonline'; $iarray[$key] = $array[$key];
		$key = 'showcodebuttons'; $iarray[$key] = $array[$key];
		$key = 'totalpms'; $iarray[$key] = $array[$key];
		$key = 'unreadpms'; $iarray[$key] = $array[$key];
		$key = 'warningpoints'; $iarray[$key] = $array[$key];
		$key = 'moderateposts'; $iarray[$key] = $array[$key];
		$key = 'moderationtime'; $iarray[$key] = $array[$key];
		$key = 'suspendposting'; $iarray[$key] = $array[$key];
		$key = 'suspensiontime'; $iarray[$key] = $array[$key];
		$key = 'suspendsignature'; $iarray[$key] = $array[$key];
		$key = 'suspendsigtime'; $iarray[$key] = $array[$key];
		$key = 'coppauser'; $iarray[$key] = $array[$key];
		$key = 'classicpostbit'; $iarray[$key] = $array[$key];
		$key = 'loginattempts'; $iarray[$key] = $array[$key];
		$key = 'sourceeditor'; $iarray[$key] = $array[$key];
 		$key = 'loginlockoutexpiry'; $iarray[$key] = $array[$key];
		$key = 'usernotes'; $iarray[$key] = $array[$key];
		$key = 'deletedtime'; $iarray[$key] = TIME_NOW;
		$key = 'deletereason'; $iarray[$key] = $db->escape_string($mybb->input['message']);
	
		//insert into deleted users table

		$db->insert_query("deleted_users",$iarray);
	
		//delete from users table

		$db->delete_query("users","uid='{$mybb->user['uid']}'");
				
		redirect("index.php", $lang->da_redirect);
	}		

}

function deleteaccount_menu($page)
{
	global $lang;
		
	$lang->load('deleteaccount');	
		
	if(THIS_SCRIPT == 'usercp.php')
	{
		if(can_delete_account())
		{	
			//show link if you can delete your account

			$page = str_replace('{delete_account_link}', '<div><a href="usercp.php?action=deleteaccount" class="usercp_nav_item usercp_nav_trash_pmfolder">'.$lang->da_delete_account.'</a></div>',$page);
		} else {
			//if you can't, then don't show the link

			$page = str_replace('{delete_account_link}', '',$page);	
		}		
	}
	
	return $page;	
}

function can_delete_account()
{
	global $mybb;
	
	$groups = explode(",",$mybb->settings['deleteaccount_normgroups']);
	
	if(in_array($mybb->user['usergroup'],$groups))
	{	
		return true;		
	} else {	
		return false;
	}		
}

////////////////////////////////////////////
//admin stuff
////////////////////////////////////////////

function deleteaccount_admin_user_menu(&$sub_menu)
{
	global $lang;

	$lang->load('deleteaccount');

	$sub_menu[] = array('id' => 'deleteaccount', 'title' => $lang->da_deleted_accounts, 'link' => 'index.php?module=user-deleteaccount');
}

function deleteaccount_admin_user_action_handler(&$actions)
{
	$actions['deleteaccount'] = array('active' => 'deleteaccount', 'file' => 'deleteaccount');
}

function deleteaccount_admin()
{
	global $db, $lang, $mybb, $page, $run_module, $action_file, $plugins, $cache;

	$lang->load("deleteaccount");	

	if($run_module == 'user' && $action_file == 'deleteaccount')
	{
		$page->add_breadcrumb_item($lang->da_deleted_accounts, 'index.php?module=user-deleteaccount');

		if($mybb->input['action'] == 'restore')
		{
			verify_post_check($mybb->input['my_post_key']);
			
			$uid = intval($mybb->input['uid']);

			$query = $db->simple_select("deleted_users","*","uid='{$uid}'");
			
			if($db->num_rows($query) == 0)
			{	
				flash_message($lang->da_invalid_user, 'error');

				admin_redirect('index.php?module=user-deleteaccount');

			} else {
				$user = $db->fetch_array($query);
			}
			
			unset($user['deletereason']);

			unset($user['deletedtime']);
			
			$db->insert_query("users",$user);

			$db->delete_query("deleted_users","uid='{$uid}'");
			
			log_admin_action($lang->da_restored, $user['username']);

			flash_message($lang->da_user_restored, 'success');

			admin_redirect('index.php?module=user-deleteaccount');
		}
	
		if($mybb->input['action'] == 'delete')
		{
			verify_post_check($mybb->input['my_post_key']);	
	
			$uid = intval($mybb->input['uid']);

			$query = $db->simple_select("deleted_users","*","uid='{$uid}'");
			
			if($db->num_rows($query) == 0)
			{	
				flash_message($lang->da_invalid_user, 'error');

				admin_redirect('index.php?module=user-deleteaccount');

			} else {
				$user = $db->fetch_array($query);
			}		
					
			// Delete the user

			if($mybb->settings['deleteaccount_tpdeletion'] == '0')
		        {
			$db->update_query("threads", array('uid' => 0), "uid='{$user['uid']}'");			        	     
			
                        $db->update_query("posts", array('uid' => 0), "uid='{$user['uid']}'");
                        }
  
                        if($mybb->settings['deleteaccount_tpdeletion'] == '1')
		        {        	     
                        $db->delete_query("threads", "uid='{$user['uid']}'");

                        $db->delete_query("posts", "uid='{$user['uid']}'");
                        }
    
      
			$db->delete_query("userfields", "ufid='{$user['uid']}'");

			$db->delete_query("privatemessages", "uid='{$user['uid']}'");

			$db->delete_query("events", "uid='{$user['uid']}'");

			$db->delete_query("forumsubscriptions", "uid='{$user['uid']}'");

			$db->delete_query("threadsubscriptions", "uid='{$user['uid']}'");

			$db->delete_query("sessions", "uid='{$user['uid']}'");

			$db->delete_query("banned", "uid='{$user['uid']}'");

			$db->delete_query("threadratings", "uid='{$user['uid']}'");

			$db->delete_query("users", "uid='{$user['uid']}'");

			$db->delete_query("joinrequests", "uid='{$user['uid']}'");

			$db->delete_query("warnings", "uid='{$user['uid']}'");

			$db->delete_query("reputation", "uid='{$user['uid']}' OR adduid='{$user['uid']}'");
			
			$db->delete_query("awaitingactivation", "uid='{$uid}'");
	
			// Update forum stats

			update_stats(array('numusers' => '-1'));
	
			// Did this user have an uploaded avatar?

			if($user['avatartype'] == "upload")
			{
				// Removes the ./ at the beginning the timestamp on the end...

				@unlink("../".substr($user['avatar'], 2, -20));
			}
	
			// Was this user a moderator?

			if(is_moderator($user['uid']))
			{
				$db->delete_query("moderators", "id='{$user['uid']}' AND isgroup = '0'");

				$cache->update_moderators();
			}
	
			$plugins->run_hooks("admin_user_users_delete_commit");
			
			$db->delete_query("deleted_users","uid='{$uid}'");
				
			log_admin_action($lang->da_deleted, $user['username']);		
			
			flash_message($lang->da_permanently_deleted, 'success');

			admin_redirect('index.php?module=user-deleteaccount');		
		}			

		if(!$mybb->input['action'])
		{
			$page->output_header($lang->da_deleted_accounts);
	
			$sub_tabs['deleted_accounts'] = array(
				'title'	      => $lang->da_deleted_accounts,
				'link'            => 'index.php?module=user-deleteaccount',
				'description' => $lang->da_deleted_accounts_desc
			);
	
			$page->output_nav_tabs($sub_tabs, 'deleted_accounts');
	
			$table = new Table;

			$table->construct_header('UID', array('class' => "align_center"));

			$table->construct_header($lang->username, array('class' => "align_center"));

			$table->construct_header($lang->da_delete_reason, array('class' => "align_center"));

			$table->construct_header($lang->da_delete_time, array('class' => "align_center"));

			$table->construct_header($lang->controls, array('class' => "align_center", 'colspan' => 2));
	
			$query = $db->simple_select('deleted_users', '*');
			
			while($user = $db->fetch_array($query))
			{
				$table->construct_cell($user['uid'], array('width' => 1));

				$table->construct_cell($user['username'], array('class' => "align_center"));

				$table->construct_cell(htmlspecialchars_uni($user['deletereason']), array('class' => "align_center"));

				$table->construct_cell(my_date("F j, Y, g:i a",$user['deletedtime']), array('class' => "align_center"));

				$table->construct_cell("<a href=\"index.php?module=user-deleteaccount&amp;action=restore&amp;uid={$user['uid']}&amp;my_post_key={$mybb->post_code}\">{$lang->da_restore}</a>", array("class" => "align_center"));

				$table->construct_cell("<a href=\"index.php?module=user-deleteaccount&amp;action=delete&amp;uid={$user['uid']}&amp;my_post_key={$mybb->post_code}\" onclick=\"return AdminCP.deleteConfirmation(this, '{$lang->da_confirm_account_deletion}')\">{$lang->da_delete}</a>", array("class" => "align_center"));

				$table->construct_row();
			}
	
			if($table->num_rows() == 0)
			{
			   	$table->construct_cell($lang->da_no_deleted_accounts, array('colspan' => 5));

			   	$table->construct_row();
			}
	
			if($mybb->settings['deleteaccount_tpdeletion'] == '1')
		        {  	
                        $table->output($lang->da_settings_tpdeletion_enabled);	
		        }	
	
                        if($mybb->settings['deleteaccount_tpdeletion'] == '0')
		        {  	
                        $table->output($lang->da_settings_tpdeletion_disabled);	
                        }					
			
                        //$table->output($lang->da_deleted_accounts);
	
			$page->output_footer();
		}
	exit;
	
	}
}

?>
