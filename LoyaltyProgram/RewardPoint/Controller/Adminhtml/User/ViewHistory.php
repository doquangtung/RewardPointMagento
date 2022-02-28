<?php

namespace LoyaltyProgram\RewardPoint\Controller\Adminhtml\User;

use Magento\Framework\Controller\ResultFactory;

class ViewHistory extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Mapped Grid List page.
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $rowId = (int) $this->getRequest()->getParam('entity_id');
        //var_dump($rowId);die;
        $this->coreRegistry->register('row_data', $rowId);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = __('History Custumer ID ').$rowId;
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

    // protected function _isAllowed()
    // {
    //     return $this->_authorization->isAllowed('LoyaltyProgram_RewardPoint::Add_row');
    // }
}
