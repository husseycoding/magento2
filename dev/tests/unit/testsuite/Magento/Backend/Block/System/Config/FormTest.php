<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Backend\Block\System\Config;

class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Block\System\Config\Form
     */
    protected $_object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_systemConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_formMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fieldFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlModelMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_formFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_backendConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fieldsetFactoryMock;

    /**
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function setUp()
    {
        $this->_systemConfigMock = $this->getMock(
            'Magento\Backend\Model\Config\Structure',
            [],
            [],
            '',
            false,
            false
        );

        $requestMock = $this->getMock('Magento\Framework\App\RequestInterface', [], [], '', false, false);
        $requestParams = [
            ['website', '', 'website_code'],
            ['section', '', 'section_code'],
            ['store', '', 'store_code'],
        ];
        $requestMock->expects($this->any())->method('getParam')->will($this->returnValueMap($requestParams));

        $layoutMock = $this->getMock('Magento\Framework\View\Layout', [], [], '', false, false);

        $this->_urlModelMock = $this->getMock('Magento\Backend\Model\Url', [], [], '', false, false);
        $configFactoryMock = $this->getMock(
            'Magento\Backend\Model\Config\Factory',
            [],
            [],
            '',
            false,
            false
        );
        $this->_formFactoryMock = $this->getMock(
            'Magento\Framework\Data\FormFactory',
            ['create'],
            [],
            '',
            false,
            false
        );
        $this->_fieldsetFactoryMock = $this->getMock(
            'Magento\Backend\Block\System\Config\Form\Fieldset\Factory',
            [],
            [],
            '',
            false,
            false
        );
        $this->_fieldFactoryMock = $this->getMock(
            'Magento\Backend\Block\System\Config\Form\Field\Factory',
            [],
            [],
            '',
            false,
            false
        );
        $this->_coreConfigMock = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');

        $this->_backendConfigMock = $this->getMock('Magento\Backend\Model\Config', [], [], '', false, false);

        $configFactoryMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            ['data' => ['section' => 'section_code', 'website' => 'website_code', 'store' => 'store_code']]
        )->will(
            $this->returnValue($this->_backendConfigMock)
        );

        $this->_backendConfigMock->expects(
            $this->once()
        )->method(
            'load'
        )->will(
            $this->returnValue(['section1/group1/field1' => 'some_value'])
        );

        $this->_formMock = $this->getMock(
            'Magento\Framework\Data\Form',
            ['setParent', 'setBaseUrl', 'addFieldset'],
            [],
            '',
            false,
            false
        );

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $context = $helper->getObject(
            'Magento\Backend\Block\Template\Context',
            [
                'scopeConfig' => $this->_coreConfigMock,
                'request' => $requestMock,
                'urlBuilder' => $this->_urlModelMock
            ]
        );

        $data = [
            'request' => $requestMock,
            'layout' => $layoutMock,
            'configStructure' => $this->_systemConfigMock,
            'configFactory' => $configFactoryMock,
            'formFactory' => $this->_formFactoryMock,
            'fieldsetFactory' => $this->_fieldsetFactoryMock,
            'fieldFactory' => $this->_fieldFactoryMock,
            'context' => $context,
        ];

        $this->_object = $helper->getObject('Magento\Backend\Block\System\Config\Form', $data);
        $this->_object->setData('scope_id', 1);
    }

    public function testInitFormWithoutSection()
    {
        $this->_formFactoryMock->expects($this->any())->method('create')->will($this->returnValue($this->_formMock));
        $this->_formMock->expects($this->once())->method('setParent')->with($this->_object);
        $this->_formMock->expects($this->once())->method('setBaseUrl')->with('base_url');
        $this->_urlModelMock->expects($this->any())->method('getBaseUrl')->will($this->returnValue('base_url'));

        $this->_systemConfigMock->expects(
            $this->once()
        )->method(
            'getElement'
        )->with(
            'section_code'
        )->will(
            $this->returnValue(null)
        );

        $this->_object->initForm();
        $this->assertEquals($this->_formMock, $this->_object->getForm());
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testInitGroup()
    {
        $this->_formFactoryMock->expects($this->any())->method('create')->will($this->returnValue($this->_formMock));
        $this->_formMock->expects($this->once())->method('setParent')->with($this->_object);
        $this->_formMock->expects($this->once())->method('setBaseUrl')->with('base_url');
        $this->_urlModelMock->expects($this->any())->method('getBaseUrl')->will($this->returnValue('base_url'));

        $fieldsetRendererMock = $this->getMock(
            'Magento\Backend\Block\System\Config\Form\Fieldset',
            [],
            [],
            '',
            false,
            false
        );
        $this->_fieldsetFactoryMock->expects(
            $this->once()
        )->method(
            'create'
        )->will(
            $this->returnValue($fieldsetRendererMock)
        );

        $cloneModelMock = $this->getMock(
            'Magento\Backend\Model\Config',
            ['getPrefixes'],
            [],
            '',
            false,
            false
        );

        $cloneModelMock->expects($this->once())->method('getPrefixes')->will($this->returnValue([]));

        $groupMock = $this->getMock(
            'Magento\Backend\Model\Config\Structure\Element\Group',
            [],
            [],
            '',
            false,
            false
        );
        $groupMock->expects($this->once())->method('getFrontendModel')->will($this->returnValue(false));
        $groupMock->expects($this->any())->method('getPath')->will($this->returnValue('section_id_group_id'));
        $groupMock->expects($this->once())->method('getLabel')->will($this->returnValue('label'));
        $groupMock->expects($this->once())->method('getComment')->will($this->returnValue('comment'));
        $groupMock->expects($this->once())->method('isExpanded')->will($this->returnValue(false));
        $groupMock->expects($this->once())->method('populateFieldset');
        $groupMock->expects($this->once())->method('shouldCloneFields')->will($this->returnValue(true));
        $groupMock->expects($this->once())->method('getCloneModel')->will($this->returnValue($cloneModelMock));
        $groupMock->expects($this->once())->method('getData')->will($this->returnValue('some group data'));
        $groupMock->expects(
            $this->once()
        )->method(
            'getDependencies'
        )->with(
            'store_code'
        )->will(
            $this->returnValue([])
        );

        $sectionMock = $this->getMock(
            'Magento\Backend\Model\Config\Structure\Element\Section',
            [],
            [],
            '',
            false,
            false
        );

        $sectionMock->expects($this->once())->method('isVisible')->will($this->returnValue(true));
        $sectionMock->expects($this->once())->method('getChildren')->will($this->returnValue([$groupMock]));

        $this->_systemConfigMock->expects(
            $this->once()
        )->method(
            'getElement'
        )->with(
            'section_code'
        )->will(
            $this->returnValue($sectionMock)
        );

        $formFieldsetMock = $this->getMock(
            'Magento\Framework\Data\Form\Element\Fieldset',
            [],
            [],
            '', false, false);

        $params = [
            'legend' => 'label',
            'comment' => 'comment',
            'expanded' => false,
            'group' => 'some group data',
        ];
        $this->_formMock->expects(
            $this->once()
        )->method(
            'addFieldset'
        )->with(
            'section_id_group_id',
            $params
        )->will(
            $this->returnValue($formFieldsetMock)
        );
        $this->_object->initForm();
    }

    /**
     * @dataProvider initFieldsDataProvider
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testInitFields($backendConfigValue, $configValue, $configPath, $inherit, $expectedValue)
    {
        // Parameters initialization
        $fieldsetMock = $this->getMock(
            'Magento\Framework\Data\Form\Element\Fieldset',
            [],
            [],
            '',
            false,
            false
        );
        $groupMock = $this->getMock(
            'Magento\Backend\Model\Config\Structure\Element\Group',
            [],
            [],
            '',
            false,
            false
        );
        $sectionMock = $this->getMock(
            'Magento\Backend\Model\Config\Structure\Element\Section',
            [],
            [],
            '',
            false,
            false
        );
        $fieldPrefix = 'fieldPrefix';
        $labelPrefix = 'labelPrefix';

        // Field Renderer Mock configuration
        $fieldRendererMock = $this->getMock(
            'Magento\Backend\Block\System\Config\Form\Field',
            [],
            [],
            '',
            false,
            false
        );
        $this->_fieldFactoryMock->expects(
            $this->once()
        )->method(
            'create'
        )->will(
            $this->returnValue($fieldRendererMock)
        );

        $this->_backendConfigMock->expects(
            $this->once()
        )->method(
            'extendConfig'
        )->with(
            'some/config/path',
            false,
            ['section1/group1/field1' => 'some_value']
        )->will(
            $this->returnValue($backendConfigValue)
        );

        $this->_coreConfigMock->expects(
            $this->any()
        )->method(
            'getValue'
        )->with(
            $configPath
        )->will(
            $this->returnValue($configValue)
        );

        // Field mock configuration
        $fieldMock = $this->getMock(
            'Magento\Backend\Model\Config\Structure\Element\Field',
            [],
            [],
            '',
            false,
            false
        );
        $fieldMock->expects($this->any())->method('getPath')->will($this->returnValue('section1/group1/field1'));
        $fieldMock->expects($this->any())->method('getConfigPath')->will($this->returnValue($configPath));
        $fieldMock->expects($this->any())->method('getGroupPath')->will($this->returnValue('some/config/path'));
        $fieldMock->expects($this->once())->method('getSectionId')->will($this->returnValue('some_section'));

        $fieldMock->expects($this->once())->method('hasBackendModel')->will($this->returnValue(false));
        $fieldMock->expects(
            $this->once()
        )->method(
            'getDependencies'
        )->with(
            $fieldPrefix
        )->will(
            $this->returnValue([])
        );
        $fieldMock->expects($this->any())->method('getType')->will($this->returnValue('field'));
        $fieldMock->expects($this->once())->method('getLabel')->will($this->returnValue('label'));
        $fieldMock->expects($this->once())->method('getComment')->will($this->returnValue('comment'));
        $fieldMock->expects($this->once())->method('getTooltip')->will($this->returnValue('tooltip'));
        $fieldMock->expects($this->once())->method('getHint')->will($this->returnValue('hint'));
        $fieldMock->expects($this->once())->method('getFrontendClass')->will($this->returnValue('frontClass'));
        $fieldMock->expects($this->once())->method('showInDefault')->will($this->returnValue(false));
        $fieldMock->expects($this->any())->method('showInWebsite')->will($this->returnValue(false));
        $fieldMock->expects($this->once())->method('getData')->will($this->returnValue('fieldData'));
        $fieldMock->expects($this->any())->method('getRequiredFields')->will($this->returnValue([]));
        $fieldMock->expects($this->any())->method('getRequiredGroups')->will($this->returnValue([]));

        $fields = [$fieldMock];
        $groupMock->expects($this->once())->method('getChildren')->will($this->returnValue($fields));

        $sectionMock->expects($this->once())->method('getId')->will($this->returnValue('section1'));

        $formFieldMock = $this->getMockForAbstractClass(
            'Magento\Framework\Data\Form\Element\AbstractElement',
            [],
            '',
            false,
            false,
            true,
            ['setRenderer']
        );

        $params = [
            'name' => 'groups[group1][fields][fieldPrefixfield1][value]',
            'label' => 'label',
            'comment' => 'comment',
            'tooltip' => 'tooltip',
            'hint' => 'hint',
            'value' => $expectedValue,
            'inherit' => $inherit,
            'class' => 'frontClass',
            'field_config' => 'fieldData',
            'scope' => 'stores',
            'scope_id' => 1,
            'scope_label' => '[GLOBAL]',
            'can_use_default_value' => false,
            'can_use_website_value' => false,
        ];

        $formFieldMock->expects($this->once())->method('setRenderer')->with($fieldRendererMock);

        $fieldsetMock->expects(
            $this->once()
        )->method(
            'addField'
        )->with(
            'section1_group1_field1',
            'field',
            $params
        )->will(
            $this->returnValue($formFieldMock)
        );

        $fieldMock->expects($this->once())->method('populateInput');

        $this->_object->initFields($fieldsetMock, $groupMock, $sectionMock, $fieldPrefix, $labelPrefix);
    }

    /**
     * @return array
     */
    public function initFieldsDataProvider()
    {
        return [
            [['section1/group1/field1' => 'some_value'], false, null, false, 'some_value'],
            [[], 'Config Value', 'some/config/path', true, 'Config Value']
        ];
    }
}
