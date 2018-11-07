<?php

namespace AdimeoDataSuite\ProcessorFilter;

use AdimeoDataSuite\Model\Datasource;
use AdimeoDataSuite\Model\ProcessorFilter;

class XPathFinderFilter extends ProcessorFilter
{
  function getDisplayName()
  {
    return "Xpath finder Parser";
  }

  function getFields()
  {
    return array('output');
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
      'xml_xpath' => 'XML xpath document',
    );
  }

  function execute(&$document, Datasource $datasource)
  {
    try {
      $xpath = $this->getArgumentValue('xml_xpath', $document);
      $settings = $this->getSettings();
      $query = $settings['xpath'];
      $queries = array_map('trim', explode(',', $query));
      if ($xpath != null) {
        $r = array();
        foreach ($queries as $query) {
          /** @var \DOMXPath $xpath */
          for ($i = 0; $i < $xpath->query($query)->length; $i++) {

            $r[] = $xpath->query($query)->item($i)->textContent;
          }
        }
        unset($xpath);
        unset($settings);
        unset($query);
        unset($queries);

        gc_enable();
        gc_collect_cycles();
        return array('output' => $r);
      }
    } catch (\Exception $ex) {
      $datasource->getOutputManager()->writeLn('Exception ==> ' . $ex->getMessage());
    }
    return array('output' => array());
  }

}