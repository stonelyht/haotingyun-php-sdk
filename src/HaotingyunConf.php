<?php

namespace Haotingyun\Sdk;

/**
 *
 * @author sjt
 * @since 1.0
 */
class HaotingyunConf implements Constant\HaotingyunConstant {
    
    /**
     *
     * @var array
     */
    private $conf = [];

    /**
     * to upsert $conf
     *
     * @param string $apikey            
     * @param array $conf            
     * @return \Haotingyun\Sdk\HaotingyunConf
     */
    function with($apikey, array $conf = []) {
        if (!empty($conf)) foreach ($conf as $key => $value) {
            $this->conf[$key] = $value;
        }
        
        if (isset($apikey)) $this->conf[self::HT_APIKEY] = $apikey;
        
        return $this;
    }

    /**
     * load Haotingyun.ini to initialize HaotingyunConf firstly:
     * <p>
     *
     * </p>
     *
     * @return Haotingyun\Sdk\HaotingyunConf
     */
    function init() {
        if (is_null($this->conf)) {
            $this->conf = [];
        }
        
        $yp = parse_ini_file("Haotingyun.ini");
        foreach ($yp as $key => $value) {
            $this->conf[$key] = $value;
        }
        return $this;
    }

    /**
     *
     * @param string $key            
     * @param mixed $defval            
     * @return mixed
     */
    function conf($key = null, $defval = null) {
        if (is_null($key)) return $this->conf;
        $val = $this->conf[$key];
        return is_null($val) ? $defval : $val;
    }

    /**
     *
     * @return string
     */
    function apikey() {
        return $this->conf[self::HT_APIKEY];
    }

}