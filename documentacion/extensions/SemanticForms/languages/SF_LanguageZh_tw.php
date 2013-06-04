<?php
/**
 * @author Yaron Koren  翻譯:張致信(Translation: Roc Michael Email:roc.no1@gmail.com)
 * @file
 * @ingroup SF
 */

/**
 * @ingroup SFLanguage
 */
class SF_LanguageZh_tw extends SF_Language {

	/* private */ var $m_SpecialProperties = array(
		// always start upper-case
		SF_SP_HAS_DEFAULT_FORM    => '預設表單',	// (Has default form) 
		SF_SP_HAS_ALTERNATE_FORM  => '代用表單'  // (Has alternate form)
	);

}

$m_SpecialPropertyAliases ['設有表單'] = SF_SP_HAS_DEFAULT_FORM;	// (Has default form) //Adding the item "Has alternate form", this item will not be suitable for translating into “設有表單”. It has changed to use “預設表單”. 

