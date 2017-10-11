<?php

namespace CrazyFactory\Curl;

class Exception extends \Exception
{
    /** @var array */
    protected $curlOptions;

    /** @var array */
    protected $curlInfo;

    /** @var string */
    protected $curlError;

    /**
     * CurlException constructor.
     *
     * @param string $curlError
     * @param array  $curlOptions
     * @param array  $curlInfo
     */
    public function __construct($curlError = '', array $curlOptions = [], array $curlInfo = [])
    {
        $this->curlError   = (string) $curlError;
        $this->curlOptions = $curlOptions;
        $this->curlInfo    = $curlInfo;

        $code = $this->getHttpCode();

        parent::__construct("[{$code}] {$curlError} ({$this->getUrl()})", $code);
    }

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return isset($this->curlInfo['http_code'])
            ? $this->curlInfo['http_code']
            : -1;
    }

    /**
     * @return string|null
     */
    public function getUrl()
    {
        return isset($curlOptions[CURLOPT_URL])
            ? $curlOptions[CURLOPT_URL]
            : null;
    }

    /**
     * @return string
     */
    public function getCurlError()
    {
        return $this->curlError;
    }

    /**
     * Access members using "CURLINFO_" constants
     *
     * @return array
     */
    public function getCurlInfo()
    {
        return $this->curlInfo;
    }

    /**
     * Access members using "CURLOPT_" constants
     *
     * @return array[]
     */
    public function getCurlOptions()
    {
        return $this->curlOptions;
    }
}
