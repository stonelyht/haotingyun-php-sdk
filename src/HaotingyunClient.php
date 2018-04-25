<?php

namespace Haotingyun\Sdk;

use Haotingyun\Sdk\Api\VideoSmsApi;

/**
 *
 * @author sjt
 * @since 1.0
 */
class HaotingyunClient implements Constant\HaotingyunConstant {
    use HaotingyunGuzzle;

    /**
     *
     * @var ApiFactory
     */
    private $api;

    /**
     *
     * @var HaotingyunConf
     */
    private $conf;

    function __construct() {
        $this->api = new Api\ApiFactory($this);
        $this->conf = new HaotingyunConf();
    }

    /**
     * Initialize/Create HaotingyunClient
     *
     * @param string $apikey
     * @param array $conf
     * @return \Haotingyun\SDK\HaotingyunClient
     */
    static function create($apikey, array $conf = []) {
        $clnt = new HaotingyunClient();
        $clnt->conf->init()->with($apikey, $conf);
        $clnt->initHttp($clnt->conf); // HaotingyunGuzzle->initHttp
        return $clnt;
    }

    /**
     *
     * @param string $name
     * @return \Haotingyun\Sdk\Api\HaotingyunApi
     */
    private function api($name) {
        return $this->api->api($name);
    }

    /**
     *
     * @return SmsApi
     */
    function sms() {
        return $this->api(Api\SmsApi::NAME);
    }

    /**
     *
     * @return VideoSmsApi
     */
    function vsms() {
        return $this->api(Api\VideoSmsApi::NAME);
    }

    /**
     *
     * @return UserApi
     */
    function user() {
        return $this->api(Api\UserApi::NAME);
    }

    /**
     *
     * @return VoiceApi
     */
    function voice() {
        return $this->api(Api\VoiceApi::NAME);
    }

    /**
     *
     * @return SignApi
     */
    function sign() {
        return $this->api(Api\SignApi::NAME);
    }

    /**
     *
     * @return TplApi
     */
    function tpl() {
        return $this->api(Api\TplApi::NAME);
    }

    /**
     *
     * @return FlowApi
     */
    function flow() {
        return $this->api(Api\FlowApi::NAME);
    }

    function conf($key = null) {
        return is_null($key) ? $this->conf : $this->conf->conf($key);
    }

    function apikey() {
        return $this->conf->apikey();
    }

    function __destruct() {
        // print "Destroying $this\n";
    }

    function __toString() {
        return "HaotingyunClient-{$this->apikey()}";
    }

}

