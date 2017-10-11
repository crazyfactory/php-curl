<?php

namespace CrazyFactory\Curl\Tests\Unit\Helpers;

use CrazyFactory\Curl\Curl;
use CrazyFactory\Curl\Exception;

class CurlTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDefaultOptions()
    {
        $backup = ini_get('open_basedir');
        try {
            ini_set('open_basedir', '');
            $curl = new Curl();
            $result = $curl->makeOptions();

            $this->assertEquals(1, $result[CURLOPT_FOLLOWLOCATION], 'CURLOPT_FOLLOWLOCATION should be set when "open_basedir" is not set');
        }
        catch (\Exception $exception) {
        }
        finally {
            ini_set('open_basedir', $backup);
        }
    }

    public function testMakeOptions()
    {
        // # return defaults when empty
        $expected = Curl::getDefaultOptions();
        $actual = (new Curl)->makeOptions();

        $this->assertSame($expected, $actual, 'should return defaultOptions by default');

        // # merge with defaults
        $options = array(
            CURLOPT_ACCEPT_ENCODING => 'foo',
            CURLOPT_URL => 'bar',
        );
        $expected = $options + Curl::getDefaultOptions();
        $actual = (new Curl)->makeOptions($options);
        $this->assertSame($expected, $actual, 'should merge defaultOptions with passed in options');

        // # convert cookies-array and post-fields-array to strings
        $result = (new Curl)->makeOptions(array(
            CURLOPT_COOKIE => array('name' => 'john', 'age' => 30),
            CURLOPT_POSTFIELDS => array('name' => 'bob', 'age' => 25)
        ));
        $expected = 'name=john; age=30';
        $this->assertSame($expected, $result[CURLOPT_COOKIE], 'should convert cookie array to string');

        $expected = 'name=bob&age=25';
        $this->assertSame($expected, $result[CURLOPT_POSTFIELDS], 'should convert post-fields array to string');
    }

    public function testCall()
    {
        $curl = new Curl();

        try {
            $curl->call(array());
            $this->fail('Exception should have been thrown');
        }
        catch (\Exception $e) {
            $this->assertTrue($e instanceof Exception);
        }

        try {
            $curl->call(array(
                CURLOPT_URL => 'http://httpbin.org/status/404'
            ));
            $this->fail('Exception should have been thrown');
        }
        catch (Exception $e) {
            $this->assertEquals(404, $e->getHttpCode());
        }
        catch (\Exception $e) {
            $this->assertTrue($e instanceof Exception);
        }
    }
}
