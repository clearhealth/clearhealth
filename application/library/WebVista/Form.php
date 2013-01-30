<?php
/*****************************************************************************
*       Form.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/

class WebVista_Form extends Zend_Form {

	const WINDOW_RELOAD = 1;
	const WINDOW_CLOSE = 2;
	const WINDOW_FUNCTION = 2;
	protected $_window;
	protected $_windowAction = WebVista_Form::WINDOW_RELOAD;

	public function loadORM(ORM $obj,$namespace = null) {
		$this->setElementsBelongTo(lcfirst($namespace));
		$fields = $obj->ormFields();
		foreach ($fields as $field) {
			if ($obj->legacyORMNaming) {
				$field = preg_replace_callback('/_(.)/',
					create_function(
						'$matches',
						'return strtoupper($matches[1]);'
					),
					$field);
			}
			if (!is_object($obj->$field)) {
				if (preg_match('/^date.*/',$field)) {
					$element = $this->createElement('dateText',$field, array('label' => $this->_prettyName($field)));
				}
				elseif (preg_match('/.*_id$/',$field)) {
					$element = $this->createElement('hidden',$field, array('label' => $this->_prettyName($field)));
				}
				else {
					$element = $this->createElement('text',$field, array('label' => $this->_prettyName($field)));
				}
				$element->setValue($obj->$field);
				$this->addElement($element);
			}
			elseif ($obj->$field instanceof ORM) {
				$sf = new WebVista_Form();
				$this->addSubForm($sf,"form" . ucwords($field));
				$sf->loadORM($obj->$field, $namespace . '['. lcfirst($field) . ']');
			}
			
		}
	}

	private function _prettyName($name) {
		$name = trim($name);
		$name = preg_replace('/([a-z])Id$/','$1',$name);
		$name = preg_replace('/([A-Z])(?![A-Z])/',' $1',$name);
		$name = ucwords($name);
		//echo "$name<br />";
		return trim($name);
	}

	public function __call($method,$args) {
	/*	if (preg_match('/(.*)Label$/',$method,$matches)) {
			if ($this->$matches[1]) {
				return $this->_generateLabel($matches[1]);
			}
		}*/
		if (preg_match('/(.*)Input$/',$method,$matches)) {
			if (strlen($matches[1]) > 0) {
				return $this->_generateInput($matches[1],$args);
			}
		}
		return parent::__call($method,$args);
	}

	/*protected function _generateLabel($labelName, array $options = null) {
		return $this->getView()->formLabel($this->getElement($labelName)->getName(),$this->getElement($labelName)->getLabel());
	}*/

	protected function _generateInput($inputName, $args = null) {
		$element = $this->$inputName;
		if (isset($args[0])) {
			$options = null;
			if (isset($args[1])) $options = $args[1];
			if ($options !== null) {
				$options = array_merge($options,$element->getAttribs());
			}
			else {
				$options = $element->getAttribs();
			}

			if ($element instanceOf Zend_Dojo_Form_Element_DateTextBox) {
				return $this->getView()->{ucwords($args[0])}($element->getFullyQualifiedName(),$element->getValue(),array(),$options);
			}
			else {
				if (ucwords($args[0]) == "FormSelect" && isset($options['options'])) {
					return $this->getView()->{ucwords($args[0])}($element->getFullyQualifiedName(),$element->getValue(),$options,$options['options']);
				}
				else {
					return $this->getView()->{ucwords($args[0])}($element->getFullyQualifiedName(),$element->getValue(),$options);
				}
			}
			
		}
		$inputHTML = $this->getView()->{$element->helper}($element->getFullyQualifiedName(),$element->getValue(),$element->getAttribs());
		return $inputHTML;
	}

	public function renderOpen(Zend_View_Interface $view = null) {
		$zvhf = new Zend_View_Helper_Form();
		$zvhf->setView($this->getView());
		return $zvhf->form($this->getName(),$this->getAttribs());
	}

	public function renderClose($closeTag = true) {
		$loadFunction = "alert(data);";
		if ($this->getWindow() && $this->_windowAction == Webvista_Form::WINDOW_RELOAD) {
			$loadFunction = <<<EOS
if (window.postSubmit{$this->getId()}) {
                var retval = postSubmit{$this->getId()}(data);
                if (retval === false) return false;
        }
	dhxWins.window('{$this->getWindow()}').attachHTMLString(data);
EOS;
		}
		elseif ($this->getWindow() && $this->_windowAction == Webvista_Form::WINDOW_CLOSE) {
			$loadFunction = <<<EOS
if (window.postSubmit{$this->getId()}) {
                var retval = postSubmit{$this->getId()}();
                if (retval === false) return false;
        }
dhxWins.window('{$this->getWindow()}').close();
EOS;
		}
		$dojoHeader = <<<EOS
<script language="javascript">
function submit{$this->getId()}Form() {
	if ('function' == typeof dojo.byId('{$this->getId()}').onFormSubmit) {
		dojo.byId('{$this->getId()}').onFormSubmit();
	}
	if (window.preSubmit{$this->getId()}) {
		var retval = preSubmit{$this->getId()}();
		if (retval === false) return false;
	}
dojo.xhrPost ({
    url: "{$this->getAction()}",
    form: '{$this->getId()}',
    load: function(data){
	{$loadFunction}
     },
     error: function (error) {
	console.error ('Error: ', error);
     }
    });
}
</script>
EOS;
		if ($closeTag) {
        	        return "</form>".$dojoHeader;
		}
        	        return $dojoHeader;
	}

	function __construct($options = array()) {
        	$this->setMethod("post");
		parent::__construct($options);
		//$translate = Zend_Registry::get('translate');
		//$this->setTranslator($translate);
		$element = $this->createElement('button','ok', array('label' => 'OK'));
		$element->setAttrib('onclick',"submit" . $this->getId() . "Form();");
		$element->setValue('OK');
                $this->addElement($element);
		$this->addPrefixPath('WebVista_Form_Decorator','WebVista/Form/Decorator/','decorator');
		$this->addPrefixPath('WebVista_Form_Element','WebVista/Form/Element/','element');
		
	}

	public function setWindow($window) {
		$this->_window = $window;
	}

	public function getWindow() {
		return $this->_window;
	}

	public function setWindowAction($action) {
		$this->_windowAction = $action;
	}
	
}
