<?php

namespace Company\Related\Test\Helper;

use PHPUnit\Framework\TestCase;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;

use Company\Related\Helper\Config;

/**
 * Class ConfigTest
 * @package Company\Related\Test\Helper
 */
class ConfigTest extends TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Config
     */
    protected $_config;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Config
     */
    protected $_scopeConfigMock;

    public function testGetRelatedProductQty()
    {
        $this->_scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->willReturn(false);

        $result = $this->_config->getRelatedProductQty();

        $this->assertEquals(false, $result);
    }

    /**
     * set vars
     */
    protected function setUp()
    {
        $this->_scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $context = $this->createMock(Context::class);
        $context->expects($this->once())
            ->method('getScopeConfig')
            ->willReturn($this->_scopeConfigMock);

        $this->_config = new Config($context);
    }
}
