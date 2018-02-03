<?php

/*
	Plugin Name: Best Users PRO
	Plugin URI: http://www.q2apro.com/plugins/best-users-pro
	Plugin Description: User scores get saved per month/year and rewards can be granted to your winning users. Widget and two separate pages.
	Plugin Version: 1.0
	Plugin Date: 2014-04-24
	Plugin Author: q2apro.com
	Plugin Author URI: http://www.q2apro.com
	Plugin Minimum Question2Answer Version: 1.6
	Plugin Update Check URI: http://www.q2apro.com/pluginupdate?id=63
	
	Licence: Copyright © q2apro.com - All rights reserved

*/

	class q2apro_bestusers_page_year {
		
		var $directory;
		var $urltoroot;
		
		function load_module($directory, $urltoroot)
		{
			$this->directory=$directory;
			$this->urltoroot=$urltoroot;
		}
		
		// for display in admin interface under admin/pages
		function suggest_requests() 
		{	
			return array(
				array(
					'title' => 'Best Users per Year Page', // title of page
					'request' => 'bestusers-year', // request name
					'nav' => 'M', // 'M'=main, 'F'=footer, 'B'=before main, 'O'=opposite main, null=none
				),
			);
		}
		
		// for url query
		function match_request($request)
		{
			if ($request=='bestusers-year') {
				return true;
			}

			return false;
		}

		function process_request($request) {
			if(qa_opt('q2apro_bestusers_enabled')!=1) {
				$qa_content = qa_content_prepare();
				$qa_content['error'] = '<div>'.qa_lang_html('q2apro_bestusers_lang/plugin_disabled').'</div>';
				return $qa_content;
			}
			// return if permission level is not sufficient
			if(qa_user_permit_error('q2apro_bestusers_permission')) {
				$qa_content = qa_content_prepare();
				$qa_content['error'] = qa_lang_html('q2apro_bestusers_lang/access_forbidden');
				return $qa_content;
			}
			if(qa_opt('q2apro_bestusers_enable_yearly')!=1) {
				$qa_content = qa_content_prepare();
				$qa_content['error'] = qa_lang_html('q2apro_bestusers_lang/page_disabled');
				return $qa_content;
			}

			/* start */
			$qa_content=qa_content_prepare();

			// set CSS class in body tag
			qa_set_template('bestusers-year');
			
			// settings
			$maxusersPage = qa_opt('q2apro_bestusers_maxuserspage');
			$showRewards = qa_opt('q2apro_bestusers_showrewards');

			$qa_content['title'] = str_replace('#', $maxusersPage, qa_lang('q2apro_bestusers_lang/page_title_yearly'));

			$rewardslist = str_replace(';', '<br />', qa_opt('q2apro_bestusers_rewardslist_y'));		

			// rewards box
			$showRewardOnTop = '<div class="rewardsBox" style="margin-bottom:30px;">
						<p style="font-size:14px;margin:0 0 5px 2px;line-height:140%;font-weight:bold;">'.qa_lang_html('q2apro_bestusers_lang/rewards_yearly').'</p>
						<p style="font-size:14px;line-height:140%;margin-left:2px;margin-bottom:0;">
						'.$rewardslist.'</p>
						</div>';
			
			$exludeusers = qa_opt('q2apro_bestusers_excludeusers');
			$exludeusers_query = '';
			$exludeusers_query2 = '';
			if(!empty($exludeusers))
			{
				// remove whitespaces
				$exludeusers = str_replace(' ', '', $exludeusers);
				$exludeusers_query = 'WHERE ^userpoints.userid NOT IN ('.$exludeusers.')';
				$exludeusers_query2 = 'AND ul.userid NOT IN ('.$exludeusers.')';
			}

	
	
			// init custom html output
			$qa_content['custom'] = '';
			
			// set start date according to first date set in qa_userscores
			$firstListDate = qa_db_read_one_value(qa_db_query_sub('SELECT `date` FROM `^userscores` ORDER BY `date` ASC LIMIT 1;'),true);
			
			if(isset($firstListDate)) {
				// last entry of dropdown list
				// -1 month, to also show the "first point interval" from all 0 userscores to all first saved userscores
				$firstListDate = date('Y-01-01', strtotime($firstListDate) );
			}
			else {
				// initial start of plugin, no data yet, set this year as default
				$firstListDate = date('Y-01-01');
			}
			
			// first entry of dropdown list
			$lastListDate = date('Y-01-01'); // current date
			
			// if you want last month as default use
			// $lastListDate = date('Y-m-01', strtotime('last month') );
			
			// this month as default
			$chosenYear = date('Y-01-01');
			// if you want last month as default use
			// $chosenYear = date('Y-m', strtotime('last month') ); 
			
			// we received post data, user has chosen a month
			if(qa_post_text('request')) {
				$chosenYear = qa_post_text('request');
				// sanitize string, keep only 0-9 and - (maybe too suspicious?)
				$chosenYear = preg_replace("/[^0-9\-]/i", '', $chosenYear);
				// add '-01-01'
				$chosenYear .= '-01-01';
			}

			// get interval start from chosen month
			$intervalStart = date('Y-01-01', strtotime($chosenYear) ); // 2012 becomes 2012-01-01
			$intervalEnd = date('Y-01-01', strtotime($chosenYear.' +1 year') ); // 2012 becomes 2013-01-01
		
			// output reward on top
			if($showRewards) {
				$qa_content['custom'] .= $showRewardOnTop;					
			}
			
			// datepicker
			$qa_content['custom'] .= '<link rel="stylesheet" type="text/css" href="'.$this->urltoroot.'zebra_datepicker/default.css">';
			$qa_content['custom'] .= '<script type="text/javascript" src="'.$this->urltoroot.'zebra_datepicker/zebra_datepicker.js"></script>';
			$qa_content['custom'] .= '<script type="text/javascript">
				$(document).ready(function() {
					$("#datepicker").Zebra_DatePicker({
						direction: ["'.substr($firstListDate,0,4).'", new Date().toISOString().substring(0,4)],
						format: "Y", 
						view: "years", 
						lang_clear_date: "", 
						offset: [15,250], 
						onSelect: function(view, elements) {
							$("form#datepick").submit();
						}
					});
				});
			</script>';

			// date picker input field
			$qa_content['custom'] .= '<form method="post" action="'.qa_self_html().'" id="datepick" style="display:block;margin-bottom:20px;">
											<span style="font-size:14px;">'.qa_lang('q2apro_bestusers_lang/choose_year').': &nbsp;</span>
											<input style="font-size:14px;width:55px;padding-left:3px;cursor:pointer;" value="'.substr($chosenYear,0,4).'" id="datepicker" name="request" type="text">
										  </form>';

			// we need to do another query to get the userscores of the recent year
			if( date('Y-01-01', strtotime($chosenYear)) == date('Y-01-01') ) {
				// calculate userscores from recent year
				$queryRecentScores = qa_db_query_sub('
										SELECT ^userpoints.userid, ^userpoints.points - COALESCE(uf.points, 0) AS mpoints 
										FROM `^userpoints` 
										LEFT JOIN (SELECT userid, points FROM `^userscores` WHERE `date` = "'.$intervalStart.'") AS uf 
										ON uf.userid = ^userpoints.userid '
										.$exludeusers_query.' 
										ORDER BY mpoints DESC;');
			}
			else {
				// calculate userscores for given interval
				$queryRecentScores = qa_db_query_sub('
										SELECT ul.userid, ul.points - COALESCE(uf.points, 0) AS mpoints 
										FROM `^userscores` ul 
										LEFT JOIN (SELECT userid, points FROM `^userscores` WHERE `date` = "'.$intervalStart.'") AS uf
										ON uf.userid = ul.userid
										WHERE ul.date = "'.$intervalEnd.'" 
										'.$exludeusers_query2.' 
										ORDER BY mpoints DESC;');
				// thanks raina77ow for helping me with mysql
				// http://stackoverflow.com/questions/11178599/mysql-get-difference-between-two-values-in-one-table-multiple-userids
			}

			// save all userscores in array
			$scores = array();
			while ( ($row = qa_db_read_one_assoc($queryRecentScores,true)) !== null ) {
				$scores[$row['userid']] = $row['mpoints'];
			}

			// save userids in array that we need for qa_userids_to_handles()
			$userids = array();
			$cnt = 0;
			foreach ($scores as $userId => $val) {
				$userids[++$cnt] = $userId;
			}
			
			// get handles (i.e. usernames) in array usernames
			$usernames = qa_userids_to_handles($userids);

			// initiate output string
			$bestusers = '<table class="bestusersTable">';
			$nrUsers = 0;
			foreach ($scores as $userId => $val) {
				// no users with 0 points, and no blocked users!
				if($val>0) {
					$currentUser = $usernames[$userId];
					$user = qa_db_select_with_pending( qa_db_user_account_selectspec($currentUser, false) );
					
					$uanswerstxt = '';
					if(qa_opt('q2apro_bestusers_bupage_show_acount')) {
						$uanswers = q2apro_bu_get_year_answers($userId, $intervalStart);
						$uanswerstxt = '<span class="q2apro-bu-acount">'. $uanswers.' '.($uanswers==1 ? qa_lang('q2apro_bestusers_lang/answer') : qa_lang('q2apro_bestusers_lang/answers')).'</span>';
					}
					
					// check if user is blocked, do not list them
					if (! (QA_USER_FLAGS_USER_BLOCKED & $user['flags'])) {
						// points below user name, check CSS descriptions for .bestusers
						$bestusers .= '<tr class="q2apro-bu-row">
						<td class="q2apro-bu-count">'.($nrUsers+1).'.</td>
						<td class="q2apro-bu-label">'.qa_get_user_avatar_html($user['flags'], $user['email'], $user['handle'], $user['avatarblobid'], $user['avatarwidth'], $user['avatarheight'], qa_opt('avatar_users_size'), false) . ' ' . qa_get_one_user_html($currentUser, false).'</td>'.
						'<td class="q2apro-bu-score">'.
						'<span class="q2apro-bu-points">'.$val.' '.qa_lang('q2apro_bestusers_lang/points').'</span>'.
						$uanswerstxt.
						'</td>'.
						( qa_opt('q2apro_bestusers_bupage_show_location') ? '<td class="q2apro-bu-location">'.q2apro_bu_get_userlocation($userId).'</td>' : '') . 
						( qa_opt('q2apro_bestusers_bupage_show_about') ? '<td class="q2apro-bu-about">'.q2apro_bu_get_userabout($userId).'</td>' : '') . 
						'</tr>'
						; 
						
						// max users to display 
						if(++$nrUsers >= $maxusersPage) break;
					}
				}
			}
			$bestusers .= '</table>';

			
			/* output into theme */
			$qa_content['custom'] .= '<div class="bestusersPage">';
			
			// convert date to display m/Y, 2 digit month and 4 digit year
			$yearName = date('Y', strtotime($chosenYear) );
			
			$qa_content['custom'] .='<div style="font-size:16px;margin:18px 0;">'.qa_lang_html('q2apro_bestusers_lang/bestusers').' '.$yearName.'</div>'; 
			$qa_content['custom'] .= $bestusers;
			$qa_content['custom'] .='</div>';
			
			// link to all users list
			$qa_content['custom'] .='<a href="'.qa_opt('site_url').'users" class="qa-form-tall-button" style="margin:40px 0 80px 0;">'.qa_lang_html('q2apro_bestusers_lang/show_all_users').'</a>';
			// CSS button fix for Snow Theme
			if(qa_opt('site_theme')=='Snow') {
				$qa_content['custom'] .= '<style type="text/css">
					.qa-part-custom a.qa-form-tall-button {
						color:#FFF !important;
						display:inline-block;
					}
				</style>';
			}
			
			// WIDGET CALL: we want the best-user-widget also to be displayed on this page
			if(qa_opt('q2apro_bestusers_widget_onbestuserslist')) {
				$widget['title'] = 'q2apro Best Users Widget';
				$module=qa_load_module('widget', $widget['title']);
				$region = 'side';
				$place = 'high';
				$qa_content['widgets'][$region][$place][] = $module;
			}
			
			return $qa_content;
		}
	};
	

/*
	Omit PHP closing tag to help avoid accidental output
*/