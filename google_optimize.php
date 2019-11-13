<?php
	namespace sv_tracking_manager;
	
	/**
	 * @version         1.000
	 * @author			straightvisions GmbH
	 * @package			sv100
	 * @copyright		2019 straightvisions GmbH
	 * @link			https://straightvisions.com
	 * @since			1.000
	 * @license			See license.txt or https://straightvisions.com
	 */
	
	class google_optimize extends modules {
		public function init() {
			// Section Info
			$this->set_section_title( __('Google Optimize', 'sv_tracking_manager' ) )
				 ->set_section_desc(__( sprintf('%sGoogle Optimize Login%s', '<a target="_blank" href="https://optimize.google.com/optimize/home/">','</a>'), 'sv_tracking_manager' ))
				 ->set_section_type( 'settings' )
				 ->load_settings()
				 ->register_scripts()
				 ->get_root()->add_section( $this );
			
			if($this->get_setting('specific_pages_only')->run_type()->get_data()){
				$this->google_optimize_metabox->set_parent($this);
				$this->google_optimize_metabox->set_root($this->get_root());
				$this->google_optimize_metabox->init();
			}else{
				add_action('init', array($this, 'load'));
			}
		}
		
		protected function load_settings(): google_optimize {
			$this->get_setting('activate')
				 ->set_title( __( 'Activate', 'sv_tracking_manager' ) )
				 ->set_description('Enable Tracking')
				 ->load_type( 'checkbox' );
			
			$this->get_setting('tracking_id')
				 ->set_title( __( 'Tracking ID', 'sv_tracking_manager' ) )
				 ->set_description( __( sprintf('%sHow to retrieve Tracking ID%s', '<a target="_blank" href="https://support.google.com/optimize/answer/6262084">','</a>'), 'sv_tracking_manager' ) )
				 ->load_type( 'text' );
			
			$this->get_setting('activate_anti_flicker')
				 ->set_title(__('Activate Anti Flicker Script', 'sv_tracking_manager'))
				 ->load_type('checkbox');
			
			$this->get_setting('specific_pages_only')
				 ->set_title(__('Specific Pages only', 'sv_tracking_manager'))
				 ->set_description(__('Activate this setting to enable Optimize for specific pages only. This will help to keep your site lean.', 'sv_tracking_manager'))
				 ->load_type('checkbox');
			
			return $this;
		}
		protected function register_scripts(): google_optimize {
			if($this->is_active()) {
				$this->get_script('default')
					->set_deps(array($this->get_parent()->google_analytics->get_script('ga')->get_handle()))
					 ->set_path('lib/frontend/js/default.js')
					 ->set_type('js');
				
				$this->get_script('anti_flicker')
					 ->set_deps(array($this->get_script('default')->get_handle()))
					 ->set_path('lib/frontend/js/anti_flicker.js')
					 ->set_type('js');
				
				$this->get_script('css_anti_flicker')
					 ->set_inline(true)
					 ->set_path('lib/frontend/css/anti_flicker.css');
			}
			
			return $this;
		}
		public function is_active(): bool{
			// activate not set
			if(!$this->get_setting('activate')->run_type()->get_data()){
				return false;
			}
			// activate not true
			if($this->get_setting('activate')->run_type()->get_data() !== '1'){
				return false;
			}
			// Tracking ID not set
			if(!$this->get_setting('tracking_id')->run_type()->get_data()){
				return false;
			}
			// Tracking ID empty
			if(strlen(trim($this->get_setting('tracking_id')->run_type()->get_data())) === 0){
				return false;
			}
			
			return true;
		}
		public function load(): google_optimize{
			if(
				$this->is_active()
				&& $this->get_parent()->google_analytics->is_active()
			){
				$this->get_script('default')
					 ->set_is_enqueued()
					 ->set_localized(array(
						 'tracking_id'	=> $this->get_setting('tracking_id')->run_type()->get_data()
					 ));
				
				if($this->get_setting('activate_anti_flicker')->run_type()->get_data()){
					$this->get_script('anti_flicker')->set_is_enqueued();
					$this->get_script('css_anti_flicker')->set_is_enqueued();
				}
			}
			
			return $this;
		}
	}