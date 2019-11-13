<?php
	namespace sv_tracking_manager;
	
	class google_optimize_metabox extends google_optimize{
		public function __construct(){
		
		}
		public function init(){
			$this->set_section_title('Optimize')
			->load_settings();
			
			add_action('wp', array($this, 'wp_init'));
		}
		protected function load_settings(): google_optimize {
			$this->get_setting('enable_on_page')
				 ->set_title(__('Enable', 'sv_tracking_manager'))
				 ->set_description(__('Enable Optimize on this page.', 'sv_tracking_manager'))
				 ->load_type('checkbox');
			
			static::$metabox->create($this)
							->set_title('Optimize');
			
			return $this;
		}
		public function wp_init(){
			if(is_front_page()){
				$post			= get_post(get_option('page_on_front'));
			}else{
				global $post;
			}
			
			if($post && get_post_meta(
					$post->ID,
					$this->get_setting('enable_on_page')->get_prefix(
						$this->get_setting('enable_on_page')->get_ID()
					), true)){
				add_action('init', array($this, 'load'));
			}
		}
	}