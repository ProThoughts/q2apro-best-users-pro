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
		'checkdate' => 'Last point update was',
		
		// widget + page
		'bestusers' => 'Best Users',
		'bestusers_m' => 'Best users this month:',
		'bestusers_y' => 'Best users this year:',
		'points' => 'points',
		'rewards_monthly' => 'Monthly rewards',
		'rewards_yearly' => 'Yearly rewards',
		'show_rewards_m' => '1. Place: USD 20;2. Place: USD 10;3. Place: USD 5',
		'show_rewards_y' => '1. Place: USD 40;2. Place: USD 20;3. Place: USD 10',
		'reward_title' => 'Every month the best users win rewards!', // the mousetip when mouse is over reward field: <p class="rewardlist" title="x">...</p>
		'widget_showanswers' => 'Widget: Show answer count for each user',
		'widget_showcomments' => 'Widget: Show comment count for each user',
		'widget_showquestions' => 'Widget: Show question count for each user',
		'answer' => 'answer',
		'answers' => 'answers',
		'question' => 'question',
		'questions' => 'questions',
		'comment' => 'comment',
		'comments' => 'comments',
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
		'page_title_monthly' => 'Best Users per Month (Top #)', // best users of each month (top 20), Geriausias mėnesio narys
		'page_title_yearly' => 'Best Users per Year (Top #)', // best users of each year (top 20)
		'choose_month' => 'Choose month',
		'choose_year' => 'Choose year',
		'show_all_users' => 'Show all users',
		
		// subnavigation on all users page
		'subnav_title' => 'Best Users per Month', // best users of the month
		'subnav_title_year' => 'Best Users per Year', // best users of the year
		'subnav_points_rewards' => 'Points and Rewards',
		
		'none_this_month' => 'Nobody yet this month.',
	);
	

/*
	Omit PHP closing tag to help avoid accidental output
*/