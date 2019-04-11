<?php
if (!defined('LICENSE'))
{
	exit('Access Denied');
}



/**
 * Available Languages
 */
$languages = array(
	'English'	=>	'en_EN',
	'Spanish'	=>	'es_ES',
	'French'	=>	'fr_FR',
	'Dutch'		=>	'nl_NL',
	'Polish'	=>	'pl_PL',
	'Russian'	=>	'ru_RU'
	);

/**
 * Define language for get-text translator
 *
 * Directory structure for traduction must be:
 *		./locale/Lang/LC_MESSAGES/messages.mo
 * Example (French):
 *		./locale/fr_FR/LC_MESSAGES/messages.mo
 */
function defineLanguage($lang)
{
	$encoding = 'UTF-8';

	if (isset($lang)) {
		$locale = $lang;
	} else {
		$locale = DEFAULT_LOCALE;
	}

	// gettext setup
	T_setlocale(LC_MESSAGES, $locale);
	// Set the text domain as 'messages'
	$domain = 'messages';
	T_bindtextdomain($domain, LOCALE_DIR);
	T_bind_textdomain_codeset($domain, $encoding);
	T_textdomain($domain);
}

?>