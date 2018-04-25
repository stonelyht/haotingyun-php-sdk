<?php

namespace Haotingyun\Sdk\Api;

use Haotingyun\Sdk\Constant\Code;
use Haotingyun\Sdk\Constant\HaotingyunConstant;
use Haotingyun\Sdk\Model\Result;
use Haotingyun\Sdk\HaotingyunClient;

/**
 *
 * @author sjt
 * @since 1.0
 */
abstract class HaotingyunApi implements HaotingyunApiResult, HaotingyunConstant {

    /**
     *
     * @var HaotingyunClient
     */
    private $clnt;

    /**
     *
     * @var string
     */
    private $host;

    /**
     *
     * @var string
     */
    private $version;

    /**
     *
     * @var string
     */
    private $path;

    /**
     *
     * @var string
     */
    private $apikey;

    /**
     *
     * @var string
     */
    private $charset;

    /**
     *
     * @param HaotingyunClient $client
     */
    function init(HaotingyunClient $clnt) {
        if (is_null($clnt))
            return;
        $this->clnt = $clnt;
        $this->apikey = $clnt->apikey();
        $this->version = $clnt->conf(self::HT_VERSION, self::VERSION_V2);
        $this->charset = $clnt->conf(self::HTTP_CHARSET, self::HTTP_CHARSET_DEFAULT);
    }

    /**
     *
     * @return string
     */
    abstract function name();

    /**
     *
     * @param HaotingyunClient $client
     * @return \Haotingyun\Sdk\HaotingyunClient|\Haotingyun\Sdk\Api\HaotingyunApi
     */
    function client(HaotingyunClient $clnt = null) {
        if (is_null($clnt))
            return $this->clnt;

        $this->clnt = $clnt;
        return $this;
    }

    /**
     *
     * @param string $host
     * @return string|\Haotingyun\Sdk\Api\HaotingyunApi
     */
    function host($host = null) {
        if (is_null($host))
            return $this->host;

        $this->host = $host;
        return $this;
    }

    /**
     *
     * @param string $version
     * @return \Haotingyun\Sdk\HaotingyunConf|\Haotingyun\Sdk\Api\HaotingyunApi
     */
    function version($version = null) {
        if (is_null($version))
            return $this->version;

        $this->version = $version;
        return $this;
    }

    /**
     *
     * @param string $path
     * @return \Haotingyun\Sdk\Api\HaotingyunApi | string
     */
    function path($path = null) {
        if (is_null($path))
            return $this->path;

        $this->path = $path;
        return $this;
    }

    /**
     *
     * @param string $apikey
     * @return string|\Haotingyun\Sdk\Api\HaotingyunApi
     */
    function apikey($apikey = null) {
        if (is_null($apikey))
            return $this->apikey;

        $this->apikey = $apikey;
        return $this;
    }

    /**
     *
     * @param string $charset
     * @return string|\Haotingyun\Sdk\Api\HaotingyunApi
     */
    function charset($charset = null) {
        if (is_null($charset))
            return $this->charset;

        $this->charset = charset;
        return $this;
    }

    /**
     *
     * @return string
     */
    function uri() {
        return "{$this->host}/{$this->version}/{$this->name()}/{$this->path}";
    }

    /**
     *
     * @param array $param
     * @param ResultHandler $h
     * @param Result $r
     * @param array|null $headers
     * @return \Haotingyun\Sdk\Model\Result
     */
    function post(array &$param, ResultHandler $h = null, Result $r = null, array &$headers = null) {
        try {
            $rsp = $this->clnt->post($this->uri(), $param, $this->charset(), $headers);
            return $this->result($rsp, $h, $r);
        } catch (\Exception $e) {
            return $h->catchExceptoin($e, $r);
        }
    }

    function result(array $rsp, ResultHandler $h = null, Result $r = null) {
        // if (is_null($h)) { TODO
        // $h = // default handler
        // }
        if (is_null($r)) {
            $r = new Result();
        }

        $code = $this->code($rsp, $this->version);
        return $code == Code::OK ? $h->succ($code, $rsp, $r) : $h->fail($code, $rsp, $r);
    }

    function code(array &$rsp, $version = HaotingyunConstant::VERSION_V2) {
        if (is_null($rsp))
            return Code::OK;

        $code = Code::UNKNOWN_EXCEPTION;
        if (is_null($version)) {
            $version = self::VERSION_V2;
        }
        if (isset($rsp)) {
            switch ($version) {
                case self::VERSION_V1:
                    $code = array_key_exists(self::CODE, $rsp) ? (int)$rsp[self::CODE] : Code::UNKNOWN_EXCEPTION;
                    break;
                case self::VERSION_V2:
                    $code = array_key_exists(self::CODE, $rsp) ? (int)$rsp[self::CODE] : Code::OK;
                    break;
            }
        }
        return $code;
    }

    /**
     *
     * @param array $param
     * @param array $must
     * @param Result $r
     * @return Result
     */
    function verifyParam(array &$param, array &$must, Result $r = null) {
        if (is_null($r)) {
            $r = new Result();
        }
        if (!array_key_exists(self::APIKEY, $param)) {
            $param[self::APIKEY] = $this->apikey;
        }
        if (isset($must)) {
            foreach ($must as $p) {
                if (!array_key_exists($p, $param)) {
                    $r->code(Code::ARGUMENT_MISSING)->detail($p);
                    break;
                }
            }
        }
        return $r;
    }

}