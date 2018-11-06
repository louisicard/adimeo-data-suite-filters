<?php

namespace AdimeoDataSuite\ProcessorFilter;

use AdimeoDataSuite\Model\Datasource;
use AdimeoDataSuite\Model\ProcessorFilter;

class MatchingListFilter extends ProcessorFilter {
  
  public function getDisplayName() {
    return "Matching list filter";
  }

  function getSettingFields()
  {
    return array(
      'matching_list_id' => array(
        'label' => 'Matching list',
        'type' => 'choice',
        'bound_to' => 'matching_list',
        'required' => true
      ),
      'case_insensitive' => array(
        'label' => 'Case insensitive input',
        'type' => 'boolean',
        'required' => false
      ),
      'default_value' => array(
        'label' => 'Default value',
        'type' => 'string',
        'trim' => false,
        'required' => false
      )
    );
  }
  
  
  public function getFields() {
    return array('output');
  }

  public function getArguments() {
    return array('input' => 'Input');
  }

  function execute(&$document, Datasource $datasource) {
    $settings = $this->getSettings();
    $input = $this->getArgumentValue('input', $document);
    $output = null;
    if (!empty($input)) {
      if (is_array($input))
        $data = $input;
      else
        $data = array($input);
      $output = array();
      $matchingList = $datasource->getMatchingList($settings['matching_list_id']);
      $list = json_decode(json_encode($matchingList->getList()), true);
      foreach ($data as $in) {
        $found = false;
        $out = '';
        if (is_string($in) && !empty($in)) {
          foreach ($list as $k => $v) {
            if ($settings['case_insensitive']) {
              if (strtolower($k) == strtolower($in)) {
                $found = true;
                $out = $v;
              }
            } else {
              if ($k == $in) {
                $found = true;
                $out = $v;
              }
            }
          }
        }
        if ($found) {
          if (!empty($out) && !in_array($out, $output)) {
            $output[] = $out;
          }
        } else {
          if (!empty($settings['default_value'])) {
            if (strtolower($settings['default_value']) != 'null' && !in_array($settings['default_value'], $output)) {
              $output[] = $settings['default_value'];
            }
          } else {
            if (!in_array($in, $output)) {
              $output[] = $in;
            }
          }
        }
      }
      unset($list);
      if (count($output) == 0) {
        $output = null;
      } elseif (count($output) == 1) {
        $output = $output[0];
      }
    }
    return array('output' => $output);
  }

}
