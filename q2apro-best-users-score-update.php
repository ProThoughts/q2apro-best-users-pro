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

	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../');
		exit;
	}

	class q2apro_bestusers_score_update {

		function init_queries($tableslc) {
			// none
		}
		
		function option_default($option) {
			// none
		}

		function process_event($event, $userid, $handle, $cookieid, $params) {
		
			if(qa_opt('q2apro_bestusers_enabled')) {
				/* Relevant events for userpoints that change the cached userscore values are: 
				 * registration
				 * question
				 * answer
				 * vote for question (up/down/nill)
				 * vote for answer (up/down/nill)
				 * answer selected as best + unselect
				 */
				$scoreEvents = array('u_register', 'q_post', 'a_post', 
									 'q_vote_up', 'q_vote_down', 'q_vote_nil', 'a_vote_up', 'a_vote_down', 'a_vote_nil',
									 'a_select', 'a_unselect');
									 
				if(in_array($event, $scoreEvents)) {
					q2apro_saveMonthlyUserscores();
					q2apro_cacheUserscores();
				}
			}
		}
	
	} // end class


/*
	Omit PHP closing tag to help avoid accidental output
*/