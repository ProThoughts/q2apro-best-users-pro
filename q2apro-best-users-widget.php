<?php

/*
	Plugin Name: Best Users PRO
	Plugin URI: http://www.q2apro.com/plugins/best-users-pro
	Plugin Description: User scores get saved per month/year and rewards can be granted to your winning users. Widget and two separate pages.
	Plugin Version: 1.1
	Plugin Date: 2014-09-22
	Plugin Author: q2apro.com
	Plugin Author URI: http://www.q2apro.com
	Plugin Minimum Question2Answer Version: 1.6
	Plugin Update Check URI: http://www.q2apro.com/pluginupdate?id=63
	
	Licence: Copyright © q2apro.com - All rights reserved

*/

class q2apro_bestusers_widget {
	
	function allow_template($template) {
		$allow=false;
		
		switch ($template)
		{
			case 'activity':
			case 'qa':
			case 'questions':
			case 'hot':
			case 'ask':
			case 'categories':
			case 'question':
			case 'tag':
			case 'tags':
			case 'unanswered':
			case 'user':
			case 'users':
			case 'search':
			case 'admin':
			case 'custom':
				$allow=true;
				break;
		}
		
		return $allow;
	}
	
	function allow_region($region)
	{
		$allow=false;
		
		switch ($region)
		{
			case 'side':
				$allow=true;
				break;
			case 'main':
			case 'full':					
				break;
		}
		
		// allow any position
		$allow = true;
		return $allow;
	}

	function output_widget($region, $place, $themeobject, $template, $request, $qa_content) {
		if(qa_opt('q2apro_bestusers_enabled')!=1) {
			return;
		}
			
		// settings
		$maxusers = qa_opt('q2apro_bestusers_maxusers');
		$showRewards = qa_opt('q2apro_bestusers_showrewards_widget');
		$rewardslist = str_replace(';', '<br />', qa_opt('q2apro_bestusers_rewardslist_m'));
		
		$rewardHtml = '<p class="rewardlist" title="'.qa_lang('q2apro_bestusers_lang/reward_title').'">
			<a style="font-weight:bold;color:#303030;" href="'.qa_opt('site_url').'rewards">'.qa_lang('q2apro_bestusers_lang/rewards_monthly').'</a><br />'.
			$rewardslist.'</p>';
		
		// get best users this month from qa_opt-CACHE
		$winners = array();
		$cnt = 0;
		while($cnt < $maxusers) {
			// data is: userid,score
			$winners[$cnt] = qa_opt('q2apro_bestusers_m'.($cnt+1));
			$cnt++;
		}
		
		// initiate output string
		$bestusers = '<ol>';
		$userlisted = false;
		$nrUsers = 0;
		$firstmonthday = date('Y-m-01'); // e.g. 2014-09-01
		
		for($i=0;$i<count($winners);$i++) {
			$userdata = explode(',', $winners[$i]);
			if(isset($userdata[0]) && isset($userdata[1])) {
				$userlisted = true;
				$userid = $userdata[0];
				$userscore = $userdata[1];
				// get userdata
				$user = qa_db_select_with_pending( qa_db_user_account_selectspec($userid, true) );
				// optional, show answer count for each user
				$additionaluserdata = '';
				if(qa_opt('q2apro_bestusers_widget_showanswers')) {
					// get number of answers of user
					$acount = qa_db_read_one_value(
											qa_db_query_sub('SELECT COUNT(*) FROM `^posts` 
												WHERE `userid` = #
												AND `type` = "A"
												AND created >= #', $userid, $firstmonthday), 
											true);
					if(!isset($acount)) {
						$acount = 0;
					}
					$additionaluserdata .= '<p class="bupro_meta">'.
						$acount.' '.($acount==1 ? qa_lang('q2apro_bestusers_lang/answer') : qa_lang('q2apro_bestusers_lang/answers')).
						'</p>';
				}
				if(qa_opt('q2apro_bestusers_widget_showcomments')) {
					// get number of comments of user
					$ccount = qa_db_read_one_value(
											qa_db_query_sub('SELECT COUNT(*) FROM `^posts` 
												WHERE `userid` = #
												AND `type` = "C"
												AND created >= #', $userid, $firstmonthday), 
											true);
					if(!isset($ccount)) {
						$ccount = 0;
					}
					$additionaluserdata .= '<p class="bupro_meta">'.
						$ccount.' '.($ccount==1 ? qa_lang('q2apro_bestusers_lang/comment') : qa_lang('q2apro_bestusers_lang/comments')).
						'</p>';
				}
				if(qa_opt('q2apro_bestusers_widget_showquestions')) {
					// get number of questions of user
					$qcount = qa_db_read_one_value(
											qa_db_query_sub('SELECT COUNT(*) FROM `^posts` 
												WHERE `userid` = #
												AND `type` = "Q"
												AND created >= #', $userid, $firstmonthday), 
											true);
					if(!isset($qcount)) {
						$qcount = 0;
					}
					$additionaluserdata .= '<p class="bupro_meta">'.
						$qcount.' '.($qcount==1 ? qa_lang('q2apro_bestusers_lang/question') : qa_lang('q2apro_bestusers_lang/questions')).
						'</p>';
				}
				// points below user name
				$bestusers .= '<li><span>'.
					qa_get_user_avatar_html($user['flags'], $user['email'], $user['handle'], $user['avatarblobid'], $user['avatarwidth'], $user['avatarheight'], qa_opt('avatar_users_size'), false) . ' '.
					qa_get_one_user_html($user['handle'], false).
					'<p class="uscore">'.$userscore.' '.qa_lang('q2apro_bestusers_lang/points').'</p>'.
					$additionaluserdata.
					'</span></li>';
			}

			// max users to display 
			if(++$nrUsers >= $maxusers) break;
		}
		$bestusers .= '</ol>';
		
		// no user counted (beginning of month!)
		if($nrUsers==0 || !$userlisted) {
			$bestusers .= '<p>'.qa_lang('q2apro_bestusers_lang/none_this_month').'</p>';
		}

		// output into theme
		$themeobject->output('<div class="bestusers-widget">');
		
		// $monthName = date('m/Y'); // 2 digit month and 4 digit year
		// if you want the month displayed in your language uncomment the following block, 
		// you have to define your language code as well, e.g. en_US, fr_FR, de_DE
		/*
		$localcode = "de_DE"; 
		setlocale (LC_TIME, $localcode); 
		$monthName = strftime("%B %G", strtotime( date('F')) ); // %B for full month name, %b for abbreviation
		*/
		
		$themeobject->output('<div style="font-size:15px;margin-bottom:18px;">
			<a href="'.qa_opt('site_url').'bestusers">'.qa_lang('q2apro_bestusers_lang/bestusers_m').'</a> 
		</div>'); 
		$themeobject->output( $bestusers );
		
		// display reward info
		if($showRewards) {
			$themeobject->output($rewardHtml);
		}
		$themeobject->output('</div>');
		
		// CSS
		$themeobject->output('<style type="text/css">
			.bestusers-widget {
				margin-top:30px;
			}
			.bestusers-widget ol {
				margin:0;
				padding-left:20px;
			}
			.bestusers-widget ol li {
				margin-bottom:20px;
				/*color:#F00;
				list-style-type:none;*/
			}
			.bestusers-widget ol li span {
				color:#000;
			}
			.uscore {
				margin:0 0 5px 0;
			}
			.bupro_meta {
				margin:0;
				padding:0;
				line-height:150%;
				color:#555;
				font-size:12px;
			}
		</style>');
	}
}

/*
	Omit PHP closing tag to help avoid accidental output
*/