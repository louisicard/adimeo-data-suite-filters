<?php

namespace AdimeoDataSuite\ProcessorFilter;

use AdimeoDataSuite\Model\Datasource;
use AdimeoDataSuite\Model\ProcessorFilter;

class XMLParserToArrayFilter extends ProcessorFilter
{
  function getDisplayName()
  {
    return "XML Parser to array";
  }

  function getFields()
  {
    return array('array');
  }

  function getSettingFields()
  {
    return array();
  }

  function getArguments()
  {
    return array(
      'xml' => 'XML source',
    );
  }

  function execute(&$document, Datasource $datasource)
  {
    try {
      $xml = simplexml_load_string($this->getArgumentValue('xml', $document));

      $array = $this->serializeXml($xml);

      return array('array' => $array);
    } catch (\Exception $ex) {
      return array('array' => array());
    }
  }

  private function serializeXml(\SimpleXMLElement $xml){
    $r = array();
    foreach($xml->attributes() as $attr){
      /** @var \SimpleXMLElement $attr */
      $r['@attributes'][$attr->getName()] = (string)$attr;
    }
    if($xml->children()->count() === 0){
      $r['@value'] = (string)$xml;
    }
    foreach($xml->children() as $child){
      /** @var \SimpleXMLElement $child */
      foreach($child->attributes() as $attr){
        $val = array();
        $val['@attributes'][$attr->getName()] = (string)$attr;
      }
      if($child->children()->count() === 0){
        $val['@value'] = (string)$child;
      }
      else{
        $val = $this->serializeXml($child);
      }
      if(isset($r[$child->getName()])){
        if(array_keys($r[$child->getName()])[0] !== 0){
          $r[$child->getName()] = array($r[$child->getName()]);
        }
        $r[$child->getName()][] = $val;
      }
      else{
        $r[$child->getName()] = $val;
      }
    }

    return $r;
  }

}