<?php

namespace ShrinkPress\Reframe\Evolve;

class Migration
{
	static function getFunction(array $f)
	{
		$function = (string) $f['function'];
		if (empty(self::migrateFunctions[ $function ]))
		{
			// dummy proto packages based on filename
			//
			// return Proto::getFunction($f);
			return phpDocPackages::getFunction($f);
		}

		$m = self::migrateFunctions[ $function ];
		return array(
			'method' => $m[0],
			'class' => $m[1],
			'namespace' => $m[2],
			'full' => "{$m[2]}\\{$m[1]}::{$m[0]}"
		);
	}

	/**
	* method, class, namespace
	*/
	const migrateFunctions = array(
		'export_add_js' => array('add_js', 'Export', 'ShrinkPress\\Admin'),
		'get_cli_args' => array('get_cli_args', 'Console', 'ShrinkPress\\Admin'),
		'wp_nav_menu_max_depth' => array('max_depth', 'Menu', 'ShrinkPress\\Admin'),
		'do_activate_header' => array('activate_header', 'Activate', 'ShrinkPress\\Activate'),

		// wp-admin/includes/class-pclzip.php
		// 'PclZipUtilPathReduction' => array('UtilPathReduction', 'Paths', 'ShrinkPress\\PclZip'),

		// wp-includes/l10n.php
		'get_locale' => array('get_locale', 'L10N', 'ShrinkPress\\I18N'),
		'get_user_locale' => array('get_user_locale', 'L10N', 'ShrinkPress\\I18N'),
		'determine_locale' => array('determine_locale', 'L10N', 'ShrinkPress\\I18N'),
	        'is_rtl' => array('is_rtl', 'L10N', 'ShrinkPress\\I18N'),
	        'switch_to_locale' => array('switch_to_locale', 'L10N', 'ShrinkPress\\I18N'),
	        'restore_previous_locale' => array('restore_previous', 'L10N', 'ShrinkPress\\I18N'),
	        'restore_current_locale' => array('restore_current', 'L10N', 'ShrinkPress\\I18N'),
	        'is_locale_switched' => array('is_switched', 'L10N', 'ShrinkPress\\I18N'),

		'translate' => array('translate', 'T9N', 'ShrinkPress\\I18N'),
		'translate_nooped_plural' => array('translate_nooped_plural', 'T9N', 'ShrinkPress\\I18N'),
		'translate_with_gettext_context' => array('translate_with_gettext_context', 'T9N', 'ShrinkPress\\I18N'),

		'__' => array('__', 'T9N', 'ShrinkPress\\I18N'),
		'_e' => array('_e', 'T9N', 'ShrinkPress\\I18N'),
		'_x' => array('_x', 'T9N', 'ShrinkPress\\I18N'),
		'_ex' => array('_ex', 'T9N', 'ShrinkPress\\I18N'),
		'_n' => array('_n', 'T9N', 'ShrinkPress\\I18N'),
		'_nx' => array('_nx', 'T9N', 'ShrinkPress\\I18N'),
		'_n_noop' => array('_n_noop', 'T9N', 'ShrinkPress\\I18N'),
		'_nx_noop' => array('_nx_noop', 'T9N', 'ShrinkPress\\I18N'),

		'esc_attr__' => array('esc_attr__', 'HTML', 'ShrinkPress\\I18N\\T9N'),
		'esc_attr_e' => array('esc_attr_e', 'HTML', 'ShrinkPress\\I18N\\T9N'),
		'esc_attr_x' => array('esc_attr_x', 'HTML', 'ShrinkPress\\I18N\\T9N'),
		'esc_html__' => array('esc_html__', 'HTML', 'ShrinkPress\\I18N\\T9N'),
		'esc_html_e' => array('esc_html_e', 'HTML', 'ShrinkPress\\I18N\\T9N'),
		'esc_html_x' => array('esc_html_x', 'HTML', 'ShrinkPress\\I18N\\T9N'),

		'load_textdomain' => array('load', 'TextDomain', 'ShrinkPress\\I18N\\T9N'),
		'unload_textdomain' => array('unload', 'TextDomain', 'ShrinkPress\\I18N\\T9N'),
		'load_default_textdomain' => array('load_default', 'TextDomain', 'ShrinkPress\\I18N\\T9N'),
		'load_plugin_textdomain' => array('load_plugin', 'TextDomain', 'ShrinkPress\\I18N\\T9N'),
		'load_muplugin_textdomain' => array('load_muplugin', 'TextDomain', 'ShrinkPress\\I18N\\T9N'),
		'load_theme_textdomain' => array('load_theme', 'TextDomain', 'ShrinkPress\\I18N\\T9N'),
		'load_child_theme_textdomain' => array('load_child_theme', 'TextDomain', 'ShrinkPress\\I18N\\T9N'),
		'load_script_textdomain' => array('load_script', 'TextDomain', 'ShrinkPress\\I18N\\T9N'),
		'is_textdomain_loaded' => array('is_loaded', 'TextDomain', 'ShrinkPress\\I18N\\T9N'),
		'_load_textdomain_just_in_time' => array('_load_just_in_time', 'TextDomain', 'ShrinkPress\\I18N\\T9N'),
		'get_translations_for_domain' => array('get_translations', 'TextDomain', 'ShrinkPress\\I18N\\T9N'),

		'wp_dropdown_languages' => array('dropdown_languages', 'UI', 'ShrinkPress\\I18N'),
		'before_last_bar' => array('before_last_bar', 'UI', 'ShrinkPress\\I18N'),
		'translate_user_role' => array('translate_user_role', 'UI', 'ShrinkPress\\I18N'),

		'wp_get_pomo_file_data' => array('get_file_data', 'PoMo', 'ShrinkPress\\I18N'),
		'wp_get_installed_translations' => array('get_installed_translations', 'PoMo', 'ShrinkPress\\I18N'),
		'get_available_languages' => array('get_available_languages', 'PoMo', 'ShrinkPress\\I18N'),
		'_get_path_to_translation_from_lang_dir' => array('_get_path_to_translation_from_lang_dir', 'PoMo', 'ShrinkPress\\I18N'),
		'_get_path_to_translation' => array('_get_path_to_translation', 'PoMo', 'ShrinkPress\\I18N'),
		'load_script_translations' => array('load_script_translations', 'PoMo', 'ShrinkPress\\I18N'),

	);
}
