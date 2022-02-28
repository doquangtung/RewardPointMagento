<?php

namespace LoyaltyProgram\RewardPoint\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements UpgradeDataInterface
{
	protected $_postFactory;

	public function __construct(\LoyaltyProgram\RewardPoint\Model\GoalFactory $goalFactory)
	{
		$this->_postFactory = $goalFactory;
	}

	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		if (version_compare($context->getVersion(), '1.1.6', '<')) {
			$dataGoalSignUp = [
				'goal_id'         => 1,
				'goal_name' => "Sign Up Goal",
				'goal_type' => "Sign Up",
				'goal_number' => 1,
				'goal_use' => 1
			];
			$dataGoalOrder = [
				'goal_id'         => 2,
				'goal_name' => "Order Goal",
				'goal_type' => "USD",
				'goal_number' => 1,
				'goal_use' => 1
			];
			$dataGoalVip0 = [
				'goal_id'         => 3,
				'goal_name' => "Vip 0",
				'goal_type' => "USD",
				'goal_number' => 0,
				'goal_use' => 0
			];
			$dataGoalVip1 = [
				'goal_id'         => 4,
				'goal_name' => "Vip 1",
				'goal_type' => "USD",
				'goal_number' => 100,
				'goal_use' => 0
			];
			$dataGoalVip2 = [
				'goal_id'         => 5,
				'goal_name' => "Vip 2",
				'goal_type' => "USD",
				'goal_number' => 250,
				'goal_use' => 0
			];
			$dataGoalVip3 = [
				'goal_id'         => 6,
				'goal_name' => "Vip 3",
				'goal_type' => "USD",
				'goal_number' => 500,
				'goal_use' => 0
			];
			$post = $this->_postFactory->create();
			$post->addData($dataGoalSignUp)->save();
			$post->addData($dataGoalOrder)->save();
			$post->addData($dataGoalVip0)->save();
			$post->addData($dataGoalVip1)->save();
			$post->addData($dataGoalVip2)->save();
			$post->addData($dataGoalVip3)->save();
		}
	}
}