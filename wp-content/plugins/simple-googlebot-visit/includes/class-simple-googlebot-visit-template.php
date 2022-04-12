<?php

class Simple_Googlebot_Visit_Template {

	private $path = '';
	private $data = array();

	public function __construct($path, $labels = null) {
		if ($path) {
			$this->path = $path;
		}
		if ($labels) {
			$this->add($labels);
		}
	}

	public function get($key = null) {
		if ($key) {
			return isset($this->data[strtoupper($key)]) ?
				$this->data[strtoupper($key)] : strtoupper($key);
		} else {
			return $this->data;
		}
	}

	public function add($labels, $block = null) {
		if ($block) {
			$this->blocks($block, $labels);
			return false;
		}
		foreach ($labels as $key => $value) {
			$this->data[strtoupper($key)] = $value;
		}
		return true;
	}

	private function blocks($block, $labels) {
		// Before saving a block, we update it
		// including the index of the same
		$index = isset($this->data[$block]) ? sizeof($this->data[$block]) : 0;
		$this->data[$block][] = array_merge(array(
			'INDEX' => $index
		), array_change_key_case($labels, CASE_UPPER));
		return true;
	}

	private function load($file, $context = null) {
		// Get the template source
		$file = $this->path . '/' . $file;
		$fileCode = implode('', @file($file));
		if ($context) {
			$contextReference = 'CONTEXT_' . strtoupper(md5(sizeof($this->data)));
			$this->data[$contextReference] = $context;
			$fileCode = str_replace('{FOR ', '{FOR ' . $contextReference . '.', $fileCode);
			$fileCode = str_replace('{{', '{{' . $contextReference . '.', $fileCode);
		}
		return $fileCode;
	}

	public function render($mainFile, $regions = array()) {
		// Main template load
		$mainCode = $this->load($mainFile);
		// We load all regions and include
		// them in the main template code
		foreach ($regions as $key => $value) {
			$template = isset($value['TEMPLATE']) ? $value['TEMPLATE'] : $value;
			$context = isset($value['CONTEXT']) ? $value['CONTEXT'] : null;
			$regionCode = $this->load($template, $context);
			$mainCode = str_replace($key, $regionCode, $mainCode);
		}
		// After loading the regions, we compiled
		// the complete code and we execute it
		eval($this->compile($mainCode));
		return true;
	}

	public function getSource($mainFile, $regions = null) {
		// Main template load
		$mainCode = $this->load($mainFile);
		// We load all regions and include
		// them in the main template code
		$regions = (is_array($regions) ? $regions : array());
		while (list($regionFile, $regionLocation) = each($regions)) {
			$regionCode = $this->load($regionFile);
			$mainCode = str_replace($regionLocation, $regionCode, $mainCode);
		}
		return $mainCode;
	}

	private function replaceVars($code) {
		$varReferences = array();
		preg_match_all('#\{\{(UCASE_|LCASE_)?(.*?)\}\}#is', $code, $varReferences);
		foreach ($varReferences[2] as $key => $value) {
			$code = str_replace($varReferences[0][$key], '/*START-ISSET*/ . ' . $this->getIsset($value, array(
				'UPPER_CASE' => ($varReferences[1][$key] === 'UCASE_') ? true : false,
				'LOWER_CASE' => ($varReferences[1][$key] === 'LCASE_') ? true : false
			)) . '. /*END-ISSET*/', $code);
		}
		return $code;
	}

	private function getIsset($var, $options = array()) {
		$defaultValue = '\'\'';
		if (isset($options['FOR']) && $options['FOR']) {
			$defaultValue = 'array()';
		}
		$references = explode('.', $var);
		$references = array_reverse($references);
		$isset = '$temp = ({HERE})';
		$base = '';
		$base1 = '';
		foreach ($references as $key => $value) {
			$prefix = str_replace('.', '_', explode($value, $var)[0]);
			$base1 = '$_' . $prefix . $value . '_value' . $base;
			$base = '[\'' . $value . '\']' . $base;
			$look = '$this->data';
			$look = ($key === 0) ? $look . $base : $base1;
			$print = $look;
			if (isset($options['UPPER_CASE']) && $options['UPPER_CASE']) {
				$print = 'strtoupper(' . $print . ')';
			}
			if (isset($options['LOWER_CASE']) && $options['LOWER_CASE']) {
				$print = 'strtolower(' . $print . ')';
			}
			$isset = str_replace('{HERE}', '(isset(' . $look . ') ? ' . $print . ' : {HERE})', $isset);
			if ($key === (sizeof($references) - 1)) {
				$isset = str_replace('{HERE}', '(isset($this->data' . $base . ') ? $this->data' . $base . ' : {HERE})', $isset);
			}
		}
		$isset = str_replace('{HERE}', $defaultValue, $isset);
		return $isset;
	}

	// Compiles the given string of code,
	// and return the result in a string
	private function compile($code) {
		// Replace \ with \\ and then ' with \'.
		$code = str_replace('\\', '\\\\', $code);
		$code = str_replace('\'', '\\\'', $code);
		$code = $this->replaceVars($code);
		// Break it up into lines
		$codeLines = explode("\n", $code);
		for ($i = 0; $i < sizeof($codeLines); $i++) {
			$codeLines[$i] = chop($codeLines[$i]);
			if (preg_match('#{IF (.*?)}#', $codeLines[$i], $m)) {
				$ifCondition = str_replace('/*START-ISSET*/ .', '', $m[1]);
				$ifCondition = str_replace('. /*END-ISSET*/', '', $ifCondition);
				$codeLines[$i] = "\n" . 'if (' . $ifCondition . ')';
				$codeLines[$i] .= "\n" . '{';
			} elseif (preg_match('#{ELSE IF (.*?)}#', $codeLines[$i], $m)) {
				$ifCondition = str_replace('/*START-ISSET*/ .', '', $m[1]);
				$ifCondition = str_replace('. /*END-ISSET*/', '', $ifCondition);
				$codeLines[$i] = "\n" . ' } else if (' . $ifCondition . ')';
				$codeLines[$i] .= "\n" . '{';
			} else if (preg_match('#{ELSE}#', $codeLines[$i], $m)) {
				$codeLines[$i] = '} else {' . "\n";
			} else if (preg_match('#{END IF}#', $codeLines[$i], $m)) {
				$codeLines[$i] = '} // END IF';
			} else if (preg_match('#{FOR (.*?)}#', $codeLines[$i], $m)) {
				$key = str_replace('.', '_', $m[1]);
				$codeLines[$i] = 'foreach (' . $this->getIsset($m[1], array(
					'FOR' => true
				)) . ' as $_' . $key . '_key => $_' . $key . '_value)';
				$codeLines[$i] .= "\n" . '{';
			} else if (preg_match('#{END FOR}#', $codeLines[$i], $m)) {
				$codeLines[$i] = '} // END FOR';
			} else {
				$codeLines[$i] = 'echo \'' . $codeLines[$i] . '\' . "\\n";';
			}
		}
		// Bring it back into a single string of lines of code
		$code = implode("\n", $codeLines);
		$code = str_replace('/*START-ISSET*/', '\'', $code);
		$code = str_replace('/*END-ISSET*/', '\'', $code);
		return $code;
	}
}
