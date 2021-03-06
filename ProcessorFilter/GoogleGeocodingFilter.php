<?php

namespace AdimeoDataSuite\ProcessorFilter;

use AdimeoDataSuite\Model\Datasource;
use AdimeoDataSuite\Model\ProcessorFilter;
use GuzzleHttp\Client;

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

        $client = new Client();
        $response = $client->request('GET', $google_url);
        $json = json_decode($response->getBody(), TRUE);
        if(isset($json['error_message']) && !empty($json['error_message'])) {
          throw new \Exception('Google maps error : ' . $json['error_message']);
        }
        if (isset($json['status']) && $json['status'] == 'OK' && isset($json['results'][0])) {
          usleep(100000);//Sleep for 100ms
          return array('location' => $json['results'][0]);
        }
      }
      return array('value' => null);

    } catch (\Exception $ex) {
      $datasource->getOutputManager()->writeLn('Exception ==> ' . $ex->getMessage());
      return array('value' => null);
    }
  }

}