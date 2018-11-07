<?php

namespace AdimeoDataSuite\ProcessorFilter;

use AdimeoDataSuite\Model\Datasource;
use AdimeoDataSuite\Model\ProcessorFilter;

class GoogleGeocodingFilter extends ProcessorFilter
{
  function getDisplayName()
  {
    return "Google Geocoding Filter";
  }

  function getFields()
  {
    return array('location');
  }

  function getSettingFields()
  {
    return array(
      'api_key' => array(
        'label' => 'API key',
        'type' => 'string',
        'required' => false
      )
    );
  }

  function getArguments()
  {
    return array(
      'address' => 'Address',
    );
  }

  function execute(&$document, Datasource $datasource)
  {
    try {
      $settings = $this->getSettings();
      $apiKey = isset($settings['api_key']) ? $settings['api_key'] : '';
      $address = $this->getArgumentValue('address', $document);

      if (!empty($address)) {
        $google_url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address);
        if (!empty($apiKey))
          $google_url .= '&key=' . $apiKey;

        $json = $this->getUrlResponse($google_url);
        if (isset($json['status']) && $json['status'] == 'OK' && isset($json['results'][0])) {
          usleep(100000);//Sleep for 100ms
          return array('location' => $json['results'][0]);
        }
      }
      return array('value' => null);

    } catch (\Exception $ex) {
      $datasource->getOutputManager()->writeLn($ex);
      return array('value' => null);
    }
  }

  private function getUrlResponse($url)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $r = curl_exec($ch);
    curl_close($ch);
    return json_decode($r, true);
  }

}