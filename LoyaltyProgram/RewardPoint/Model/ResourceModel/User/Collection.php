<?php

namespace LoyaltyProgram\RewardPoint\Model\ResourceModel\User;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';
    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init(
            'LoyaltyProgram\RewardPoint\Model\User',
            'LoyaltyProgram\RewardPoint\Model\ResourceModel\User'
        );
    }
}
