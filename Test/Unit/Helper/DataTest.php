<?php

namespace Company\Related\Test\Helper;

use PHPUnit\Framework\TestCase;

use Magento\Framework\App\Helper\Context;

use Company\Related\Helper\Data;

/**
 * Class DataTest
 * @package Company\Related\Test\Helper
 */
class DataTest extends TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Config
     */
    protected $_data;


    protected function setUp()
    {
        $context = $this->createMock(Context::class);

        $this->_data = new Data($context);
    }

    public function testGetIdsArray()
    {
        $arr = $this->getTestData();
        $field = 'testField';

        $result = $this->_data->getIdsArray($arr, $field);

        $this->assertEquals($this->getTestDataResult(), $result);
    }

    public function getTestData()
    {
        return [
            0 => ['testField' => 3, 'custom_field' => 4],
            1 => ['testField' => 4],
            2 => ['testField' => 6],
            3 => ['testField' => 8]
        ];
    }

    public function getTestDataResult()
    {
        return [
          0 => 3,
          1 => 4,
          2 => 6,
          3 => 8
        ];
    }

}
