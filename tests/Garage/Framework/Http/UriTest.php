<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-5-7
 * Time: 下午8:55
 */

namespace Garage\Framework\Http;


class UriTest extends \PHPUnit_Framework_TestCase
{
    public function testParseUri() {
        $uri = new Uri('http://zsh:pwd@www.baidu.com:82/hahaha/hehehe/hihihi?query=heihei#hash=hhh');
        $this->assertEquals('http', $uri->getScheme(), 'Scheme is not valid.');
        $this->assertEquals('www.baidu.com', $uri->getHost(), 'Host is not valid.');
        $this->assertEquals('/hahaha/hehehe/hihihi', $uri->getPath(), 'Path is not valid.');
        $this->assertEquals('zsh:pwd@www.baidu.com:82', $uri->getAuthority(), 'Auth is not valid.');
        $this->assertEquals('query=heihei', $uri->getQuery());
        $this->assertEquals('hash=hhh', $uri->getFragment());
    }

    public function testWiths() {
        $uri = new Uri();
        $uri = $uri->withScheme('https');
        $this->assertEquals('https', $uri->getScheme());
        $uri = $uri->withHost('www.bing.com');
        $this->assertEquals('www.bing.com', $uri->getHost());
        $uri = $uri->withUserInfo('zsh', 'pwd');
        $this->assertEquals('zsh:pwd', $uri->getUserInfo());
        $uri = $uri->withPort(123);
        $this->assertEquals(123, $uri->getPort());
        $uri = $uri->withQuery('haha=hehe');
        $this->assertEquals('haha=hehe', $uri->getQuery());
        $uri = $uri->withFragment('heihei=hoho');
        $this->assertEquals('heihei=hoho', $uri->getFragment());
    }

    public function testToString() {
        $uri = new Uri('http://www.baidu.com');
        $this->assertEquals('http://www.baidu.com', strval($uri));
        $uri = new Uri();
        $uri = $uri->withPath('/testpath/hhh');
        $this->assertEquals('/testpath/hhh', strval($uri));
    }
}