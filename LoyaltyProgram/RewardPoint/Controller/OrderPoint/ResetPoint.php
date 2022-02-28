<?php

namespace LoyaltyProgram\RewardPoint\Controller\OrderPoint;
use Magento\Framework\Controller\ResultFactory;


class ResetPoint extends \Magento\Framework\App\Action\Action
{
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
		\Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Quote\Model\Quote\Address\Total $total)
	{
        $this->request = $request;
		$this->customerRepository = $customerRepository;
		$this->_customerSession = $customerSession;
        $this->_total = $total;

		parent::__construct($context);
	}

	public function execute()
	{
        // var_dump($point);die;
        
        $customer = $this->_customerSession->create();
		$user = $customer->getCustomer()->getId();
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $table = $resource->getTableName('using_point'); //gives table name

        $connection->delete(
            $table,
            ['entity_id = ?' => $user]
        );

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('checkout/cart/');
	}
}
