<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Magefan\Blog\Block\Adminhtml\Post\Edit\Tab;

/**
 * Admin blog post edit form design tab
 */
class Design extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Framework\View\Design\Theme\LabelFactory
     */
    protected $_labelFactory;

    /**
     * @var \Magento\Theme\Model\Layout\Source\Layout
     */
    protected $_pageLayout;

    /**
     * @var \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface
     */
    protected $pageLayoutBuilder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Theme\Model\Layout\Source\Layout $pageLayout
     * @param \Magento\Framework\View\Design\Theme\LabelFactory $labelFactory
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Theme\Model\Layout\Source\Layout $pageLayout,
        \Magento\Framework\View\Design\Theme\LabelFactory $labelFactory,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        array $data = []
    ) {
        $this->pageLayoutBuilder = $pageLayoutBuilder;
        $this->_labelFactory = $labelFactory;
        $this->_pageLayout = $pageLayout;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form tab configuration
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setShowGlobalIcon(true);
    }

    /**
     * Initialise form fields
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /*
         * Checking if user have permissions to save information
         */
        $isElementDisabled = !$this->_isAllowedAction('Magefan_Blog::post');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(['data' => ['html_id_prefix' => 'post_']]);

        $model = $this->_coreRegistry->registry('current_model');

        $layoutFieldset = $form->addFieldset(
            'layout_fieldset',
            ['legend' => __('Page Layout'), 'class' => 'fieldset-wide', 'disabled' => $isElementDisabled]
        );

        $layoutFieldset->addField(
            'page_layout',
            'select',
            [
                'name' => 'post[page_layout]',
                'label' => __('Layout'),
                'values' => $this->pageLayoutBuilder->getPageLayoutsConfig()->toOptionArray(true),
                'disabled' => $isElementDisabled
            ]
        );
        if (!$model->getId()) {
            $model->setRootTemplate($this->_pageLayout->getDefaultValue());
        }

        $layoutFieldset->addField(
            'layout_update_xml',
            'textarea',
            [
                'name' => 'post[layout_update_xml]',
                'label' => __('Layout Update XML'),
                'style' => 'height:24em;',
                'disabled' => $isElementDisabled
            ]
        );

        $designFieldset = $form->addFieldset(
            'design_fieldset',
            ['legend' => __('Custom Design'), 'class' => 'fieldset-wide', 'disabled' => $isElementDisabled]
        );

        $dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::SHORT
        );

        $designFieldset->addField(
            'custom_theme_from',
            'date',
            [
                'name' => 'post[custom_theme_from]',
                'label' => __('Custom Design From'),
                'date_format' => $dateFormat,
                'disabled' => $isElementDisabled,
                'class' => 'validate-date validate-date-range date-range-custom_theme-from'
            ]
        );

        $designFieldset->addField(
            'custom_theme_to',
            'date',
            [
                'name' => 'post[custom_theme_to]',
                'label' => __('Custom Design To'),
                'date_format' => $dateFormat,
                'disabled' => $isElementDisabled,
                'class' => 'validate-date validate-date-range date-range-custom_theme-to'
            ]
        );

        $options = $this->_labelFactory->create()->getLabelsCollection(__('-- Please Select --'));
        $designFieldset->addField(
            'custom_theme',
            'select',
            [
                'name' => 'post[custom_theme]',
                'label' => __('Custom Theme'),
                'values' => $options,
                'disabled' => $isElementDisabled
            ]
        );

        $designFieldset->addField(
            'custom_layout',
            'select',
            [
                'name' => 'post[custom_layout]',
                'label' => __('Custom Layout'),
                'values' => $this->pageLayoutBuilder->getPageLayoutsConfig()->toOptionArray(true),
                'disabled' => $isElementDisabled
            ]
        );

        $designFieldset->addField(
            'custom_layout_update_xml',
            'textarea',
            [
                'name' => 'post[custom_layout_update_xml]',
                'label' => __('Custom Layout Update XML'),
                'style' => 'height:24em;',
                'disabled' => $isElementDisabled
            ]
        );

        $this->_eventManager->dispatch('magefan_blog_post_edit_tab_design_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Design');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Design');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
