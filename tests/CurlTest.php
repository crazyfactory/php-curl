<?php

namespace CrazyFactory\Curl\Tests\Unit\Helpers;

use CrazyFactory\Curl\Curl;
use CrazyFactory\Curl\Exception;
use PHPUnit\Framework\TestCase;

class CurlTest extends TestCase
{
    public function testGetDefaultOptions()
    {
        $backup = ini_get('open_basedir');

        ini_set('open_basedir', '');

        $curl = new Curl();
        $result = $curl->makeOptions();

        $this->assertEquals(1, $result[CURLOPT_FOLLOWLOCATION], 'CURLOPT_FOLLOWLOCATION should be set when "open_basedir" is not set');

        ini_set('open_basedir', $backup);
    }

    public function testGet()
    {
        $curl = new Curl();
        $result = json_decode($curl->get('http://httpbin.org/get'), true);

        $this->assertSame('http://httpbin.org/get', $result['url']);
        $this->assertSame('httpbin.org', $result['headers']['Host']);
    }

    public function testGetOnFields()
    {
        $curl = new Curl();
        $result = json_decode($curl->get('http://httpbin.org/get', array('test' => 'value')), true);

        $this->assertSame('http://httpbin.org/get?test=value', $result['url']);
    }

    public function testPostOnFields()
    {
        $curl = new Curl();
        $result = json_decode($curl->post('http://httpbin.org/post', array('test' => 'value')), true);

        $this->assertSame('value', $result['form']['test']);
    }

    public function testMakeOptions()
    {
        $curl = new Curl();

        // # return defaults when empty
        $expected = Curl::getDefaultOptions();
        $actual = $curl->makeOptions();

        $this->assertSame($expected, $actual, 'should return defaultOptions by default');

        // # merge with defaults
        $options = array(
            CURLOPT_URL => 'bar',
        );
        $expected = $options + Curl::getDefaultOptions();
        $actual = $curl->makeOptions($options);
        $this->assertSame($expected, $actual, 'should merge defaultOptions with passed in options');

        // # convert cookies-array and post-fields-array to strings
        $result = $curl->makeOptions(array(
            CURLOPT_COOKIE => array('name' => 'john', 'age' => 30),
            CURLOPT_POSTFIELDS => array('name' => 'bob', 'age' => 25)
        ));
        $expected = 'name=john; age=30';
        $this->assertSame($expected, $result[CURLOPT_COOKIE], 'should convert cookie array to string');

        $expected = 'name=bob&age=25';
        $this->assertSame($expected, $result[CURLOPT_POSTFIELDS], 'should convert post-fields array to string');
    }

    public function callInvalidRequestProvider()
    {
        return array(
            array(array(CURLOPT_URL => 'http://httpbin.org/status/404')),
            array(array(CURLOPT_URL => 'http://httpbin.org/status/404')),
        );
    }

    /**
     * @dataProvider callInvalidRequestProvider
     * @expectedException \Exception
     */
    public function testCallOnInvalidRequest($requestOption)
    {
        $curl = new Curl();
        $curl->call($requestOption);
    }
}
