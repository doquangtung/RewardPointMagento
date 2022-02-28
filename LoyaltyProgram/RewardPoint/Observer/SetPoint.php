<?php
namespace LoyaltyProgram\RewardPoint\Observer;

use Magento\Framework\Event\ObserverInterface;

class SetPoint implements ObserverInterface
{      
  protected $_customerRepositoryInterface;

  public function __construct(
    \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
    \Magento\Framework\App\ResourceConnection $resourceConnection ,
    \Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
) {
    $this->_customerRepositoryInterface = $customerRepositoryInterface;
    $this->resourceConnection = $resourceConnection;
    $this->date = $date;
		$this->timezone = $timezone;
}

 public function execute(\Magento\Framework\Event\Observer $observer)
 {
    $email = $observer->getEvent()->getAccountController()->getRequest()->getParam('referrer');
    $customerObject = $observer->getEvent()->getCustomer();
    $customer = $customerObject->getId();
    $name = $customerObject->getFirstName() . " " . $customerObject->getLastname();

    $date = $this->date->gmtDate();
		$date = $this->timezone->date(new \DateTime($date))->format('y/m/d H:i:s');

    $connection  = $this->resourceConnection->getConnection();
    // $tableVip = $connection->getTableName('current_vip');

    // $dataVip = [
    //     'entity_id' => $customer,
    //     'vip' => 'Vip 0',
    // ];
    // $connection->insert($tableVip, $dataVip);
   
    $tableEarn = $connection->getTableName('earn'); //gives table name

    $sqlEarn = $connection->select('*')->from($tableEarn)->where(
        $tableEarn . '.earn_name = \'Sign Up Point\'');
    
    foreach ($connection->fetchAll($sqlEarn) as $row){   
    $signUpPoint =  $row['earn_point'];
    }

    $tablePoint = $connection->getTableName('current_point');

    $dataPoint = [
        'entity_id' => $customer,
        'entity_name' => $name,
        'point' => $signUpPoint,
        'vip' => 'Vip 0',
    ];
    $connection->insert($tablePoint, $dataPoint);

    $tableHistory = $connection->getTableName('history_point');

    $dataHistory = [
        'history_id' => 0,
        'entity_id' => $customer,
        'history_point' => $signUpPoint,
        'earn_id' => 10,
        'history_date' => $date,
        'history_reason' => 'Get point from sign up',
        'is_delete' => 0,
    ];
    $connection->insert($tableHistory, $dataHistory);
    
    $tableCustomer = $connection->getTableName('customer_entity');
    $sqlGetEmail = $connection->select('*')->from($tableCustomer)->where(
      $tableCustomer . '.email = \'' . $email . '\'');
    $resultEmail = $connection->fetchAll($sqlGetEmail);  
    if (count($resultEmail) > 0) {
      $sqlUserEmail = $connection->select('*')->from($tableCustomer)->where(
        $tableCustomer . '.entity_id = ' . $customer);
      foreach ($connection->fetchAll($sqlUserEmail) as $rowUserEmail){   
          $userEmail= $rowUserEmail['email'];
        }
      foreach ($connection->fetchAll($sqlGetEmail) as $rowEmail){   
        $referrerID = $rowEmail['entity_id'];
      }
      $sqlReferrerPoint = $connection->select('*')->from($tablePoint)->where(
        $tablePoint . '.entity_id = ' . $referrerID
      );
      foreach ($connection->fetchAll($sqlReferrerPoint) as $rowReferrerPoint){   
      $referrerPoint =  $rowReferrerPoint['point'];
      }
      $dataPointReferrer = [
        'point' => $referrerPoint + $signUpPoint/2,
    ];
      $connection->update($tablePoint, $dataPointReferrer, ['entity_id = ?' => $referrerID]);
      $dataHistoryReferrer = [
        'entity_id' => $referrerID,
        'history_point' => $signUpPoint/2,
        'earn_id' => 10,
        'history_date' => $date,
        'history_reason' => 'Invite ' . $userEmail,
        'is_delete' => 0,
      ];
      $connection->insert($tableHistory, $dataHistoryReferrer);
    }
  }
}