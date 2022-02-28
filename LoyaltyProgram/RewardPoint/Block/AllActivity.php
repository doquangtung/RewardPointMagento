<?php
namespace LoyaltyProgram\RewardPoint\Block;

class AllActivity extends \Magento\Framework\View\Element\Template
{
	public function __construct(
		\Magento\Framework\App\Request\Http $request,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
		\Magento\Customer\Model\SessionFactory $customerSession,
		\Magento\Framework\View\Element\Template\Context $context)
	{
		$this->request = $request;
		$this->customerRepository = $customerRepository;
		$this->_customerSession = $customerSession;
		parent::__construct($context);
	}

	public function sayHello()
	{
		return __('Hello World');
	}
	public function getCustomerId(){
		$customer = $this->_customerSession->create();
		return $customer->getCustomer()->getId();
	}
}
