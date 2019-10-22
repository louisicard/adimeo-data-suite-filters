<?php

namespace AdimeoDataSuite\ProcessorFilter;

use AdimeoDataSuite\Model\Datasource;
use AdimeoDataSuite\Model\ProcessorFilter;

class XPathGetterFilter extends ProcessorFilter
{
  function getDisplayName()
  {
    return "XPath Getter (SimpleXml)";
  }

  function getFields()
  {
    return array('value');
  }

  function getSettingFields()
  {
    return array(
      'xpath' => array(
        'label' => 'Xpath',
        'type' => 'string',
        'required' => true
      )
    );
  }

  function getArguments()
  {
    return array(
      'xml' => 'SimpleXml element',
    );
  }

  function execute(&$document, Datasource $datasource)
  {
    try{
      $settings = $this->getSettings();
      $xml = $this->getArgumentValue('xml', $document);
      /* @var $xml \SimpleXMLElement */
      if(get_class($xml) == 'SimpleXMLElement'){
        $r = $xml->xpath($settings['xpath']);
      }
      else{
        $r = array();
      }

      if(count($r) == 1 && strlen(trim($this->xmlToString($r[0]))) > 0){
        return array('value' => trim($this->xmlToString($r[0])));
      }
      elseif(count($r) > 1){
        $vals = array();
        foreach($r as $val){
          if(strlen(trim($this->xmlToString($val))) > 0 && !in_array(trim($this->xmlToString($val)), $vals)){
            $vals[] = trim($this->xmlToString($val));
          }
        }
        return array('value' => $vals);
      }
      else{
        return array('value' => null);
      }
    }catch(\Exception $ex){
      return array('value' => null);
    }
  }

  private function xmlToString(\SimpleXMLElement $elem) {
    return (string)$elem;
  }

}