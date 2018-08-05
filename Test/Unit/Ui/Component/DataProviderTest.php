<?php

namespace Company\Related\Ui\Component;

use Company\Related\Ui\Component\DataProvider;

use PHPUnit\Framework\TestCase;

class DataProviderTest extends TestCase
{
    protected function setUp()
    {
        $this->_uiDataProvider = $this->createMock(DataProvider::class);
        $this->_uiDataProvider->expects($this->any())->method('getData')->willReturn();
    }

    protected function tearDown()
    {
        unset($this->_uiDataProvider);
    }

    public function testGetData()
    {

    }
}
