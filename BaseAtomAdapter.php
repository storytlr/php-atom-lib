<?php

require_once 'AtomNS.php';
require_once 'AtomAdapterBasic.php';

class AtomAdapterException extends Exception { }

abstract class BaseAtomAdapter {	
	protected $_atomNode;
	protected $_prefix;
	
	public function getBase() {
		return (string)$this->_atomNode->attributes()->{AtomNS::BASE_ATTRIBUTE};
	}
	
	public function getLang() {
		return (string)$this->_atomNode->attributes()->{AtomNS::LANG_ATTRIBUTE};
	}
	
	public function getXml() {
		return $this->_atomNode->asXML();
	}
	
	public function __construct($adapterType,$data,$data_is_url=false) {
		if (is_string($data)) { 
			$this->_atomNode = new SimpleXMLElement($data,null,$data_is_url);
		}
		else if ($data instanceof SimpleXMLElement) { 
			$this->_atomNode = $data;
		}
		else if ($data === null) {
			$this->_atomNode = new SimpleXMLElement("<".$adapterType." xmlns='".AtomNS::NAMESPACE."'></".$adapterType.">",null,$data_is_url);
		}
		else { 		
			throw new AtomAdapterException("Invalid Data Type");
		}
		
		if ($this->_atomNode->getName() != $adapterType) { //check whether $this->_atomNode is the appropriate XML Object, e.g. atom entry node for AtomEntryAdapter
			throw new AtomAdapterException("Invalid XML Object");
		}
		
		$this->_prefix = $this->_getPrefix(AtomNS::NAMESPACE);
		if ($this->_prefix === null) {
			throw new AtomAdapterException("Invalid Atom Document");
		}
	}
	
	public function __get($name) {
        $method = 'get' . $name;
        return $this->$method();
	}
	
	public function __set($name, $value) {
		$method = 'set' . $name;
		$this->$method($value);
	}
	
	protected function _getPrefix($namespace) {
		foreach($this->_atomNode->getDocNamespaces(true) as $prefix => $ns) {
			if ($ns == $namespace) {
				return $prefix;
			}
		}
		return null;
	}
}