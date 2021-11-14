<?php
namespace NotificationBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use EMGS\HelperClasses\db;

class NotificationController extends Controller
{

    /**
     * @Route("/")
     */
    public function indexAction()
    {
        /*
         * $db = new db ( 1 ); if ($db) echo "connected\n <br />"; else echo $db->error; $hc = Encryption::say_hello ( " Inam " ); return new Response ( '<html><body>Lucky numbers: FROM Notification and ' . $hc . '</body></html>' ); // return $this->render('NotificationBundle:Default:index.html.twig');
         */
        return new Response('helo');
    }

	
	    /**
     * @Route("/runcronstatic")
     */
	 
	public function runcronstaticAction(){
		
		
		//this quary is running every 2 day at 8 am/10 am/12 pm/4 pm -  0 8 */2 * *|0 10 */2 * *|0 12 */2 * *|0 16 */2 * *
		$db = new db(1);
		$qry="SELECT 
			  `id`,
			  `title`,
			  `alert`,
			  `uri`,
			  `to_all`,
			  `notification_time_at` ,
			  CONCAT(DATE(NOW()),' ',notification_time_at) AS run_at
			FROM
			  `notification` 
			WHERE `to_all` = 1 
			  AND `is_active` = 1 
			  AND CONCAT(DATE(NOW()),' ',notification_time_at) BETWEEN DATE_SUB(NOW(), INTERVAL 10 MINUTE) AND NOW()";
		  
		//die($qry);
		$stmt2 = $db->stmt($qry);
		$row2 = array();
		$db->stmt_bind_assoc($stmt2, $row2);
        $j = 0;
		$update[] = array();
		//$users = array("60126313308","60182227042");
		while ($stmt2->fetch()) {
			
			$update[$j] = new \StdClass();
			$update[$j]->notification_id = trim($row2['id']);
			$update[$j]->title = trim($row2['title']);
			$update[$j]->alert = trim($row2['alert']);
		    $update[$j]->notification_at=trim($row2['run_at']);
			
			//$this->sendToAll($update[$j]->notification_id, $update[$j]->title, $update[$j]->alert);
			$qryUsers="SELECT `id` FROM `users` WHERE id IN (516,545)";
			$st = $db->stmt($qryUsers);
			$rowUsers = array();
			$db->stmt_bind_assoc($st, $rowUsers);
			while ($st->fetch()) {
				$user_id=$rowUsers['id'];
			    $this->insertUserNotificationLog($user_id,$update[$j]->notification_id,$update[$j]->notification_at);
				
			}
				
			
			$j++;
			
		}
	
		$stmt2->free_result();
        $stmt2->close();
        $header = array(
            "content-type" => "application/json",
            "charset" => "utf-8"
        );
        // $this->updateAction();
        //echo json_encode($update);
        if ($update) {
            return new Response(json_encode($update), 200, $header);
        } else {
            return new Response("null", 200, $header);
        }
		
	}
	
	
	
	
    /**
     * @Route("/runcron")
     */
    public function runcronAction()
    {
		
        // if ($_SERVER['SERVER_NAME'] != 'localhost')
            // die('!!!!');
        $db = new db(1);
        
       // $stmt = $db->stmt("SELECT * FROM `new_notification` WHERE new_notification.`notification_at` < DATE_SUB(NOW(), INTERVAL 1 HOUR)  ");
        $qry="SELECT
		`new_notification`.`notification_id`
		, `new_notification`.`users_tbl_id`
		, `notification`.`title`
		, `notification`.`alert`
		, `new_notification`.`id`
		,users.`user_home_address`
		,users.`user_office_address`
		,users.`current_health_status`
		,users.`current_location_updated_at`
		,users.`close_companions_updated_at`
		,users.`mobile_number`
		,new_notification.`notification_at`
		FROM
		`new_notification`
		INNER JOIN `notification` 
        ON (`new_notification`.`notification_id` = `notification`.`id`)
        INNER JOIN users
                  ON (`new_notification`.`users_tbl_id` = `users`.`id`)
          WHERE new_notification.is_notified=0 AND new_notification.`notification_at` < DATE_SUB(NOW(), INTERVAL 2 MINUTE )";
		//die($qry);
		$stmt2 = $db->stmt($qry);
        //$row = array();
		 $row2 = array();
        //$db->stmt_bind_assoc($stmt, $row);
		$db->stmt_bind_assoc($stmt2, $row2);
        $j = 0;

        //$update = null;
		$update[] = array();
		while ($stmt2->fetch()) {
			
			$update[$j] = new \StdClass();
			$update[$j]->users_tbl_id = trim($row2['users_tbl_id']);
			$update[$j]->notification_id = trim($row2['notification_id']);
			$update[$j]->title = trim($row2['title']);
			$update[$j]->alert = trim($row2['alert']);
			$update[$j]->id= trim($row2['id']);// this is notification running id
			$update[$j]->user_home_address = trim($row2['user_home_address']);
			$update[$j]->user_office_address = trim($row2['user_office_address']);
			$update[$j]->current_health_status = trim($row2['current_health_status']);
			$update[$j]->current_location_updated_at = $row2['current_location_updated_at'];
			$update[$j]->close_companions_updated_at = trim($row2['close_companions_updated_at']);
			$update[$j]->mobile_number = trim($row2['mobile_number']);
			$update[$j]->notification_at = trim($row2['notification_at']);

			$this->updateNotificationUpdate($update[$j]->users_tbl_id,$update[$j]->id,$update[$j]->notification_id);
			$this->insertUserNotificationLog($update[$j]->users_tbl_id,$update[$j]->notification_id,$update[$j]->notification_at);
			$this->send($update[$j]->mobile_number, $update[$j]->notification_id, $update[$j]->title, $update[$j]->alert);
			$j++;
			//echo $row2['notification_id']."<br/>";
			//var_dump($update);
		}
	
	
        $stmt2->free_result();
        $stmt2->close();
        $header = array(
            "content-type" => "application/json",
            "charset" => "utf-8"
        );
        // $this->updateAction();
        //echo json_encode($update);
        if ($update) {
            return new Response(json_encode($update), 200, $header);
        } else {
            return new Response("null", 200, $header);
        }
        // return $this->render('NotificationBundle:Default:index.html.twig');
    }

    public static function insertConditionalNotification($users_tbl_id, $notification_id)
    {
		
        $db = new db(1);
        //$status_title = $db->getMysqli()->real_escape_string($status_title);
        //$remark = $db->getMysqli()->real_escape_string($remark);
		
		//set time interval for notification
		$timeInterval="INTERVAL 0 MINUTE";
		$timeInterval0Day="INTERVAL 0 MINUTE"; //"INTERVAL 0 MINUTE";
		$timeInterval1Day="INTERVAL 1440 MINUTE"; //"INTERVAL 1440 MINUTE";
		if ($notification_id==1 or $notification_id==3 or $notification_id==6 or $notification_id==7 or $notification_id==8 or $notification_id==10 or $notification_id==12 )
			$timeInterval=$timeInterval0Day;  
		else if ($notification_id==2 or $notification_id==4 or $notification_id==5 or $notification_id==9 or $notification_id==16)
		   $timeInterval=$timeInterval1Day;
	   
        $qryCheck="SELECT count(id) as cnt FROM new_notification WHERE users_tbl_id=$users_tbl_id AND notification_id=$notification_id ";
		//echo $notification_id.">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>".$qryCheck;
		$q1=$db->stmt( $qryCheck);
		$r1 = array();
		$queryIns="";
			$db->stmt_bind_assoc($q1, $r1);
			$rowcount=0;
			while ($q1->fetch()) {
				$rowcount=$r1['cnt'];
			}
			
		if ($rowcount==0){
		  $queryIns= "INSERT INTO new_notification (users_tbl_id,notification_id,notification_at) 
         (SELECT $users_tbl_id,$notification_id ,CONCAT (DATE (DATE_ADD(NOW(),$timeInterval)) ,' ',`notification_time_at`) FROM notification WHERE id=$notification_id );";
		 
			$stmt = $db->stmt($queryIns);
			$lastInsertedID = $stmt->insert_id;
			$stmt->free_result();
			$stmt->close();
			return $lastInsertedID;
		}
		
		
    }

	
    public static function insertUserNotificationLog($users_tbl_id, $notification_id,$notification_at)
    {
        $db = new db(1);
        $query = "INSERT INTO `new_notification_log`
					 (`id`, `users_tbl_id`, `notification_id`,notification_at, `is_notified`, `notified_at`)
					VALUES (NULL," . $users_tbl_id . ", $notification_id ,'$notification_at', 1, CURRENT_TIMESTAMP)";
        $stmt = $db->stmt($query);
        $lastInsertedID = $stmt->insert_id;
        $stmt->free_result();
        $stmt->close();
        return $lastInsertedID;
    }

    public static function updateUserInfo($staudent_info_id, $application_id, $student_id, $status_code, $status_created_at, $status_complete)
    {
        $db = new db(1);
        $query = "UPDATE `student_info`
	 				SET
	 				application_id='" . strtolower(trim($application_id)) . "',
					student_id='" . strtolower(trim($student_id)) . "',	
	 				status_code='" . strtolower(trim($status_code)) . "',
	 				status_created_at='" . $status_created_at . "',
	 				status_notified='1',
	 				status_complete=" . $status_complete . " WHERE id=" . $staudent_info_id;
        
        $stmt = $db->stmt($query);
        $stmt->free_result();
        $stmt->close();
        return true;
    }

    private function updateNotificationUpdate($users_tbl_id,$notification_update_id,$notification_id)
    {
         $db = new db(1);
		$is_notified=0;
		$notified_at="null";
		$updateNotifcationRepeat="";
		$q1=$db->stmt("SELECT 
		notification_time_at,
		frequancy,
		repeat_interval,
		TIME(NOW()) AS ct,
		TIME(new_notification.`notification_at`) AS nnt, 
		new_notification.`notification_at`  
		FROM notification
		INNER JOIN `new_notification` ON new_notification.`notification_id`=notification.id   
		WHERE `new_notification`.`notification_id`=$notification_id AND `new_notification`.`users_tbl_id`=$users_tbl_id ");

			$r1 = array();
			$db->stmt_bind_assoc($q1, $r1);
			$nt="";
			$nnt="";
			while ($q1->fetch()) {
				$nt=$r1['notification_time_at'];
				$nnt=$r1['nnt'];
				$ct=$r1['ct'];
				$frequancy=$r1['frequancy'];
				$repeat_interval=$r1['repeat_interval'];
			}
			if ($repeat_interval==0){
				$is_notified=1;
				$notified_at="NOW()";
			}
		switch($notification_id){
		 case 14:
		    //get next notifction time for hourly template
			if ( "21:00:00" > $ct AND $ct < "08:00:00")
				 $updateNotifcationRepeat=" ,notification_at=DATE_ADD(`notification_at`,INTERVAL 60 MINUTE)";
            else if ("00:00:00" < $ct AND $ct < "08:00:00")
				$updateNotifcationRepeat=" ,notification_at=CONCAT (DATE (notification_at),' ','$nt')";
			else if ("21:00:00" < $ct AND $ct < "00:00:00")
				$updateNotifcationRepeat=" ,notification_at=CONCAT (DATE (DATE_ADD(notification_at,INTERVAL 1440 MINUTE)) ,' ','$nt')";
           
		break;
		 case 19:
		    $updateNotifcationRepeat=" ,notification_at=DATE_ADD(NOW(),INTERVAL $repeat_interval MINUTE)";
        break;
		 case 20:
		    $updateNotifcationRepeat=" ,notification_at=DATE_ADD(NOW(),INTERVAL $repeat_interval MINUTE)";
        case 21: //this is test case for notification 14
		    $updateNotifcationRepeat=" ,notification_at=DATE_ADD(NOW(),INTERVAL $repeat_interval MINUTE)";
           
		break;
        default: 
			$updateNotifcationRepeat=" ,notification_at=DATE_ADD(notification_at,INTERVAL $repeat_interval MINUTE)";
			//$updateNotifcationRepeat=" ,notification_at=CONCAT (DATE (DATE_ADD(NOW(),INTERVAL $repeat_interval HOUR)) ,' ','$nt')";   
			
			break;
    }
		
	    $query = "UPDATE `new_notification`	SET	is_notified=$is_notified,notified_at=$notified_at $updateNotifcationRepeat	WHERE id=" .$notification_update_id;
        //die($query);
        $stmt = $db->stmt($query);
        $stmt->free_result();
        $stmt->close();
        return true;
    }
	 public static function sendToAll($notification_id, $title, $alert, $uri = "")
    {
        $push_payload = json_encode(array(
    
            "data" => array(
                "notification_id" => $notification_id,
                "title" => $title,
                "alert" => $alert,
                "uri" => "",
                "content-available" => 1,
                "badge" => 1
            ))
			);
		//die('dddd');
        $rest = curl_init();
        curl_setopt($rest, CURLOPT_URL, PARSE_URL);
        curl_setopt($rest, CURLOPT_PORT, 443);
        curl_setopt($rest, CURLOPT_POST, 1);
        curl_setopt($rest, CURLOPT_POSTFIELDS, $push_payload);
        curl_setopt($rest, CURLOPT_HTTPHEADER, array(
            "X-Parse-Application-Id: " . PARSE_APP_ID,
            "X-Parse-REST-API-Key: " . PARSE_REST_KEY,
            "Content-Type: application/json"
        ));
        
        $response = curl_exec($rest);
         return $response;
    }

    public static function send($passport_no, $notification_id, $title, $alert, $uri = "")
    {
        $push_payload = json_encode(array(
            "where" => array(
                "uuid" => strtoupper($passport_no)
            ),
            "data" => array(
                "notification_id" => $notification_id,
                "title" => $title,
                "alert" => $alert,
                "uri" => "",
                "content-available" => 1,
                "badge" => 1
            )
        ));
        $rest = curl_init();
        curl_setopt($rest, CURLOPT_URL, PARSE_URL);
        curl_setopt($rest, CURLOPT_PORT, 443);
        curl_setopt($rest, CURLOPT_POST, 1);
        curl_setopt($rest, CURLOPT_POSTFIELDS, $push_payload);
        curl_setopt($rest, CURLOPT_HTTPHEADER, array(
            "X-Parse-Application-Id: " . PARSE_APP_ID,
            "X-Parse-REST-API-Key: " . PARSE_REST_KEY,
            "Content-Type: application/json"
        ));
        
        $response = curl_exec($rest);
         return $response;
    }




}

?>
