<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Tests for \Magento\Framework\Data\Form\Element\Text
 */
namespace Magento\Framework\Data\Form\Element;

class TextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var \Magento\Framework\Data\Form\Element\Text
     */
    protected $_model;

    protected function setUp()
    {
        $factoryMock = $this->getMock('\Magento\Framework\Data\Form\Element\Factory', [], [], '', false);
        $collectionFactoryMock = $this->getMock(
            '\Magento\Framework\Data\Form\Element\CollectionFactory',
            [],
            [],
            '',
            false
        );
        $escaperMock = $this->getMock('\Magento\Framework\Escaper', [], [], '', false);
        $this->_model = new \Magento\Framework\Data\Form\Element\Text(
            $factoryMock,
            $collectionFactoryMock,
            $escaperMock
        );
        $formMock = new \Magento\Framework\Object();
        $formMock->getHtmlIdPrefix('id_prefix');
        $formMock->getHtmlIdPrefix('id_suffix');
        $this->_model->setForm($formMock);
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\Text::__construct
     */
    public function testConstruct()
    {
        $this->assertEquals('text', $this->_model->getType());
        $this->assertEquals('textfield', $this->_model->getExtType());
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\Text::getHtml
     */
    public function testGetHtml()
    {
        $html = $this->_model->getHtml();
        $this->assertContains('type="text"', $html);
        $this->assertTrue(preg_match('/class=\".*input-text.*\"/i', $html) > 0);
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\Text::getHtmlAttributes
     */
    public function testGetHtmlAttributes()
    {
        $this->assertEmpty(
            array_diff(
                [
                    'type',
                    'title',
                    'class',
                    'style',
                    'onclick',
                    'onchange',
                    'onkeyup',
                    'disabled',
                    'readonly',
                    'maxlength',
                    'tabindex',
                    'placeholder',
                ],
                $this->_model->getHtmlAttributes()
            )
        );
    }
}
