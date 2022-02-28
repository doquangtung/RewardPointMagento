<?php

namespace LoyaltyProgram\RewardPoint\Block\Adminhtml\User;
use Magento\Backend\Block\Template;

class ViewHistory extends Template
{
	protected $_coreRegistry;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    public function getUserId()
    {
        // will return 'bar'
        return $this->_coreRegistry->registry('row_data');
    }
}
