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

	class q2apro_bestusers_admin {

		// initialize db-table 'userscores' if it does not exist yet
		function init_queries($tableslc) {
			$tablename = qa_db_add_table_prefix('userscores');
			
			if(!in_array($tablename, $tableslc)) {
				return 'CREATE TABLE IF NOT EXISTS `'.$tablename.'` (
				  `date` date NOT NULL,
				  `userid` int(10) unsigned NOT NULL,
				  `points` int(11) NOT NULL DEFAULT "0",
				  KEY `userid` (`userid`),
				  KEY `date` (`date`)
				)';
			}
		}
		
		// option value is requested but the option has not yet been set
		function option_default($option) {
			switch($option) {
				case 'q2apro_bestusers_enabled':
					return 1; // true
				case 'q2apro_bestusers_permission': // default level to access the best-users page
					return QA_PERMIT_ALL;
				case 'q2apro_bestusers_m1': // 1st best user in month for caching in DB
					return 0; 
				case 'q2apro_bestusers_m2':
					return 0;
				case 'q2apro_bestusers_m3':
					return 0;
				case 'q2apro_bestusers_m4':
					return 0;
				case 'q2apro_bestusers_checkdate':
					return date('Y-m-d');
				case 'q2apro_bestusers_maxusers':
					return 4;
				case 'q2apro_bestusers_maxuserspage':
					return 20;
				case 'q2apro_bestusers_showrewards':
					return 0; // false
				case 'q2apro_bestusers_showrewards_widget':
					return 0; // false
				case 'q2apro_bestusers_enable_yearly':
					return 1; // false
				case 'q2apro_bestusers_excludeusers':
					return 1; // should be admin id
				case 'q2apro_bestusers_rewardslist_m':
					return qa_lang('q2apro_bestusers_lang/show_rewards_m');
				case 'q2apro_bestusers_rewardslist_y':
					return qa_lang('q2apro_bestusers_lang/show_rewards_y');
				case 'q2apro_bestusers_months_abbr':
					return qa_lang('q2apro_bestusers_lang/months_abbr');
				case 'q2apro_bestusers_widget_showanswers':
					return 0; // false
				case 'q2apro_bestusers_widget_showcomments':
					return 0; // false
				case 'q2apro_bestusers_widget_showquestions':
					return 0; // false
				case 'q2apro_bestusers_widget_onbestuserslist':
					return 1; // true
				case 'q2apro_bestusers_bupage_show_acount':
					return 1; // true
				case 'q2apro_bestusers_bupage_show_location':
					return 0; // false
				case 'q2apro_bestusers_bupage_show_about':
					return 0; // false
				default:
					return null;				
			}
		}
			
		function allow_template($template) {
			return ($template!='admin');
		}       
			
		function admin_form(&$qa_content){                       

			// process the admin form if admin hit Save-Changes-button
			$ok = null;
			if (qa_clicked('q2apro_bestusers_save')) {
				$monthsabbr = qa_post_text('q2apro_bestusers_months_abbr');
				$monthsabbr = str_replace(' ', '', $monthsabbr); // remove whitespaces
				qa_opt('q2apro_bestusers_enabled', (bool)qa_post_text('q2apro_bestusers_enabled')); // empty or 1
				qa_opt('q2apro_bestusers_permission', (int)qa_post_text('q2apro_bestusers_permission')); // level
				qa_opt('q2apro_bestusers_maxusers', (int)qa_post_text('q2apro_bestusers_maxusers'));
				qa_opt('q2apro_bestusers_showrewards', (bool)qa_post_text('q2apro_bestusers_showrewards'));
				qa_opt('q2apro_bestusers_showrewards_widget', (bool)qa_post_text('q2apro_bestusers_showrewards_widget'));
				qa_opt('q2apro_bestusers_enable_yearly', (bool)qa_post_text('q2apro_bestusers_enable_yearly'));
				qa_opt('q2apro_bestusers_maxuserspage', (int)qa_post_text('q2apro_bestusers_maxuserspage'));
				qa_opt('q2apro_bestusers_excludeusers', qa_post_text('q2apro_bestusers_excludeusers'));
				qa_opt('q2apro_bestusers_rewardslist_m', qa_post_text('q2apro_bestusers_rewardslist_m'));
				qa_opt('q2apro_bestusers_rewardslist_y', qa_post_text('q2apro_bestusers_rewardslist_y'));
				qa_opt('q2apro_bestusers_months_abbr', $monthsabbr);
				qa_opt('q2apro_bestusers_widget_showanswers', (int)qa_post_text('q2apro_bestusers_widget_showanswers'));
				qa_opt('q2apro_bestusers_widget_showcomments', (int)qa_post_text('q2apro_bestusers_widget_showcomments'));
				qa_opt('q2apro_bestusers_widget_showquestions', (int)qa_post_text('q2apro_bestusers_widget_showquestions'));
				qa_opt('q2apro_bestusers_widget_onbestuserslist', (int)qa_post_text('q2apro_bestusers_widget_onbestuserslist'));
				qa_opt('q2apro_bestusers_bupage_show_acount', (int)qa_post_text('q2apro_bestusers_bupage_show_acount'));
				qa_opt('q2apro_bestusers_bupage_show_location', (int)qa_post_text('q2apro_bestusers_bupage_show_location'));
				qa_opt('q2apro_bestusers_bupage_show_about', (int)qa_post_text('q2apro_bestusers_bupage_show_about'));
				$ok = qa_lang('admin/options_saved');
				
				// update cache in case admin shall be excluded from widget
				q2apro_cacheUserscores();
			}
			
			// form fields to display frontend for admin
			$fields = array();
			
			$fields[] = array(
				'type' => 'checkbox',
				'label' => qa_lang('q2apro_bestusers_lang/enable_plugin'),
				'tags' => 'NAME="q2apro_bestusers_enabled"',
				'value' => qa_opt('q2apro_bestusers_enabled'),
			);
			
			$view_permission = (int)qa_opt('q2apro_bestusers_permission');
			$permitoptions = qa_admin_permit_options(QA_PERMIT_ALL, QA_PERMIT_SUPERS, false, false);
			$pluginpageURL = qa_opt('site_url').'bestusers';
			$fields[] = array(
				'type' => 'static',
				'note' => qa_lang('q2apro_bestusers_lang/plugin_page_url').' <a target="_blank" href="'.$pluginpageURL.'">'.$pluginpageURL.'</a>',
			);
			$fields[] = array(
				'type' => 'select',
				'label' => qa_lang('q2apro_bestusers_lang/minimum_level'),
				'tags' => 'name="q2apro_bestusers_permission"',
				'options' => $permitoptions,
				'value' => $permitoptions[$view_permission],
			);
			
			$fields[] = array(
				'type' => 'number',
				'label' => qa_lang('q2apro_bestusers_lang/maxusers'),
				'tags' => 'name="q2apro_bestusers_maxusers"',
				'value' => qa_opt('q2apro_bestusers_maxusers'),
			);
			
			$fields[] = array(
				'type' => 'number',
				'label' => qa_lang('q2apro_bestusers_lang/maxuserspage_label'),
				'tags' => 'name="q2apro_bestusers_maxuserspage"',
				'value' => qa_opt('q2apro_bestusers_maxuserspage'),
			);
			
			$fields[] = array(
				'type' => 'text',
				'label' => qa_lang('q2apro_bestusers_lang/exclude_users').':',
				'tags' => 'name="q2apro_bestusers_excludeusers"',
				'value' => qa_opt('q2apro_bestusers_excludeusers'),
			);
			
			$fields[] = array(
				'type' => 'checkbox',
				'label' => qa_lang('q2apro_bestusers_lang/enable_yearly'),
				'tags' => 'name="q2apro_bestusers_enable_yearly"',
				'value' => qa_opt('q2apro_bestusers_enable_yearly'),
			);
			
			$fields[] = array(
				'type' => 'checkbox',
				'label' => qa_lang('q2apro_bestusers_lang/show_rewards'),
				'tags' => 'name="q2apro_bestusers_showrewards"',
				'value' => qa_opt('q2apro_bestusers_showrewards'),
			);
			
			$fields[] = array(
				'type' => 'checkbox',
				'label' => qa_lang('q2apro_bestusers_lang/show_rewards_widget'),
				'tags' => 'name="q2apro_bestusers_showrewards_widget"',
				'value' => qa_opt('q2apro_bestusers_showrewards_widget'),
			);
			
			$fields[] = array(
				'type' => 'text',
				'label' => qa_lang('q2apro_bestusers_lang/rewardslist_m'),
				'tags' => 'name="q2apro_bestusers_rewardslist_m"',
				'value' => qa_opt('q2apro_bestusers_rewardslist_m'),
			);
			
			$fields[] = array(
				'type' => 'text',
				'label' => qa_lang('q2apro_bestusers_lang/rewardslist_y'),
				'tags' => 'name="q2apro_bestusers_rewardslist_y"',
				'value' => qa_opt('q2apro_bestusers_rewardslist_y'),
			);
			
			$fields[] = array(
				'type' => 'text',
				'label' => qa_lang('q2apro_bestusers_lang/months_abbr_label'),
				'tags' => 'name="q2apro_bestusers_months_abbr"',
				'value' => qa_opt('q2apro_bestusers_months_abbr'),
			);
			
			// with newest version
			$fields[] = array(
				'type' => 'checkbox',
				'label' => qa_lang('q2apro_bestusers_lang/widget_showanswers'),
				'tags' => 'name="q2apro_bestusers_widget_showanswers"',
				'value' => qa_opt('q2apro_bestusers_widget_showanswers'),
			);
			$fields[] = array(
				'type' => 'checkbox',
				'label' => qa_lang('q2apro_bestusers_lang/widget_showcomments'),
				'tags' => 'name="q2apro_bestusers_widget_showcomments"',
				'value' => qa_opt('q2apro_bestusers_widget_showcomments'),
			);
			$fields[] = array(
				'type' => 'checkbox',
				'label' => qa_lang('q2apro_bestusers_lang/widget_showquestions'),
				'tags' => 'name="q2apro_bestusers_widget_showquestions"',
				'value' => qa_opt('q2apro_bestusers_widget_showquestions'),
			);

			$fields[] = array(
				'type' => 'checkbox',
				'label' => qa_lang('q2apro_bestusers_lang/widget_onbestuserslist'),
				'tags' => 'name="q2apro_bestusers_widget_onbestuserslist"',
				'value' => qa_opt('q2apro_bestusers_widget_onbestuserslist'),
			);
			
			$fields[] = array(
				'type' => 'checkbox',
				'label' => qa_lang('q2apro_bestusers_lang/bupage_show_acount'),
				'tags' => 'name="q2apro_bestusers_bupage_show_acount"',
				'value' => qa_opt('q2apro_bestusers_bupage_show_acount'),
			);
			
			$fields[] = array(
				'type' => 'checkbox',
				'label' => qa_lang('q2apro_bestusers_lang/bupage_show_location'),
				'tags' => 'name="q2apro_bestusers_bupage_show_location"',
				'value' => qa_opt('q2apro_bestusers_bupage_show_location'),
			);
			
			$fields[] = array(
				'type' => 'checkbox',
				'label' => qa_lang('q2apro_bestusers_lang/bupage_show_about'),
				'tags' => 'name="q2apro_bestusers_bupage_show_about"',
				'value' => qa_opt('q2apro_bestusers_bupage_show_about'),
			);
			
			$fields[] = array(
				'type' => 'static',
				'note' => '<span style="color:#00C;">'.qa_lang('q2apro_bestusers_lang/checkdate').': '.qa_opt('q2apro_bestusers_checkdate').'</span>',
			);
			
			$fields[] = array(
				'type' => 'static',
				'note' => '<span style="font-size:75%;color:#789;">'.strtr( qa_lang('q2apro_bestusers_lang/contact'), array( 
							'^1' => '<a target="_blank" href="http://www.q2apro.com/plugins/best-users-pro">',
							'^2' => '</a>'
						  )).'</span>',
			);
			
			return array(           
				'ok' => ($ok && !isset($error)) ? $ok : null,
				'fields' => $fields,
				'buttons' => array(
					array(
						'label' => qa_lang_html('main/save_button'),
						'tags' => 'name="q2apro_bestusers_save"',
					),
				),
			);
		}
	}


/*
	Omit PHP closing tag to help avoid accidental output
*/