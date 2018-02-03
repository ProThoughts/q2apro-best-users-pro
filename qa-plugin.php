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

if ( !defined('QA_VERSION') ) {
	header('Location: ../../');
	exit;
}

// widget
qa_register_plugin_module('widget', 'q2apro-best-users-widget.php', 'q2apro_bestusers_widget', 'q2apro Best Users Widget');

// page (month)
qa_register_plugin_module('page', 'q2apro-best-users-page-month.php', 'q2apro_bestusers_page_month', 'q2apro Best Users per Month Page');

// page (year)
qa_register_plugin_module('page', 'q2apro-best-users-page-year.php', 'q2apro_bestusers_page_year', 'q2apro Best Users per Year Page');

// language file
qa_register_plugin_phrases('q2apro-best-users-lang-*.php', 'q2apro_bestusers_lang');

// change default users page, add subnavigation (tabs)
qa_register_plugin_layer('q2apro-best-users-layer.php', 'q2apro Best Users Layer');

// event module updates userscores and replace cronjob, saves scores to db each 1st of month
qa_register_plugin_module('event', 'q2apro-best-users-score-update.php', 'q2apro_bestusers_score_update', 'q2apro Best-Users Score Update');

// admin
qa_register_plugin_module('module', 'q2apro-best-users-admin.php', 'q2apro_bestusers_admin', 'q2apro Best-Users Admin');


// q2apro custom function
function q2apro_saveMonthlyUserscores() {
	// monthly score saving
	// this replaces the cronjob of the free best-users-per-month version
	$date = date('Y-m-d');
	// all dates get saved to db on 1st day of month
	$dateMonth = date('Y-m-01');
	
	if($date != qa_opt('q2apro_bestusers_checkdate') ) {
		qa_opt('q2apro_bestusers_checkdate', $date);
		
		// when first install and in the middle of the month, the table qa_userscores is empty
		// do not save the userscores until we reach the next 1st of month
		if($date != $dateMonth) {
			$scoreCount = qa_db_read_one_value(
							qa_db_query_sub('SELECT COUNT(*) FROM `^userscores`'), 
										true);
			if($scoreCount==0) {
				return;
			}
		}
		
		// to avoid double entries, check if data was not already set for THIS MONTH
		$checkDate = qa_db_read_one_assoc(
						qa_db_query_sub('SELECT `date` FROM `^userscores` 
										WHERE YEAR(`date`) = YEAR("'.$date.'") 
										AND MONTH(`date`) = MONTH("'.$date.'");'), 
										true);
		if(isset($checkDate['date'])) { 
			// userscores for this month already exist
		}
		else {
			// copy userid and userpoints to our qa_userscores table
			qa_db_query_sub('INSERT INTO `^userscores` (userid, points, date) 
								SELECT userid, points, "'.$dateMonth.'" AS date FROM `^userpoints` 
								ORDER BY userid ASC;');
		}
		
		// update score cache to show correct userscores in widget
		q2apro_cacheUserscores();
	}
}

// q2apro custom function
function q2apro_cacheUserscores() {
	$exludeusers = qa_opt('q2apro_bestusers_excludeusers');
	$exludeusers_query = '';
	if(!empty($exludeusers))
	{
		// remove whitespaces
		$exludeusers = str_replace(' ', '', $exludeusers);
		$exludeusers_query = 'WHERE ^userpoints.userid NOT IN ('.$exludeusers.')';
	}
	
	// compare userscores from last month to userpoints now (this query is considering new users that do not exist in qa_userscores) 
	// as we order by mpoints the query returns best users first, and we do not need to sort by php: arsort($scores)
	$queryRecentScores = qa_db_query_sub('SELECT ^userpoints.userid, ^userpoints.points - COALESCE(^userscores.points,0) AS mpoints 
							FROM `^userpoints`
							LEFT JOIN `^userscores` on ^userpoints.userid=^userscores.userid 
								AND YEAR(^userscores.date) = YEAR(CURDATE()) 
								AND MONTH(^userscores.date) = MONTH(CURDATE()) '.
							$exludeusers_query.' 
							ORDER BY mpoints DESC, ^userpoints.userid DESC;');
		// thanks srini.venigalla for helping me with advanced mysql
		// http://stackoverflow.com/questions/11085202/calculate-monthly-userscores-between-two-tables-using-mysql

	// save all userscores in array $scores
	$scores = array();
	while( ($row = qa_db_read_one_assoc($queryRecentScores,true)) !== null ) {
		$scores[$row['userid']] = $row['mpoints'];
	}

	$maxusers = qa_opt('q2apro_bestusers_maxusers'); // max users to display in widget
	$nrUsers = 0;
	foreach ($scores as $userid => $userscore) {
		// no users with 0 points
		if($userscore>0) {
			// get userdata
			$user = qa_db_select_with_pending( qa_db_user_account_selectspec($userid, true) );
			// check if user is blocked
			if (!(QA_USER_FLAGS_USER_BLOCKED & $user['flags'])) {
				// save bestusers in cache
				qa_opt('q2apro_bestusers_m'.(++$nrUsers), $userid.','.$userscore);
				// max users to display
				if($nrUsers >= $maxusers) break;
			}
		}
	}
	// no user counted (beginning of month!)
	if($nrUsers==0) {
		// free cache
		while($nrUsers <= $maxusers) {
			qa_opt('q2apro_bestusers_m'.($nrUsers+1), '');
			$nrUsers++;
		}
	}
	// in case the user slots have not been filled we have to empty the other cached scores to remove former values
	else if($nrUsers!=$maxusers) {
		while($nrUsers<$maxusers) {
			qa_opt('q2apro_bestusers_m'.(++$nrUsers), '');
		}
	}
}

function q2apro_bu_get_userlocation($userid) {
	// no such user exists
	if(empty($userid) || $userid < 1) {
		return;
	}
	$userLocation = qa_db_read_one_value(qa_db_query_sub('SELECT content FROM ^userprofile
															WHERE userid=#
															AND title="location"
															', $userid), true);
	return $userLocation;
}

function q2apro_bu_get_userabout($userid) {
	// no such user exists
	if(empty($userid) || $userid < 1) {
		return;
	}
	$userAbout = qa_db_read_one_value(qa_db_query_sub('SELECT content FROM ^userprofile
															WHERE userid=#
															AND title="about"
															', $userid), true);
	return $userAbout;
}

function q2apro_bu_get_month_answers($userid, $date) {
	// no such user exists
	if(empty($userid) || $userid < 1 || empty($date)) {
		return;
	}
	$month = date('m',strtotime($date));
	$year = date('Y',strtotime($date));
	$time = mktime(0, 0, 0, $month, 1, $year); 
	
	$monthstart = date('Y-m-d', $time);
	$monthend = date('Y-m-t', $time);

	$userAnswerCount = qa_db_read_one_value(
							qa_db_query_sub('SELECT COUNT(*) FROM ^posts
												WHERE userid=#
												AND type="A"
												AND created BETWEEN # AND  #', $userid, $monthstart, $monthend),
											true);
	return $userAnswerCount;
}

function q2apro_bu_get_year_answers($userid, $date) {
	// no such user exists
	if(empty($userid) || $userid < 1 || empty($date)) {
		return;
	}
	$year = date('Y',strtotime($date));
	$timestart = mktime(0, 0, 0, 1, 1, $year); 
	$timeend = mktime(0, 0, 0, 12, 1, $year); 
	
	$yearstart = date('Y-m-d', $timestart);
	$yearend = date('Y-m-t', $timeend);

	$userAnswerCount = qa_db_read_one_value(
							qa_db_query_sub('SELECT COUNT(*) FROM ^posts
												WHERE userid=#
												AND type="A"
												AND created BETWEEN # AND  #', $userid, $yearstart, $yearend),
											true);
	return $userAnswerCount;
}



/*
	Omit PHP closing tag to help avoid accidental output
*/