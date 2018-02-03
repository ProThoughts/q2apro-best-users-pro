<?php

/*
	Plugin Name: Best Users PRO
	Plugin URI: http://www.q2apro.com/plugins/best-users-pro
	Plugin Description: User scores get saved per month/year and rewards can be granted to your winning users. Widget and two separate pages.
	Plugin Version: 1.2
	Plugin Date: 2014-10-04
	Plugin Author: q2apro.com
	Plugin Author URI: http://www.q2apro.com
	Plugin Minimum Question2Answer Version: 1.6
	Plugin Update Check URI: http://www.q2apro.com/pluginupdate?id=63
	
	Licence: Copyright © q2apro.com - All rights reserved

*/

	class q2apro_bestusers_page_month {
		
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
					'title' => 'Best Users per Month Page', // title of page
					'request' => 'bestusers', // request name
					'nav' => 'M', // 'M'=main, 'F'=footer, 'B'=before main, 'O'=opposite main, null=none
				),
			);
		}
		
		// for url query
		function match_request($request)
		{
			if ($request=='bestusers') {
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

			/* start */
			$qa_content=qa_content_prepare();

			// set CSS class in body tag
			qa_set_template('bestusers-month');
			
			// settings
			$maxusersPage = qa_opt('q2apro_bestusers_maxuserspage');
			$showRewards = qa_opt('q2apro_bestusers_showrewards');

			$qa_content['title'] = str_replace('#', $maxusersPage, qa_lang('q2apro_bestusers_lang/page_title_monthly'));
			
			$rewardslist = str_replace(';', '<br />', qa_opt('q2apro_bestusers_rewardslist_m'));		

			// rewards box
			$showRewardOnTop = '<div class="rewardsBox">
						<p class="p1">'.qa_lang_html('q2apro_bestusers_lang/rewards_monthly').'</p>
						<p class="p2">
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
				$firstListDate = date('Y-m-01', strtotime($firstListDate.'-1 month') );
			}
			else {
				// initial start of plugin, no data yet, set this month as default
				$firstListDate = date('Y-m-01');
			}
			
			// first entry of dropdown list
			$lastListDate = date('Y-m-01'); // current year
			
			// if you want last month as default use
			// $lastListDate = date('Y-m-01', strtotime('last month') );
			
			// this month as default
			$chosenMonth = date('Y-m-01');
			// if you want last month as default use
			// $chosenMonth = date('Y-m', strtotime('last month') ); 
			
			// we received post data, user has chosen a month
			if(qa_post_text('request')) {
				$chosenMonth = qa_post_text('request');
				// sanitize string, keep only 0-9 and - (maybe too suspicious?)
				$chosenMonth = preg_replace("/[^0-9\-]/i", '', $chosenMonth);
			}

			// get interval start from chosen month
			$intervalStart = date('Y-m-01', strtotime($chosenMonth) ); // 05/2012 becomes 2012-05-01
			$intervalEnd = date('Y-m-01', strtotime($chosenMonth.'+1 month') ); // 05/2012 becomes 2012-06-01
			
			// output reward on top
			if($showRewards) {
				$qa_content['custom'] .= $showRewardOnTop;
			}
			
			$monthsNames = qa_opt('q2apro_bestusers_months_abbr');
			$monthsNamesArr = explode(',', $monthsNames);
			$monthsString = '';
			for($m=0; $m<count($monthsNamesArr); $m++) {
				$monthsString .= '"'.$monthsNamesArr[$m].'",';
			}
			
			// datepicker
			$qa_content['custom'] .= '<link rel="stylesheet" type="text/css" href="'.$this->urltoroot.'zebra_datepicker/default.css">';
			$qa_content['custom'] .= '<script type="text/javascript" src="'.$this->urltoroot.'zebra_datepicker/zebra_datepicker.js"></script>';
			$qa_content['custom'] .= '<script type="text/javascript">
				$(document).ready(function() {
					$("#datepicker").Zebra_DatePicker({
						direction: ["'.substr($firstListDate,0,7).'", new Date().toISOString().substring(0,7)], // until today
						format: "Y-m", 
						lang_clear_date: "", 
						months: ['.$monthsString.'],
						offset: [15,250], 
						onSelect: function(view, elements) {
							$("form#datepick").submit();
						}
					});
				});
			</script>';

			// date picker input field
			$qa_content['custom'] .= '<form method="post" action="'.qa_self_html().'" id="datepick">
											<span>'.qa_lang('q2apro_bestusers_lang/choose_month').': &nbsp;</span>
											<input value="'.substr($chosenMonth,0,7).'" id="datepicker" name="request" type="text">
										  </form>';

			// we need to do another query to get the userscores of the recent month
			if( date('Y-m-01', strtotime($chosenMonth)) == date('Y-m-01') ) {
				// calculate userscores from recent month
				$queryRecentScores = qa_db_query_sub('
										SELECT ^userpoints.userid, ^userpoints.points - COALESCE(^userscores.points,0) AS mpoints 
										FROM `^userpoints`
										LEFT JOIN `^userscores` on ^userpoints.userid = ^userscores.userid 
											AND YEAR(^userscores.date) = YEAR(CURDATE()) 
											AND MONTH(^userscores.date) = MONTH(CURDATE()) '
										.$exludeusers_query.' 
										ORDER BY mpoints DESC, ^userpoints.userid DESC;');
				// thanks srini.venigalla for helping me with advanced mysql
				// http://stackoverflow.com/questions/11085202/calculate-monthly-userscores-between-two-tables-using-mysql
			}
			else {
				// calculate userscores for given month
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
						$uanswers = q2apro_bu_get_month_answers($userId, $intervalStart);
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
			$monthName = date('m/Y', strtotime($chosenMonth) );
			
			$qa_content['custom'] .= '<div class="bu_monthname">'.qa_lang_html('q2apro_bestusers_lang/bestusers').' '.$monthName.'</div>'; 
			$qa_content['custom'] .= $bestusers;
			$qa_content['custom'] .= '</div>';
			
			// link to all users list
			$qa_content['custom'] .= '<a href="'.qa_opt('site_url').'users" class="qa-form-tall-button" style="margin:40px 0;">'.qa_lang_html('q2apro_bestusers_lang/show_all_users').'</a>';
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