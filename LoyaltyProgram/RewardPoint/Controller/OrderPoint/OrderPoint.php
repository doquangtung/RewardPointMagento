<?php

namespace LoyaltyProgram\RewardPoint\Controller\OrderPoint;
use Magento\Framework\Controller\ResultFactory;


class OrderPoint extends \Magento\Framework\App\Action\Action
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
        $point = $this->getRequest()->getPost("orderPoint");
        // var_dump($point);die;
        
        $customer = $this->_customerSession->create();
		$user = $customer->getCustomer()->getId();
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $table = $resource->getTableName('using_point'); //gives table name

        $sql = $connection->select('*')->from($table)->where(
            $table . '.entity_id = ' . $user
        );

        $resultUsingPoint = $connection->fetchAll($sql);

        if (count($resultUsingPoint) != 0) {
            $dataPoint = [
                'point' => $point,
            ];
            $connection->update($table, $dataPoint, ['entity_id = ?' => $user]);

        } else {
            $data = [
                'entity_id' => $user,
                'point' => $point,
            ];
            $connection->insert($table, $data);
        } 

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('checkout/cart/');
	}
}
