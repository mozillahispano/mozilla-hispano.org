<?php
/**
 * Represents a template in a user-defined form.
 * @author Yaron Koren
 * @file
 * @ingroup SF
 */
class SFTemplateInForm {
	private $mTemplateName;
	private $mLabel;
	private $mAllowMultiple;
	private $mMaxAllowed;
	private $mFields;

	/**
	 * For a field name and its attached property name located in the
	 * template text, create an SFTemplateField object out of it, and
	 * add it to the $templateFields array.
	 */
	function handlePropertySettingInTemplate( $fieldName, $propertyName, $isList, &$templateFields, $templateText ) {
		global $wgContLang;
		$templateField = SFTemplateField::create( $fieldName, $wgContLang->ucfirst( $fieldName ), $propertyName, $isList );
		$cur_pos = stripos( $templateText, $fieldName );
		$templateFields[$cur_pos] = $templateField;
	}

	/**
	 * Get the fields of the template, along with the semantic property
	 * attached to each one (if any), by parsing the text of the template.
	 */
	function getAllFields() {
		global $wgContLang;
		$templateFields = array();
		$fieldNamesArray = array();

		// The way this works is that fields are found and then stored
		// in an array based on their location in the template text, so
		// that they can be returned in the order in which they appear
		// in the template, not the order in which they were found.
		// Some fields can be found more than once (especially if
		// they're part of an "#if" statement), so they're only
		// recorded the first time they're found.
		$template_title = Title::makeTitleSafe( NS_TEMPLATE, $this->mTemplateName );
		$template_article = null;
		if ( isset( $template_title ) ) $template_article = new Article( $template_title, 0 );
		if ( isset( $template_article ) ) {
			$templateText = $template_article->getContent();
			// Ignore 'noinclude' sections and 'includeonly' tags.
			$templateText = StringUtils::delimiterReplace( '<noinclude>', '</noinclude>', '', $templateText );
			$templateText = strtr( $templateText, array( '<includeonly>' => '', '</includeonly>' => '' ) );

			// First, look for "arraymap" parser function calls
			// that map a property onto a list.
			if ( $ret = preg_match_all( '/{{#arraymap:{{{([^|}]*:?[^|}]*)[^\[]*\[\[([^:]*:?[^:]*)::/mis', $templateText, $matches ) ) {
				foreach ( $matches[1] as $i => $field_name ) {
					if ( ! in_array( $field_name, $fieldNamesArray ) ) {
						$propertyName = $matches[2][$i];
						$this->handlePropertySettingInTemplate( $field_name, $propertyName, true, $templateFields, $templateText );
						$fieldNamesArray[] = $field_name;
					}
				}
			} elseif ( $ret === false ) {
				// There was an error in the preg_match_all()
				// call - let the user know about it.
				if ( preg_last_error() == PREG_BACKTRACK_LIMIT_ERROR ) {
					print 'Semantic Forms error: backtrace limit exceeded during parsing! Please increase the value of <a href="http://www.php.net/manual/en/pcre.configuration.php#ini.pcre.backtrack-limit">pcre.backtrack-limit</a> in the PHP settings.';
				}
			}

			// Second, look for normal property calls.
			if ( preg_match_all( '/\[\[([^:|\[\]]*:*?[^:|\[\]]*)::{{{([^\]\|}]*).*?\]\]/mis', $templateText, $matches ) ) {
				foreach ( $matches[1] as $i => $propertyName ) {
					$field_name = trim( $matches[2][$i] );
					if ( ! in_array( $field_name, $fieldNamesArray ) ) {
						$propertyName = trim( $propertyName );
						$this->handlePropertySettingInTemplate( $field_name, $propertyName, false, $templateFields, $templateText );
						$fieldNamesArray[] = $field_name;
					}
				}
			}

			// Then, get calls to #set and #set_internal
			// (thankfully, they have basically the same syntax).
			if ( preg_match_all( '/#(set|set_internal):(.*?}}})\s*}}/mis', $templateText, $matches ) ) {
				foreach ( $matches[2] as $match ) {
					if ( preg_match_all( '/([^|{]*?)=\s*{{{([^|}]*)/mis', $match, $matches2 ) ) {
						foreach ( $matches2[1] as $i => $propertyName ) {
							$fieldName = trim( $matches2[2][$i] );
							if ( ! in_array( $fieldName, $fieldNamesArray ) ) {
								$propertyName = trim( $propertyName );
								$this->handlePropertySettingInTemplate( $fieldName, $propertyName, false, $templateFields, $templateText );
								$fieldNamesArray[] = $fieldName;
							}
						}
					}
				}
			}

			// Then, get calls to #declare.
			if ( preg_match_all( '/#declare:(.*?)}}/mis', $templateText, $matches ) ) {
				foreach ( $matches[1] as $match ) {
					$setValues = explode( '|', $match );
					foreach ( $setValues as $valuePair ) {
						$keyAndVal = explode( '=', $valuePair );
						if ( count( $keyAndVal ) == 2 ) {
							$propertyName = trim( $keyAndVal[0] );
							$fieldName = trim( $keyAndVal[1] );
							if ( ! in_array( $fieldName, $fieldNamesArray ) ) {
								$this->handlePropertySettingInTemplate( $fieldName, $propertyName, false, $templateFields, $templateText );
								$fieldNamesArray[] = $fieldName;
							}
						}
					}
				}
			}

			// Finally, get any non-semantic fields defined.
			if ( preg_match_all( '/{{{([^|}]*)/mis', $templateText, $matches ) ) {
				foreach ( $matches[1] as $fieldName ) {
					$fieldName = trim( $fieldName );
					if ( !empty( $fieldName ) && ( ! in_array( $fieldName, $fieldNamesArray ) ) ) {
						$cur_pos = stripos( $templateText, $fieldName );
						$templateFields[$cur_pos] = SFTemplateField::create( $fieldName, $wgContLang->ucfirst( $fieldName ) );
						$fieldNamesArray[] = $fieldName;
					}
				}
			}
		}
		ksort( $templateFields );
		return $templateFields;
	}

	static function create( $name, $label = null, $allowMultiple = null, $maxAllowed = null, $formFields = null ) {
		$tif = new SFTemplateInForm();
		$tif->mTemplateName = str_replace( '_', ' ', $name );
		$tif->mFields = array();
		if ( is_null( $formFields ) ) {
			$fields = $tif->getAllFields();
			$field_num = 0;
			foreach ( $fields as $field ) {
				$tif->mFields[] = SFFormField::create( $field_num++, $field );
			}
		} else {
			$tif->mFields = $formFields;
		}
		$tif->mLabel = $label;
		$tif->mAllowMultiple = $allowMultiple;
		$tif->mMaxAllowed = $maxAllowed;
		return $tif;
	}

	function getTemplateName() {
		return $this->mTemplateName;
	}

	function getFields() {
		return $this->mFields;
	}

	function creationHTML( $template_num ) {
		$checked_str = ( $this->mAllowMultiple ) ? "checked" : "";
		$template_str = wfMsg( 'sf_createform_template' );
		$template_label_input = wfMsg( 'sf_createform_templatelabelinput' );
		$allow_multiple_text = wfMsg( 'sf_createform_allowmultiple' );
		$text = <<<END
	<input type="hidden" name="template_$template_num" value="$this->mTemplateName">
	<div class="templateForm">
	<h2>$template_str '$this->mTemplateName'</h2>
	<p>$template_label_input <input size=25 name="label_$template_num" value="$this->mLabel"></p>
	<p><input type="checkbox" name="allow_multiple_$template_num" $checked_str> $allow_multiple_text</p>
	<hr>

END;
		foreach ( $this->mFields as $field ) {
			$text .= $field->creationHTML( $template_num );
		}
		$removeTemplateButton = Html::input(
			'del_' . $template_num,
			wfMsg( 'sf_createform_removetemplate' ),
			'submit'
		);
		$text .= "\t" . Html::rawElement( 'p', null, $removeTemplateButton ) . "\n";
		$text .= "	</div>\n";
		return $text;
	}

	function createMarkup() {
		$text = "{{{for template|" . $this->mTemplateName;
		if ( $this->mAllowMultiple ) {
			$text .= "|multiple";
		}
		if ( $this->mLabel != '' ) {
			$text .= "|label=" . $this->mLabel;
		}
		$text .= "}}}\n";
		// For now, HTML for templates differs for multiple-instance
		// templates; this may change if handling of form definitions
		// gets more sophisticated.
		if ( ! $this->mAllowMultiple ) { $text .= "{| class=\"formtable\"\n"; }
		foreach ( $this->mFields as $i => $field ) {
			$is_last_field = ( $i == count( $this->mFields ) - 1 );
			$text .= $field->createMarkup( $this->mAllowMultiple, $is_last_field );
		}
		if ( ! $this->mAllowMultiple ) { $text .= "|}\n"; }
		$text .= "{{{end template}}}\n";
		return $text;
	}
}
