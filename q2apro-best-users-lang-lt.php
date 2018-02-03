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
	
	return array(
		// admin
		'enable_plugin' => 'Enable Plugin',
		'minimum_level' => 'Level to access this page:',
		'plugin_disabled' => 'Plugin has been disabled.',
		'access_forbidden' => 'Access forbidden.',
		'plugin_page_url' => 'Open page in forum:',
		'contact' => 'For questions please visit ^1q2apro.com^2',
		
		'page_disabled' => 'This page has been disabled.',
		'maxusers' => 'Maximum users to display in best users widget:',
		'maxuserspage_label' => 'Maximum users to display on best users pages:',
		'show_rewards' => 'Display rewards on best users pages',
		'show_rewards_widget' => 'Display rewards in widget',
		'exclude_admin' => 'Exclude admin from userscore listings and from widget',
		'exclude_users' => 'Exclude users from userscore listings and from widget (comma separated)',
		'enable_yearly' => 'Enable page "Best Users (Year)"',
		'rewardslist_m' => 'Monthly rewards (divide by semicolon):',
		'rewardslist_y' => 'Yearly rewards (divide by semicolon):',
		'months_abbr_label' => 'Abbreviations of the months for the Datepicker:',
		'months_abbr' => 'Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec',
		
		// widget + page
		'bestusers' => 'Šio mėnesio geriausi',
		'bestusers_m' => 'Šio mėnesio geriausi:',
		'bestusers_y' => 'Best users this year:',
		'points' => 'taškų',
		'rewards_monthly' => 'Mėnesio prizai',
		'rewards_yearly' => 'Metų prizai',
		'show_rewards_m' => '1-a vieta: 30 litų;2-a vieta: 20 litų;3-ia vieta: 10 litų',
		'show_rewards_y' => '1-a vieta: 60 litų;2-a vieta: 40 litų;3-ia vieta: 20 litų',
		'reward_title' => 'Kiekvieną mėnesį gaus geriausi nariai prizus!', // the mousetip when mouse is over reward field: <p class="rewardlist" title="x">...</p>
		'widget_showanswers' => 'Widget: Show answer count for each user',
		'widget_showcomments' => 'Widget: Show comment count for each user',
		'widget_showquestions' => 'Widget: Show question count for each user',
		'answer' => 'ats.', // atsakymas
		'answers' => 'ats.', // atsakymų
		'question' => 'klausimas',
		'questions' => 'klausimų',
		'comment' => 'komentaras',
		'comments' => 'komentarų',
		'place' => 'place',
		'widget_onbestuserslist' => 'Show widget on best users per month/per year lists in sidepanel',
		'bupage_show_acount' => 'Show number of answers of each user on best users pages',
		'bupage_show_location' => 'Show user\'s location on best users pages',
		'bupage_show_about' => 'Show user\'s about-me on best users pages',
		
		'reward_1' => '1. Place: USD 20',
		'reward_2' => '2. Place: USD 10',
		'reward_3' => '3. Place: USD 5',
		'reward_y1' => '1. Place: USD 40',
		'reward_y2' => '2. Place: USD 20',
		'reward_y3' => '3. Place: USD 10',
		
		// on page only
		'page_title_monthly' => 'Geriausi mėnesio nariai (top #)', // best users of each month (top 20), Geriausias mėnesio narys
		'page_title_yearly' => 'Geriausias metų narys (top #)', // best users of each year (top 20)
		'choose_month' => 'Išsirink mėnesį',
		'choose_year' => 'Išsirink metus',
		'show_all_users' => 'Visi nariai',
		
		// subnavigation on all users page
		'subnav_title' => 'Mėnesio laimėtojas', // best users of the month
		'subnav_title_year' => 'Metų laimėtojas', // best users of the year
		'subnav_points_rewards' => 'Taškai ir prizai',
		'subnav_points' => 'Taškų sistema', // Reputacija taškai
		'subnav_rewards' => 'Prizai',
		
		'none_this_month' => 'Šį mėnesį dar nėra.', // nobody yet this month
	);
	

/*
	Omit PHP closing tag to help avoid accidental output
*/