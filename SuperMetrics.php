<?php

namespace apiassignment\supermetrics;
use Exception;


/**
 * Class SuperMetrics
 * @package apiassignment
 */
class SuperMetrics
{
    private $_clientId;
    private $_email;
    private $_name;

    public function __construct($clientId, $email, $name) {
        $this->_clientId = $clientId;
        $this->_email = $email;
        $this->_name = $name;
    }

    public function getClientId(){
        return $this->_clientId;
    }

    public function setClientId($clientId){
        $this->_clientId = $clientId;
    }

    public function getEmail(){
        return $this->_email;
    }

    public function setEmail($email){
        $this->_email = $email;
    }

    public function getName(){
        return $this->_name;
    }

    public function setName($name){
        $this->_name = $name;
    }

    /**
     * @param $method
     * @param $url
     * @param $params
     * @return bool|string
     * @throws Exception
     */

    public function callApi($method, $url, $params)
    {
        $curl = curl_init();
        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($params)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($params)
                    $url = sprintf("%s?%s", $url, http_build_query($params));
        }

        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        if(curl_errno($curl)){
            echo "CURL Error: " . curl_error($curl);
            exit;
        }
        curl_close($curl);
        return $result;
    }
}
