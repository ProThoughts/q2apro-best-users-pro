<?php

/*
	Plugin Name: Best Users PRO
	Plugin URI: http://www.q2apro.com/plugins/best-users-pro
	Plugin Description: User scores get saved per month/year and rewards can be granted to your winning users. Widget and two separate pages.
	Plugin Version: 1.1
	Plugin Date: 2014-04-24
	Plugin Author: q2apro.com
	Plugin Author URI: http://www.q2apro.com
	Plugin Minimum Question2Answer Version: 1.6
	Plugin Update Check URI: http://www.q2apro.com/pluginupdate?id=63
	
	Licence: Copyright © q2apro.com - All rights reserved

*/

class qa_html_theme_layer extends qa_html_theme_base {

	function doctype(){
	
		qa_html_theme_base::doctype();

		if(qa_opt('q2apro_bestusers_enabled'))
		{
			global $qa_request;
			// adds subnavigation to pages bestusers and users
			if($qa_request == 'bestusers' || $qa_request == 'bestusers-year' || $qa_request == 'users' || $qa_request == 'rewards' || $qa_request == 'badges' ) {
			
				// add tab element to custom page 'points' - admin needs to create a page at /rewards for this
				/*
				$this->content['navigation']['sub']['rewards'] = array(
					'label' => qa_lang_html('q2apro_bestusers_lang/subnav_points_rewards'),
					'url' => qa_path_html('rewards'),
					'selected' => ($qa_request == 'rewards')
				);
				*/
				// add link to list all users (only admin by default, see qa-app-format.php, qa_users_sub_navigation()
				$this->content['navigation']['sub']['users$'] = array(
					'url' => qa_path_html('users'),
					'label' => qa_lang_html('main/highest_users'),
					'selected' => ($qa_request == 'users')
				);
				// add tab element best-users per month
				$this->content['navigation']['sub']['bestusers'] = array(
					'label' => qa_lang_html('q2apro_bestusers_lang/subnav_title'),
					'url' => qa_path_html('bestusers'),
					'selected' => ($qa_request == 'bestusers')
				);
				
				if(qa_opt('q2apro_bestusers_enable_yearly')) {
					// add tab element best-users per year
					$this->content['navigation']['sub']['bestusers-year'] = array(
						'label' => qa_lang_html('q2apro_bestusers_lang/subnav_title_year'),
						'url' => qa_path_html('bestusers-year'),
						'selected' => ($qa_request == 'bestusers-year')
					);
				}
			} // end subnavigation
			
			// if logged in user then check if monthly userscore has been set, if not, set it
			// this is necessary because the monthly scores get only updated with point events, see q2apro-best-users-score-update.php
			// if there are no point events on 1st of month, the monthly scores would not been set otherwise
			if(qa_is_logged_in()) {
				q2apro_saveMonthlyUserscores();
			}

		} // end enabled
	}
	
	// insert CSS file
	function head_script(){
		qa_html_theme_base::head_script();
		if(qa_opt('q2apro_bestusers_enabled')) {
			if($this->request == 'bestusers' || $this->request == 'bestusers-year') {
				$this->output('<link rel="stylesheet" type="text/css" href="'.QA_HTML_THEME_LAYER_URLTOROOT.'styles.css">');
			}
		}
	}


}


/*
	Omit PHP closing tag to help avoid accidental output
*/