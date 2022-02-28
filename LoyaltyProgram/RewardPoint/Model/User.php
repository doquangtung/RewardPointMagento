<?php

namespace LoyaltyProgram\RewardPoint\Model;


class User extends \Magento\Framework\Model\AbstractModel {
    const CACHE_TAG = 'current_point';

    protected $_cacheTag = 'current_point';

    protected $_eventPrefix = 'current_point';

    protected function _construct()
    {
        $this->_init('LoyaltyProgram\RewardPoint\Model\ResourceModel\User');
    }
}
