<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Catalog\Model\Indexer\Product\Eav\Plugin;

class AttributeSetTest extends \PHPUnit_Framework_TestCase
{
    public function testAroundSave()
    {
        $eavProcessorMock = $this->getMockBuilder('Magento\Catalog\Model\Indexer\Product\Eav\Processor')
            ->disableOriginalConstructor()
            ->getMock();
        $eavProcessorMock->expects($this->once())
            ->method('markIndexerAsInvalid');

        $filter = $this->getMockBuilder(
            'Magento\Catalog\Model\Indexer\Product\Eav\Plugin\AttributeSet\IndexableAttributeFilter'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $filter->expects($this->at(0))
            ->method('filter')
            ->will($this->returnValue([1, 2, 3]));
        $filter->expects($this->at(1))
            ->method('filter')
            ->will($this->returnValue([1, 2]));

        $subjectMock = $this->getMockBuilder('Magento\Eav\Model\Entity\Attribute\Set')
            ->disableOriginalConstructor()
            ->getMock();
        $subjectMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(11));

        $model = new \Magento\Catalog\Model\Indexer\Product\Eav\Plugin\AttributeSet(
            $eavProcessorMock,
            $filter
        );

        $closure  = function () use ($subjectMock) {
            return $subjectMock;
        };

        $this->assertEquals(
            $subjectMock,
            $model->aroundSave($subjectMock, $closure)
        );
    }
}
