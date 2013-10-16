<?php

/* - WPML compatibility - */
if(defined('ICL_SITEPRESS_VERSION') && defined('ICL_LANGUAGE_CODE')) 
{
	add_filter( 'avia_filter_base_data' , 'avia_wpml_options_language' );
	add_filter( 'avia_filter_backend_page_title' , 'avia_wpml_backend_page_title' );
	add_action( 'init', 'avia_wpml_register_post_type_permalink', 20);
	add_action( 'avia_action_before_framework_init', 'avia_wpml_get_languages');
	add_filter( 'icl_ls_languages' , 'avia_wpml_url_filter' );
	add_action( 'init', 'avia_wpml_backend_language_switch');
	add_action( 'avia_wpml_backend_language_switch', 'avia_default_dynamics');
	add_action( 'avia_wpml_backend_language_switch', 'avia_wpml_copy_options');
	add_action( 'wp_enqueue_scripts', 'avia_wpml_register_assets' );	
	add_action( 'avia_meta_header', 'avia_wpml_language_switch' );	
	
	/*
	* This function makes it possible that all backend options can be saved several times
	* for different languages. It appends a language string to the key of the options entry 
	* that is saved to the wordpress database.
	*
	* Since the Avia Framework only uses a single option array for the whole backend and
	* then serializes that array and saves it to a single database entry this is a very
	* easy and flexible method to setup your site in any way you want with muliple
	* languages, layouts, logos, dynamic templates, etc for each language
	*/
	
	if(!function_exists('avia_wpml_options_language'))
	{
		function avia_wpml_options_language($base_data)
		{
			global $avia_config;
			$wpml_options = $avia_config['wpml']['settings'];
			
			if((isset($wpml_options['default_language']) && $wpml_options['default_language'] != ICL_LANGUAGE_CODE) && 'all' != ICL_LANGUAGE_CODE && "" != ICL_LANGUAGE_CODE)
			{
				$base_data['prefix_origin'] = $base_data['prefix'];
				$base_data['prefix'] = $base_data['prefix'] . "_" . ICL_LANGUAGE_CODE;
			}
			
			return $base_data;
		}
	}
	
	/*fetch some default data necessary for the framework*/
	if(!function_exists('avia_wpml_get_languages'))
	{
		function avia_wpml_get_languages()
		{
			global $sitepress, $avia_config;
			$avia_config['wpml']['lang'] 		= $sitepress->get_active_languages();
			$avia_config['wpml']['settings'] 	= get_option('icl_sitepress_settings');
		}
	}
	
	/*language switch hook for the backend*/
	if(!function_exists('avia_wpml_backend_language_switch'))
	{
		function avia_wpml_backend_language_switch()
		{
			if(isset($_GET['lang']) && is_admin())
			{
				do_action('avia_wpml_backend_language_switch');
			}
		}
	}
	
	
	
	/* 
	  get an option from the database based on the option key passed. 
	  other then with the default avia_get_option function this one retrieves all language entries and passes them as array
	*/
	
	if(!function_exists('avia_wpml_get_options'))
	{
		function avia_wpml_get_options($option_key)
		{
			global $avia, $avia_config;
			
			if(!isset($avia->wpml))
			{
				$key 			= isset($avia->base_data['prefix_origin']) ? $avia->base_data['prefix_origin'] : $avia->base_data['prefix'];
				$key 			= 'avia_options_'.avia_backend_safe_string( $key );
				$wpml_options 	= $avia_config['wpml']['settings'];
				
				$key_array = array();
				if(is_array($avia_config['wpml']['lang'] ))
				{
					foreach($avia_config['wpml']['lang'] as $lang => $values)
					{
						if($wpml_options['default_language'] != $lang)
						{
							$key_array[$lang] = $key ."_".$lang;
						}
						else
						{
							$key_array[$lang] = $key;
						}
						
						$avia->wpml[$lang] = get_option($key_array[$lang]);
					}
				}
			}
			
			$option = array();
			
			if(isset($avia->wpml))
			{
				foreach($avia->wpml as $language => $option_set)
				{
					if(isset($option_set['avia']) && isset($option_set['avia'][$option_key]))
					{
						$option[$language] = $option_set['avia'][$option_key];
					}
					else
					{
						$option[$language] = false;
					}
				}
			}
			return $option;
		}
	}
	
	/*
	* Filters the menu entry in the backend and displays the language in addition to the theme name
	*/
	if(!function_exists('avia_wpml_backend_page_title'))
	{
		function avia_wpml_backend_page_title($title)
		{
			if(ICL_LANGUAGE_CODE == "") return $title;
			
			$append = "";
			if('all' != ICL_LANGUAGE_CODE) 
			{ 
				$append = " (".strtoupper( ICL_LANGUAGE_CODE ).")"; 
			}
			else
			{
				global $avia_config;
				
				$wpml_options 	= $avia_config['wpml']['settings'];
				$append 		= " (".strtoupper( $wpml_options['default_language'] ).")"; 
			}
			return $title . $append;
		}
	}
	
	/*
	* Creates an additional dynamic slug rewrite rule for custom categories
	*/
	if(!function_exists('avia_wpml_register_post_type_permalink'))
	{
		function avia_wpml_register_post_type_permalink() {
		
			global $wp_post_types, $wp_rewrite, $wp, $avia_config;
			
			if(!isset($avia_config['custom_post'])) return false;
			
			$slug_array = avia_wpml_get_options('portfolio-slug');
			
			foreach($avia_config['wpml']['lang'] as $lang => $values)
			{
				foreach($avia_config['custom_post'] as $post_type => $arguments)
				{
					$args = (object) $arguments['args'];
					$args->rewrite['slug'] = $slug_array[$lang];
					$args->permalink_epmask = EP_PERMALINK;
					$post_type = sanitize_key($post_type);
					
					if ( false !== $args->rewrite && ( is_admin() || '' != get_option('permalink_structure') ) ) 
					{ 
						$wp_rewrite->add_permastruct($post_type."_$lang", "{$args->rewrite['slug']}/%$post_type%", $args->rewrite['with_front'], $args->permalink_epmask);
					}
				}
			}
		}
	}
	
	/*
	* Filters the links generated for the language switcher in case a user is viewing a single portfolio entry and changes the portfolio slug if necessary
	*/
	if(!function_exists('avia_wpml_url_filter'))
	{
		function avia_wpml_url_filter($lang)
		{
			$post_type	= get_post_type();
			
			if("portfolio" == $post_type)
			{
				$slug 		= avia_wpml_get_options('portfolio-slug');
				
				$current 	= isset($slug[ICL_LANGUAGE_CODE]) ? $slug[ICL_LANGUAGE_CODE] : "";
				foreach ($lang as $key => $options)
				{
					if(isset($options['url']) && $current != "" && $current != $slug[$key] && "" != $slug[$key])
					{
						$lang[$key]['url'] = str_replace("/".$current."/", "/".$slug[$key]."/", $lang[$key]['url']);
					}
				}
			}
			return $lang;
		}
	}
	
	
	/*
	* register css styles
	*/
	if(!function_exists('avia_wpml_register_assets'))
	{
		function avia_wpml_register_assets()
		{
			wp_enqueue_style( 'avia-wpml', AVIA_BASE_URL.'config-wpml/wpml-mod.css');
		}
	}
	
	/*
	* styleswitcher for the avia framework
	*/
	if(!function_exists('avia_wpml_language_switch'))
	{
		function avia_wpml_language_switch()
		{
			$languages = icl_get_languages('skip_missing=0&orderby=id');
			$output = "";
			
			if(is_array($languages))
			{
				$output .= "<ul class='avia_wpml_language_switch'>";
			
				foreach($languages as $lang)
				{
	
					$output .= "<li class='language_".$lang['language_code']."'><a href='".$lang['url']."'>";
					$output .= "	<span class='language_flag'><img title='".$lang['native_name']."' src='".$lang['country_flag_url']."' /></span>";
					$output .= "	<span class='language_native'>".$lang['native_name']."</span>";
					$output .= "	<span class='language_translated'>".$lang['translated_name']."</span>";
					$output .= "	<span class='language_code'>".$lang['language_code']."</span>";
					$output .= "</a></li>";
				}
				
				$output .= "</ul>";
			}
			
			echo $output;
		}
	}
	
	/*
	* copy the default option set to the current language if no options set for this language is available yet
	*/
	if(!function_exists('avia_wpml_copy_options'))
	{
		function avia_wpml_copy_options()
		{
			global $avia, $avia_config;
			
			$key 			= isset($avia->base_data['prefix_origin']) ? $avia->base_data['prefix_origin'] : $avia->base_data['prefix'];
			$original_key 	= 'avia_options_'.avia_backend_safe_string( $key );
			$language_key	= 'avia_options_'.avia_backend_safe_string( $avia->base_data['prefix'] );
			
			if($original_key !== $language_key)
			{
				$lang_set = get_option($language_key);
				
				if(empty($lang_set))
				{
					$lang_set = get_option($original_key);
					update_option($language_key, $lang_set);
					
					wp_redirect( $_SERVER['REQUEST_URI'] );
					exit();
				}
			}
		}
	}
	
}





