<?php 

class Sublanguage_settings {
	
	/**
	 *	@from 1.0
	 */
	var $option_page_name = 'sublanguage_settings';
	
	
	/**
	 *	@from 1.0
	 */
	public function __construct() {
		
		add_action('admin_menu', array($this, 'admin_menu'));
		add_action('admin_init', array($this, 'admin_init') );
		
	}
	
	/**
	 *	@from 1.0
	 */
	public function admin_menu() {
	
		add_options_page(
			__('Sublanguage', 'sublanguage'), 
			__('Sublanguage', 'sublanguage'), 
			'manage_options', 
			$this->option_page_name, 
			array($this, 'options_page')
		);
			
	}
	
	/**
	 *	@from 1.0
	 */
	public function admin_init() {
		global $sublanguage_admin;
		
    	register_setting( 'language-group', $sublanguage_admin->option_name, array($this, 'sanitize_settings') );
    
		add_settings_section( 
			'section-settings', 
			__('Settings', 'sublanguage'), 
			array($this, 'section_settings'), 
			$this->option_page_name 
		);
		
		add_settings_field(	
			'translate-cpt', 
			__('Translate post types', 'sublanguage'), 
			array($this, 'field_translate_cpt'), 
			$this->option_page_name, 
			'section-settings'
		);
		
		add_settings_field(	
			'translate-taxonomies', 
			__('Translate Taxonomies', 'sublanguage'), 
			array($this, 'field_translate_taxonomies'), 
			$this->option_page_name, 
			'section-settings'
		);
		
		add_settings_field(	
			'main-language', 
			__('Original language', 'sublanguage'), 
			array($this, 'field_main_language'), 
			$this->option_page_name, 
			'section-settings'
		);
		
		add_settings_field(	
			'default-language', 
			__('Default language', 'sublanguage'), 
			array($this, 'field_default_language'), 
			$this->option_page_name, 
			'section-settings'
		);

		add_settings_field(	
			'show-slug', 
			__('Show slug for main language', 'sublanguage'), 
			array($this, 'field_show_slug'), 
			$this->option_page_name, 
			'section-settings'
		);
		
		add_settings_field(	
			'autodetect-language', 
			__('Auto-detect language', 'sublanguage'), 
			array($this, 'field_autodetect_language'), 
			$this->option_page_name, 
			'section-settings'
		);
		
		add_settings_field(	
			'current-first', 
			__('Current language first', 'sublanguage'), 
			array($this, 'field_current_first_language'), 
			$this->option_page_name, 
			'section-settings'
		);
		
		add_settings_field(	
			'sublanguage-version', 
			__('Version', 'sublanguage'), 
			array($this, 'field_version'), 
			$this->option_page_name, 
			'section-settings'
		);
		
	}
	
	/**
	 *	@from 1.0
	 */
	public function section_settings() {				
		
	}

	/**
	 *	@from 1.3
	 */
	function field_version($args) {
		global $sublanguage_admin;
      	
      	echo '<input type="hidden" name="'.$sublanguage_admin->option_name.'[version]" value="'.$sublanguage_admin->get_option('version').'"/>';
		echo '<p>'.$sublanguage_admin->get_option('version').'</p>';
		
	}
		
	/**
	 *	@from 1.0
	 */
	function field_translate_taxonomies($args) {
		global $sublanguage_admin;
        
		$taxonomies = get_taxonomies(array(
			'show_ui' => true
		), 'objects');
		
		if (isset($taxonomies)) {
		
			foreach ($taxonomies as $taxonomy) {
				
				$checked = in_array($taxonomy->name, $sublanguage_admin->get_taxonomies()) ? ' checked' : '';
				
				echo '<input type="checkbox" name="'.$sublanguage_admin->option_name.'[taxonomy][]" value="'.$taxonomy->name.'" id="'.$sublanguage_admin->option_name.'-taxi-'.$taxonomy->name.'"'.$checked.'/>
					<label for="'.$sublanguage_admin->option_name.'-taxi-'.$taxonomy->name.'">'.(isset($taxonomy->labels->name) ? $taxonomy->labels->name : $taxonomy->name).'</label><br/>';
				
			}
			
		}
		
		echo '<p>'.sprintf(__('You can set taxonomy translations in %s permalink section', 'sublanguage'), '<a href="'.admin_url('options-permalink.php').'">').'</a></p>';
		
	}
	
	/**
	 *	@from 1.0
	 */
	function field_translate_cpt($args) {
		global $sublanguage_admin;
       
		$cpts = get_post_types(array(
			'show_ui' => true,
			'public' => true,
		), 'objects' );
		
		if (isset($cpts)) {
		
			foreach ($cpts as $post_type) {
				
				$checked = in_array($post_type->name, $sublanguage_admin->get_post_types()) ? ' checked' : '';
				
				echo '<input type="checkbox" id="'.$sublanguage_admin->option_name.'-cpt-'.$post_type->name.'" name="'.$sublanguage_admin->option_name.'[cpt][]" value="'.$post_type->name.'" '.$checked.'/>
					<label for="'.$sublanguage_admin->option_name.'-cpt-'.$post_type->name.'">'.(isset($post_type->labels->name) ? $post_type->labels->name : $post_type->name).'</label><br/>';
				
			}
			
		}
		
		echo '<p>'.sprintf(__('You can set post-type translations in %s permalink section', 'sublanguage'), '<a href="'.admin_url('options-permalink.php').'">').'</a></p>';
		
	}

	/**
	 *	@from 1.1
	 */
	function field_main_language($args) {
		global $sublanguage_admin;
    
		$languages = $sublanguage_admin->get_languages();
   		
   		$html = '';
   		
   		if ($languages) {
   		
			$html .= sprintf('<label><select name="%s[main]">', $sublanguage_admin->option_name);
		
			foreach ($languages as $lng) {
		
				$html .= sprintf('<option value="%d"%s>%s</option>',
					$lng->ID,
					($sublanguage_admin->is_main($lng->ID)) ? ' selected' : '',
					$lng->post_title);
			
			}
		
			$html .= '</select> ';
			$html .= __('This is the langage that will be used if a translation is missing for a post.', 'sublanguage').'</label>';
			
		} 
		
		$html .= ' <a href="'.admin_url('edit.php?post_type='.$sublanguage_admin->language_post_type).'">'.__('Add language', 'sublanguage').'</a>';
		
		echo $html;
		
	}	
	
	/**
	 *	@from 1.1
	 */
	function field_default_language($args) {
		global $sublanguage_admin;
    
		$languages = $sublanguage_admin->get_languages();
   		
   		$html = '';
   		
   		if ($languages) {
   		
			$html .= '<label><select name="'.$sublanguage_admin->option_name.'[default]">';
		
			foreach ($languages as $lng) {
		
				$html .= sprintf('<option value="%d"%s>%s</option>',
					$lng->ID,
					($sublanguage_admin->is_default($lng->ID)) ? ' selected' : '',
					$lng->post_title);
			
			}
		
			$html .= '</select> ';
			$html .= __('This is the langage visitors will see when language is not specified in url.', 'sublanguage').'</label>';
		
		}
		
		echo $html;
		
	}	

	/**
	 *	@from 1.1
	 */
	function field_show_slug($args) {
		global $sublanguage_admin;
       
		echo sprintf('<label><input type="checkbox" name="%s" value="1"%s/>%s</label>', 
			$sublanguage_admin->option_name.'[show_slug]',
			$sublanguage_admin->get_option('show_slug') ? ' checked' : '',
			__('Show language slug for main language in site url', 'sublanguage')
		);
		
	}
	
	
	/**
	 *	@from 1.1
	 */
	function field_show_edit_language($args) {
		global $sublanguage_admin;
       
		echo sprintf('<label><input type="checkbox" name="%s" value="1"%s/>%s</label>', 
    	$sublanguage_admin->option_name.'[show_edit_lng]',
			$sublanguage_admin->get_option('show_edit_lng') ? ' checked' : '',
			__('Display current language in content and title fields when editing post.', 'sublanguage')
		);
		
	}

	/**
	 *	@from 1.2
	 */
	function field_autodetect_language($args) {
		global $sublanguage_admin;
       
		echo sprintf('<label><input type="checkbox" name="%s" value="1"%s/>%s</label>', 
			$sublanguage_admin->option_name.'[autodetect]',
			$sublanguage_admin->get_option('autodetect') ? ' checked' : '',
			__('Auto-detect language when language is not specified in url.', 'sublanguage')
		);
		
	}


	/**
	 *	@from 1.2
	 */
	function field_current_first_language($args) {
		global $sublanguage_admin;
    	   
		echo sprintf('<label><input type="checkbox" name="%s" value="1"%s/>%s</label>', 
			$sublanguage_admin->option_name.'[current_first]',
			$sublanguage_admin->get_option('current_first') ? ' checked' : '',
			__('Set the current language to be the first in the language selectors.', 'sublanguage')
		);
		
	}

	/**
	 *	@from 1.0
	 */
	public function options_page() {
		
		?>
			<div class="wrap">
				<h2>Language Settings</h2>
				<form action="options.php" method="POST">
						<?php settings_fields('language-group'); ?>
						<?php do_settings_sections($this->option_page_name); ?>
						<?php submit_button(); ?>
				</form>
			</div>
		<?php
		    
	}
	

	/**
	 *	@from 1.0
	 */
	public function sanitize_settings($input) {
		
		$output = array();
		$output['cpt'] = isset($input['cpt']) ? array_map('esc_attr', $input['cpt']) : array();
		$output['taxonomy'] = isset($input['taxonomy']) ? array_map('esc_attr', $input['taxonomy']) : array();
		$output['show_slug'] = (isset($input['show_slug']) && $input['show_slug']);
		$output['main'] = isset($input['main']) && $input['main'] ? $input['main'] : 0;
		$output['default'] = isset($input['default']) && $input['default'] ? $input['default'] : 0;
		$output['autodetect'] = (isset($input['autodetect']) && $input['autodetect']);
		$output['current_first'] = (isset($input['current_first']) && $input['current_first']);
		$output['version'] = isset($input['version']) ? esc_attr($input['version']) : '-';
		
    	return $output;
	}


}


