<?php

namespace apiassignment\statistics;

use apiassignment\supermetrics\SuperMetrics;
use Exception;

/**
 * Class Statistics
 * @package apiassignment
 */
class Statistics
{
    private $_apiClass;
    private $_apiToken;

    /**
     * Statistics constructor.
     * @param SuperMetrics $supermetric
     */
    public function __construct(SuperMetrics $supermetric) {
        $this->_apiClass = $supermetric;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function init() {
        $clientId = $this->_apiClass->getClientId();
        $email = $this->_apiClass->getEmail();
        $name = $this->_apiClass->getName();
        $this->_apiToken = $this->registerToken($clientId, $email, $name);
        return $this->_apiToken;
    }


    /**
     * @param $clientId
     * @param $email
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public function registerToken($clientId, $email, $name='')
    {
        $params = array();
        $params['client_id'] = $clientId;
        $params['email'] = $email;
        $params['name'] = $name;

        $token = $this->_apiClass->callApi("POST", "https://api.supermetrics.com/assignment/register", $params);
        $resArr = json_decode($token);
        if(isset($resArr->data)){
            return $resArr->data->sl_token;
        }else{
            print_r($resArr->error);
            exit;
        }
    }


    /**
     * @param string $page
     * @return mixed
     * @throws Exception
     */
    public function listStatistics($page="") {
        $params = array();
        $params['sl_token'] = $this->_apiToken;
        if($page) {
            $params['page'] = $page;
        }
        $posts = json_decode($this->_apiClass->callApi("GET", "https://api.supermetrics.com/assignment/posts", $params));

        if(isset($posts->data)){
            return $posts;
        }else{
            print $posts;
            exit;
        }
    }
}
