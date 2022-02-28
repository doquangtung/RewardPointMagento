<?php

namespace LoyaltyProgram\RewardPoint\Model\Total;

class Custom extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal {

    public function collect(
            \Magento\Quote\Model\Quote $quote,
            \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
            \Magento\Quote\Model\Quote\Address\Total $total
        ) {
            parent::collect($quote, $shippingAssignment, $total);

            
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();  
            $customerSession = $objectManager->get('Magento\Customer\Model\Session');  
            $customerId = $customerSession->getCustomer()->getId();//get id of customer

            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $table = $resource->getTableName('using_point'); //gives table name


            $sql = $connection->select('*')->from($table)->where(
                $table . '.entity_id = ' . $customerId
            );

            $resultUsingPoint = $connection->fetchAll($sql);  
            if (count($resultUsingPoint) != 0) {
                foreach ($connection->fetchAll($sql) as $row){   
                    $usingPoint = $row['point'];
                }
    
                $tableVip = $resource->getTableName('current_point'); //gives table name
                $tableAllVip = $resource->getTableName('vip'); //gives table name
    
                $sqlVip = $connection->select('*')->from($tableVip)->join(
                       $tableAllVip, 
                       $tableVip . '.vip = ' . $tableAllVip . '.vip_name'
                   )->where(
                    $tableVip . '.entity_id = ' . $customerId
                );
                
                foreach ($connection->fetchAll($sqlVip) as $rowVip){   
                $rate = $rowVip['vip_rate'];
                }
                
               $point = $usingPoint/$rate;

            } else $point = 0;
            
           $total->addTotalAmount($this->getCode(), -$point);
           $total->addBaseTotalAmount($this->getCode(), -$point);
           $quote->setDiscount($point);
    
          return $this;
        }
     
        public function fetch(
            \Magento\Quote\Model\Quote $quote,
            \Magento\Quote\Model\Quote\Address\Total $total
        ) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();  
            $customerSession = $objectManager->get('Magento\Customer\Model\Session');  
            $customerId = $customerSession->getCustomer()->getId();//get id of customer

            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $table = $resource->getTableName('using_point'); //gives table name


            $sql = $connection->select('*')->from($table)->where(
                $table . '.entity_id = ' . $customerId
            );

            $resultUsingPoint = $connection->fetchAll($sql);  
            if (count($resultUsingPoint) != 0) {
                foreach ($connection->fetchAll($sql) as $row){   
                    $usingPoint = $row['point'];
                }
    
                $tableVip = $resource->getTableName('current_point'); //gives table name
                $tableAllVip = $resource->getTableName('vip'); //gives table name
    
                $sqlVip = $connection->select('*')->from($tableVip)->join(
                       $tableAllVip, 
                       $tableVip . '.vip = ' . $tableAllVip . '.vip_name'
                   )->where(
                    $tableVip . '.entity_id = ' . $customerId
                );
                
                foreach ($connection->fetchAll($sqlVip) as $rowVip){   
                $rate = $rowVip['vip_rate'];
                }
                
                $point = $usingPoint/$rate;

            } else $point = 0;
            return [
                'code' => $this->getCode(),
                'title' => $this->getLabel(),
                'value' => -$point  //You can change the reduced amount, or replace it with your own variable
            ];
            }
}