<?php

namespace LoyaltyProgram\RewardPoint\Model\ResourceModel\Activity;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'earn_id';
    protected $activity;
    protected $goal;
    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init(
            'LoyaltyProgram\RewardPoint\Model\Activity',
            'LoyaltyProgram\RewardPoint\Model\ResourceModel\Activity'
        );
    }
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinLeft(
            ['secondTable'=>$this->getTable('goal')],
            'main_table.goal_id = secondTable.goal_id','*');
    }
}
