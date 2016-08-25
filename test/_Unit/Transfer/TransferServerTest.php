<?php

namespace Kraken\_Unit\Transfer;

use Kraken\Ipc\Socket\SocketListener;
use Kraken\Loop\Loop;
use Kraken\Transfer\Http\Component\Router\HttpRouter;
use Kraken\Transfer\ServerComponentInterface;
use Kraken\Transfer\Socket\Component\Firewall\SocketFirewall;
use Kraken\Transfer\TransferServer;
use Kraken\Transfer\TransferServerInterface;
use Kraken\Test\TUnit;

class TransferServerTest extends TUnit
{
    /**
     * @var TransferServer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $server;

    /**
     *
     */
    public function testApiConstructor_CreatesInstance()
    {
        $server = $this->createTransferServer();

        $this->assertInstanceOf(TransferServer::class, $server);
        $this->assertInstanceOf(TransferServerInterface::class, $server);
    }

    /**
     *
     */
    public function testApiDestructor_DoesNotThrowException()
    {
        $server = $this->createTransferServer();
        unset($server);
    }

    /**
     *
     */
    public function testApiAddRoute_CallsMethodOnRouter()
    {
        $path = 'path';
        $component = $this->getMock(ServerComponentInterface::class, [], [], '', false);

        $server = $this->createTransferServer();
        $router = $this->createRouter([ 'addRoute' ]);
        $router
            ->expects($this->once())
            ->method('addRoute')
            ->with($path, $component)
            ->will($this->returnValue($router));

        $this->assertSame($router, $server->addRoute($path, $component));
    }

    /**
     *
     */
    public function testApiRemoveRoute_CallsMethodOnRouter()
    {
        $path = 'path';

        $server = $this->createTransferServer();
        $router = $this->createRouter([ 'removeRoute' ]);
        $router
            ->expects($this->once())
            ->method('removeRoute')
            ->with($path)
            ->will($this->returnValue($router));

        $this->assertSame($router, $server->removeRoute($path));
    }

    /**
     *
     */
    public function testApiStop_CallsMethodOnListener()
    {
        $server = $this->createTransferServer();
        $router = $this->createListener([ 'close' ]);
        $router
            ->expects($this->once())
            ->method('close');

        $server->stop();
    }

    /**
     *
     */
    public function testApiClose_CallsMethodOnListener()
    {
        $server = $this->createTransferServer();
        $router = $this->createListener([ 'close' ]);
        $router
            ->expects($this->once())
            ->method('close');

        $server->close();
    }

    /**
     *
     */
    public function testApiBlockAddress_CallsMethodOnFirewall()
    {
        $ip = '50.50.50.50';

        $server   = $this->createTransferServer();
        $firewall = $this->createFirewall([ 'blockAddress' ]);
        $firewall
            ->expects($this->once())
            ->method('blockAddress')
            ->with($ip);

        $this->assertSame($server, $server->blockAddress($ip));
    }

    /**
     *
     */
    public function testApiUnblockAddress_CallsMethodOnFirewall()
    {
        $ip = '50.50.50.50';

        $server   = $this->createTransferServer();
        $firewall = $this->createFirewall([ 'unblockAddress' ]);
        $firewall
            ->expects($this->once())
            ->method('unblockAddress')
            ->with($ip);

        $this->assertSame($server, $server->unblockAddress($ip));
    }

    /**
     *
     */
    public function testApiIsAddressBlocked_ReturnsFalse_WhenFirewallDoesNotExist()
    {
        $ip = '50.50.50.50';

        $server = $this->createTransferServer();

        $this->assertSame(false, $server->isAddressBlocked($ip));
    }

    /**
     *
     */
    public function testApiIsAddressBlocked_CallsMethodOnFirewall_WhenFirewallDoesExist()
    {
        $ip = '50.50.50.50';
        $result = 'result';

        $server   = $this->createTransferServer();
        $firewall = $this->createFirewall([ 'isAddressBlocked' ]);
        $firewall
            ->expects($this->once())
            ->method('isAddressBlocked')
            ->with($ip)
            ->will($this->returnValue($result));

        $this->assertSame($result, $server->isAddressBlocked($ip));
    }

    /**
     *
     */
    public function testApiGetBlockedAddresses_ReturnsEmptyArray_WhenFirewallDoesNotExist()
    {
        $server = $this->createTransferServer();
        $this->assertSame([], $server->getBlockedAddresses());
    }

    /**
     *
     */
    public function testApiGetBlockedAddresses_CallsMethodOnFirewall_WhenFirewallDoesExist()
    {
        $ips = [ '50.25.25.25', '50.50.50.50' ];

        $server   = $this->createTransferServer();
        $firewall = $this->createFirewall([ 'getBlockedAddresses' ]);
        $firewall
            ->expects($this->once())
            ->method('getBlockedAddresses')
            ->will($this->returnValue($ips));

        $this->assertSame($ips, $server->getBlockedAddresses());
    }

    /**
     *
     */
    public function testApiSetLoop_CallsMethodOnListener()
    {
        $loop = $this->getMock(Loop::class, [], [], '', false);

        $server = $this->createTransferServer();
        $router = $this->createListener([ 'setLoop' ]);
        $router
            ->expects($this->once())
            ->method('setLoop')
            ->with($loop);

        $server->setLoop($loop);
    }

    /**
     *
     */
    public function testApiGetLoop_CallsMethodOnListener()
    {
        $loop = $this->getMock(Loop::class, [], [], '', false);

        $server = $this->createTransferServer();
        $router = $this->createListener([ 'getLoop' ]);
        $router
            ->expects($this->once())
            ->method('getLoop')
            ->will($this->returnValue($loop));

        $this->assertSame($loop, $server->getLoop());
    }

    /**
     *
     */
    public function testApiIsPaused_CallsMethodOnListener()
    {
        $result = true;

        $server = $this->createTransferServer();
        $router = $this->createListener([ 'isPaused' ]);
        $router
            ->expects($this->once())
            ->method('isPaused')
            ->will($this->returnValue($result));

        $this->assertSame($result, $server->isPaused());
    }

    /**
     *
     */
    public function testApiPause_CallsMethodOnListener()
    {
        $server = $this->createTransferServer();
        $router = $this->createListener([ 'pause' ]);
        $router
            ->expects($this->once())
            ->method('pause');

        $server->pause();
    }

    /**
     *
     */
    public function testApiResume_CallsMethodOnListener()
    {
        $server = $this->createTransferServer();
        $router = $this->createListener([ 'resume' ]);
        $router
            ->expects($this->once())
            ->method('resume');

        $server->resume();
    }

    /**
     *
     */
    public function testApiCreateFirewall_CreatesFirewall()
    {
        $server = $this->createTransferServer();

        $this->assertSame(null, $this->getProtectedProperty($server, 'firewall'));
        $this->callProtectedMethod($server, 'createFirewall');
        $this->assertInstanceOf(SocketFirewall::class, $this->getProtectedProperty($server, 'firewall'));
    }

    /**
     * @param string[]|null $methods
     * @return SocketListener|\PHPUnit_Framework_MockObject_MockObject
     */
    public function createListener($methods = null)
    {
        $listener = $this->getMock(SocketListener::class, $methods, [], '', false);

        $this->setProtectedProperty($this->server, 'listener', $listener);

        return $listener;
    }

    /**
     * @param string[]|null $methods
     * @return HttpRouter|\PHPUnit_Framework_MockObject_MockObject
     */
    public function createRouter($methods = null)
    {
        $router = $this->getMock(HttpRouter::class, $methods, [], '', false);

        $this->setProtectedProperty($this->server, 'router', $router);

        return $router;
    }

    /**
     * @param string[]|null $methods
     * @return HttpRouter|\PHPUnit_Framework_MockObject_MockObject
     */
    public function createFirewall($methods = null)
    {
        $firewall = $this->getMock(SocketFirewall::class, $methods, [], '', false);

        $this->setProtectedProperty($this->server, 'firewall', $firewall);

        return $firewall;
    }

    /**
     * @param string[]|null $methods
     * @return TransferServer|\PHPUnit_Framework_MockObject_MockObject
     */
    public function createTransferServer($methods = null)
    {
        $listener = $this->getMock(SocketListener::class, [], [], '', false);

        $this->server = $this->getMock(TransferServer::class, $methods, [ $listener ]);

        return $this->server;
    }
}