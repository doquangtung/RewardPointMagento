<?php

namespace LoyaltyProgram\RewardPoint\Controller\Adminhtml\Activity;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use LoyaltyProgram\RewardPoint\Model\ResourceModel\Activity\CollectionFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Massactions filter.
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Context           $context
     * @param Filter            $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {

        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $recordDeleted = 0;
        foreach ($collection->getItems() as $record) {
            if ($record->getId() == 10 || $record->getId() == 11){
                if ($record->getId() == 10) $this->messageManager->addError(__("You can't delete activity Sign Up Point"));
                else $this->messageManager->addError(__("You can't delete activity Order Point"));
                return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('rewardpoint/Activity/Index');
            }
        }
        foreach ($collection->getItems() as $record) {
            $record->setId($record->getId());
            $record->delete();
            $recordDeleted++;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $recordDeleted));

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('rewardpoint/Activity/Index');
    }

    /**
     * Check Category Map recode delete Permission.
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('LoyaltyProgram_RewardPoint::row_data_delete');
    }
}
