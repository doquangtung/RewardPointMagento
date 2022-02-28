<?php
namespace LoyaltyProgram\RewardPoint\Observer;

use Magento\Framework\Event\ObserverInterface;

class OrderManage implements ObserverInterface
{      
  protected $_customerRepositoryInterface;
  protected $logger;

  public function __construct(
    \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
    \Magento\Framework\App\ResourceConnection $resourceConnection ,
    \Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Psr\Log\LoggerInterface $logger
) {
    $this->_customerRepositoryInterface = $customerRepositoryInterface;
    $this->resourceConnection = $resourceConnection;
    $this->date = $date;
	$this->timezone = $timezone;
    $this->logger = $logger;
}

 public function execute(\Magento\Framework\Event\Observer $observer)
 {
    $order = $observer->getEvent()->getOrder();
    $user = $order->getCustomerId();
    $date = $this->date->gmtDate();
	$date = $this->timezone->date(new \DateTime($date))->format('y/m/d H:i:s');

    $message = "State la" . $order->getState();
    $this->logger->debug($message);

    if($order->getState() == 'canceled') {
        $connection  = $this->resourceConnection->getConnection();
        $tableOrderPoint = $connection->getTableName('order_point'); //gives table name
        $tablePoint = $connection->getTableName('current_point');
        $tableHistory = $connection->getTableName('history_point');

        $sqlGetPoint = $connection->select('*')->from($tablePoint)->where(
            $tablePoint . '.entity_id = ' . $user
        );     

        $sqlOrderPoint = $connection->select('*')->from($tableOrderPoint)->where(
            $tableOrderPoint . '.order_id = ' . $order->getEntityId());
        $resultOrderPoint = $connection->fetchAll($sqlOrderPoint);  
        if (count($resultOrderPoint) != 0) {
            foreach ($connection->fetchAll($sqlOrderPoint) as $rowOrderPoint){   
                $orderPoint = $rowOrderPoint['point'];
            }
            foreach ($connection->fetchAll($sqlGetPoint) as $rowUser){   
                $userPoint = $rowUser['point'];
            }
            $userPoint = $userPoint - $orderPoint;

            $dataPoint = [
                'point' => $userPoint,
            ];
            $connection->update($tablePoint, $dataPoint, ['entity_id = ?' => $user]);

            $dataHistory = [
                'entity_id' => $user,
                'history_point' => $orderPoint,
                'history_date' => $date,
                'history_reason' => 'Order ' . $order->getEntityId() . ' is canceled',
                'is_delete' => 0,
            ];
            $connection->insert($tableHistory, $dataHistory);

        }


    } else if($order->getState() == 'new'){
        $message = "Nhan state r";
        $this->logger->debug($message);


        $connection  = $this->resourceConnection->getConnection();
        $message = "tao connection r";
        $this->logger->debug($message);
        $tableUsingPoint = $connection->getTableName('using_point'); //gives table name
        $tableOrderPoint = $connection->getTableName('order_point'); //gives table name
        $tablePoint = $connection->getTableName('current_point');
        $tableHistory = $connection->getTableName('history_point');

        $message = "gives table name r";
        $this->logger->debug($message);

        $sqlGetPoint = $connection->select('*')->from($tablePoint)->where(
            $tablePoint . '.entity_id = ' . $user
        );     
        

        $sqlUsingPoint = $connection->select('*')->from($tableUsingPoint)->where(
            $tableUsingPoint . '.entity_id = ' . $user);
        $resultUsingPoint = $connection->fetchAll($sqlUsingPoint);  
        if (count($resultUsingPoint) != 0) {
            foreach ($connection->fetchAll($sqlUsingPoint) as $rowUsingPoint){   
                $usingPoint = $rowUsingPoint['point'];
            }
            $dataOrder = [
                        'order_id' => $order->getEntityId(),
                        'point' => $usingPoint,
                    ];
            $connection->insert($tableOrderPoint, $dataOrder);

            foreach ($connection->fetchAll($sqlGetPoint) as $rowUser){   
                $userPoint = $rowUser['point'];
            }
            $userPoint = $userPoint - $usingPoint;

            $dataPoint = [
                'point' => $userPoint,
            ];
            $connection->update($tablePoint, $dataPoint, ['entity_id = ?' => $user]);

            $dataHistory = [
                'entity_id' => $user,
                'history_point' => $usingPoint,
                'order_id' => $order->getEntityId(),
                'history_date' => $date,
                'history_reason' => 'Use point to buy Order ' . $order->getEntityId(),
                'is_delete' => 0,
            ];
            $connection->insert($tableHistory, $dataHistory);

            $connection->delete(
                $tableUsingPoint,
                ['entity_id = ?' => $user]
            );
        }
    }
    else if($order->getState() == 'complete') {
        $connection  = $this->resourceConnection->getConnection();
        $tableEarn = $connection->getTableName('earn'); //gives table name
        $tableProcess = $connection->getTableName('process');
        $tableGoal = $connection->getTableName('goal'); //gives table name
        $tablePoint = $connection->getTableName('current_point');
        $tableHistory = $connection->getTableName('history_point');
        
        //$tableVip = $connection->getTableName('current_vip'); //gives table name
        $tableAllVip = $connection->getTableName('vip'); //gives table name
        $tableSpend = $connection->getTableName('user_spend'); //gives table name
        $tableOrderPoint = $connection->getTableName('order_point'); //gives table name

        $sqlOrderPoint = $connection->select('*')->from($tableOrderPoint)->where(
            $tableOrderPoint . '.order_id = ' . $order->getEntityId());
        $resultOrderPoint = $connection->fetchAll($sqlOrderPoint);  
        $orderPointUse = 0;
        if (count($resultOrderPoint) != 0) {
            foreach ($connection->fetchAll($sqlOrderPoint) as $rowOrderPoint){   
                $orderPointUse = $rowOrderPoint['point'];
            }
        }
        
        $sqlSpend = $connection->select('*')->from($tableSpend)->where(
            $tableSpend . '.entity_id = ' . $user);
        $resultSpend = $connection->fetchAll($sqlSpend);  
        if (count($resultSpend) == 0) {
            $dataSpend = [
                    'entity_id' => $user,
                    'spend' => 0,
                ];
            $connection->insert($tableSpend, $dataSpend);
        }
        $sqlGetSpend = $connection->select('*')->from($tableSpend)->where(
            $tableSpend . '.entity_id = ' . $user);

        foreach ($connection->fetchAll($sqlGetSpend) as $rowSpend){   
                $userSpend = $rowSpend['spend'];
        }
        
        $sqlVipRate = $connection->select('*')->from($tablePoint)->join(
            $tableAllVip, 
            $tablePoint . '.vip = ' . $tableAllVip . '.vip_name'
            )->where(
                $tablePoint . '.entity_id = ' . $user);
        
        foreach ($connection->fetchAll($sqlVipRate) as $rowVipRate){   
        $rate = $rowVipRate['vip_rate'];
        }
        $orderPointUse = $orderPointUse / $rate;

        $userSpend = $userSpend + $order->getGrandTotal() + $orderPointUse;
        $dataSpend = [
            'spend' => $userSpend,
        ];
        $connection->update($tableSpend, $dataSpend, ['entity_id = ?' => $user]);
        
        $sqlGetVip = $connection->select('*')->from($tablePoint)->where(
            $tablePoint . '.entity_id = ' . $user
        );
        foreach ($connection->fetchAll($sqlGetVip) as $rowVip){   
        $userVip =  $rowVip['vip'];
        }
        $checkVip = 0;
        $sqlMaxVip = $connection->select('*')->from($tableAllVip)->join(
            $tableGoal, 
            $tableAllVip . '.goal_id = ' . $tableGoal . '.goal_id'
        )->order(array('goal.goal_number ASC'));
        foreach ($connection->fetchAll($sqlMaxVip) as $rowMaxVip) {
            $maxVip = $rowMaxVip['vip_name'];
        }
        if ($userVip != $maxVip) {
            $sqlVip = $connection->select('*')->from($tableAllVip)->join(
                $tableGoal, 
                $tableAllVip . '.goal_id = ' . $tableGoal . '.goal_id'
            );
            foreach ($connection->fetchAll($sqlVip) as $rowAllVip){   
                if ($checkVip == 1) {
                    if ($userSpend >= $rowAllVip['goal_number']) {
                        $dataNewVip = [
                            'vip' => $rowAllVip['vip_name'],
                        ];
                        $connection->update($tablePoint, $dataNewVip, ['entity_id = ?' => $user]);
                    }
                }
                if ($rowAllVip['vip_name'] == $userVip) $checkVip = 1;
            }
        }
        

        $sqlEarn = $connection->select('*')->from($tableEarn)->join(
                $tableGoal, 
                $tableEarn . '.goal_id = ' . $tableGoal . '.goal_id'
            )->where($tableEarn . '.earn_actived = 1')->order(array('earn.earn_priority ASC'));
        
        foreach ($connection->fetchAll($sqlEarn) as $row){   
        if ($row['earn_id'] == 11) {
            $orderPoint = ($order->getGrandTotal() + $orderPointUse)*$row['earn_point'];
            $sqlGetPoint = $connection->select('*')->from($tablePoint)->where(
                $tablePoint . '.entity_id = ' . $user
            );     
            foreach ($connection->fetchAll($sqlGetPoint) as $rowUser){   
            $userPoint = $rowUser['point'];
            }
            $dataPoint = [
                'point' => $userPoint + $orderPoint,
            ];
            $connection->update($tablePoint, $dataPoint, ['entity_id = ?' => $user]);
            $dataHistory = [
                'entity_id' => $user,
                'history_point' => $orderPoint,
                'earn_id' => 11,
                'history_date' => $date,
                'history_reason' => 'Get point from Order ' . $order->getEntityId(),
                'is_delete' => 0,
            ];
            $connection->insert($tableHistory, $dataHistory);
        } else if ($row['goal_type'] == "Order" || $row['goal_type'] == "USD") {
            $sqlProcess = $connection->select('*')->from($tableProcess)->where(
            $tableProcess . '.entity_id = ' . $user . ' AND ' . $tableProcess . '.earn_id = ' . $row['earn_id']);
            $result = $connection->fetchAll($sqlProcess);  
            if (count($result) == 0) {
                $dataProcess = [
                    'entity_id' => $user,
                    'earn_id' => $row['earn_id'],
                    'process_activity' => 0,
                ];
                $connection->insert($tableProcess, $dataProcess);
            }
            $sqlGetProcess = $connection->select('*')->from($tableProcess)->where(
            $tableProcess . '.entity_id = ' . $user . ' AND ' . $tableProcess . '.earn_id = ' . $row['earn_id']);
            foreach ($connection->fetchAll($sqlGetProcess) as $rowProcess){   
                $userProcess = $rowProcess['process_activity'];
                }
            if ($userProcess < $row['goal_number']){
                if ($row['goal_type'] == "Order") $type = 1;
                else $type = $order->getGrandTotal();
                if ($userProcess + $type >= $row['goal_number']){
                    $newProcess = $row['goal_number'];
                    
                    $sqlGetPoint = $connection->select('*')->from($tablePoint)->where(
                        $tablePoint . '.entity_id = ' . $user
                    );     
                    foreach ($connection->fetchAll($sqlGetPoint) as $rowUser){   
                    $userPoint = $rowUser['point'];
                    }
                    $dataPoint = [
                        'point' => $userPoint + $row['earn_point'],
                    ];
                    $connection->update($tablePoint, $dataPoint, ['entity_id = ?' => $user]);

                    $dataHistory = [
                        'entity_id' => $user,
                        'history_point' => $row['earn_point'],
                        'earn_id' => $row['earn_id'],
                        'history_date' => $date,
                        'history_reason' => 'Get point from Activity ' . $row['earn_name'],
                        'is_delete' => 0,
                    ];
                    $connection->insert($tableHistory, $dataHistory);
                } else $newProcess = $userProcess + $type;
                $dataProcess = [
                    'process_activity' => $newProcess,
                ];
                $connection->update($tableProcess, $dataProcess, ['entity_id = ?' => $user, 'earn_id = ?' => $row['earn_id']]);
                if ($row['earn_overlap'] == 0) break;
            }
            
        }
        }
    }
  }
}