<?php
namespace HealthstatusBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use EMGS\HelperClasses\db;

class HealthstatusController extends Controller
{

    /**
     * @Route("/")
     */
    public function indexAction()
    {
        /*
         * $db = new db ( 1 ); if ($db) echo "connected\n <br />"; else echo $db->error; $hc = Encryption::say_hello ( " Inam " ); return new Response ( '<html><body>Lucky numbers: FROM Notification and ' . $hc . '</body></html>' ); // return $this->render('NotificationBundle:Default:index.html.twig');
         */
        return new Response('health status');
    }

    /**
     * @Route("/runcron")
     */
    public function runcronAction()
    {
        $t2 = microtime(true); 

//        if ($_SERVER['SERVER_NAME'] != 'localhost')
//            die('!!!!');
        $db = new db(1);
        $proxDistance = 3;
        $degree = $proxDistance / 111;
        $degree = round($degree, 6);
        echo 'DEGREE IS '.$degree;
        
        $stmt = $db->stmt("SELECT * FROM `users` WHERE `current_location_updated_at` > (now() - interval 30 minute) AND `current_health_status` in (2, 3)  ");
        $row = array();
        $db->stmt_bind_assoc($stmt, $row);
        $j = 0;
        $update = null;
        while ($stmt->fetch()) {
            $dt = microtime(true) - $t2;
            echo '<br><br>TIME lapse is '.$dt;  
            echo '<br>User red/orange with location updated in last 30 mins';print_r($row);
            // proxomity check - get potential risk users
            // if user's current_health_status has been updated to orange or red user in the last 30 mins, get location history for past 14 days
            // else get location history for past 30 mins.
            // for each orange or red user, if current_health_status has been updated in the last 30 mins get location history for past 14 days

//            echo 'HEALTH STATUS UPDATE TIME IS '.strtotime($row['health_status_updated_at']);
//            echo ' <br>7 hrs 30 mins ago '.strtotime("-8 hours -30 minutes");
            //strtotime("+{$min} minutes");
            //if ($row['health_status_updated_at'] )
            
            $interval = (strtotime($row['health_status_updated_at']) > strtotime("-8 hours -30 minutes")) ? '30 MINUTE' : '14 DAY';  

            echo ' INTERVAL IS '.$interval;
            //$interval = '30 MINUTE';
            $locStmt = $db->stmt("SELECT * FROM `user_location_history` WHERE `time` > (now() - interval ".$interval.") AND `user_id` = ".$row['id']);
            echo '<br>LOCATION QUERY '."SELECT * FROM `user_location_history` WHERE `time` > (now() - interval ".$interval.") AND `user_id` = ".$row['id'];
            $db->stmt_bind_assoc($locStmt, $locRow);
            $dt = microtime(true) - $t2; 
            $userIdsAry = [$row['id']];
            while ($locStmt->fetch()) {
                echo '<br><br>TIME lapse is '.$dt;  
                echo '<br>User location history';print_r($locRow);
                // look for all the poor green dudes within proximity
                
                $userIds = implode(',', $userIdsAry);
                $statement = "SELECT DISTINCT `user_id` FROM `user_location_history_spatial` WHERE `user_id` NOT IN (".$userIds .") AND `time` between ('".$locRow['time']."' - interval 7 MINUTE) and ('".$locRow['time']."' + interval 7 MINUTE) 
                AND `latitude` BETWEEN ".($locRow['latitude'] - $degree). " AND ".($locRow['latitude'] + $degree) .
                " AND `longitude` BETWEEN ".($locRow['longitude'] - $degree). " AND ".($locRow['longitude'] + $degree
                );
                $x1 = 101.7066227 + $proxDistance / ( 111.1 / COS(deg2rad(3.1476478)));
                $y1 = 3.1476478 + $proxDistance / 111.1;
                $x2 = 101.7066227 - $proxDistance / ( 111.1 / COS(deg2rad(3.1476478))); 
                $y2 = 3.1476478 - $proxDistance / 111.1;
                $statement2 = "SELECT DISTINCT `user_id` FROM `user_location_history_spatial` WHERE MBRContains (LineString ( 
                    Point ( ".$x1.", ".$y1." ), Point ( ".$x2.", ".$y2." )), `location` ) 
                    AND `user_id` NOT IN (".$userIds .")
                    AND `time` between ('".$locRow['time']."' - interval 7 MINUTE) and ('".$locRow['time']."' + interval 7 MINUTE) " ;

                echo 'STATEMENT IS '.$statement;
                echo '<br>STATEMENT2 IS '.$statement2;
                //die();
                $userStmt = $db->stmt($statement)   ;
                $db->stmt_bind_assoc($userStmt, $userId);
                $dt = microtime(true) - $t2; 
                echo '<br><br>TIME lapse A is '.$dt; 
                while ($userStmt->fetch()) {
                    echo '<br> USER ID ';  print_r($userId);
                    $userIdsAry[] = $userId['user_id'];     
                }
                $dt = microtime(true) - $t2; 
                echo '<br><br>TIME lapse B is '.$dt;
            }
        }


        $stmt->free_result();
        $stmt->close();
        
        $header = array(
            "content-type" => "application/json",
            "charset" => "utf-8"
        );
        // $this->updateAction();
        
        if ($update) {
            echo 'ABC'; die();
            return new Response(json_encode($update), 200, $header);
        } else {
            echo 'DEF'; die();

            return new Response("null", 200, $header);
        }
        // return $this->render('NotificationBundle:Default:index.html.twig');
        
    }

    public static function insertNotification($status_title, $remark)
    {
        $db = new db(1);
        $status_title = $db->getMysqli()->real_escape_string($status_title);
        $remark = $db->getMysqli()->real_escape_string($remark);
        
        $query = "INSERT INTO `notification`
					 (`id`, `title`, `alert`, `uri`, `created_by`, `created_at`, `updated_by`, `updated_at`)
					VALUES (NULL, '" . $status_title . "', '" . $remark . "', 'N/A', '0', CURRENT_TIMESTAMP, '0', CURRENT_TIMESTAMP)";
        $stmt = $db->stmt($query);
        $lastInsertedID = $stmt->insert_id;
        $stmt->free_result();
        $stmt->close();
        return $lastInsertedID;
    }

    public static function insertUserNotification($std_info_id, $lastInsertedNotificationID)
    {
        $db = new db(1);
        $query = "INSERT INTO `student_notification`
					 (`id`, `student_id`, `notification_id`, `notification_sent`, `notification_sent_at`, `notification_dowanloaded`, `created_at`)
					VALUES (NULL," . $std_info_id . "," . $lastInsertedNotificationID . ", 1, CURRENT_TIMESTAMP, 0,CURRENT_TIMESTAMP)";
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

    private function updateNotificationUpdate($notification_update_id)
    {
        $db = new db(1);
        $query = "UPDATE `notification_update`
	 				SET
	 				is_notified=1
					WHERE id=" . $notification_update_id;
        
        $stmt = $db->stmt($query);
        $stmt->free_result();
        $stmt->close();
        return true;
    }

    public static function send($passport_no, $notification_id, $title, $alert, $uri = "")
    {
        $push_payload = json_encode(array(
            "where" => array(
                "passport_no" => strtoupper($passport_no)
            ),
            "data" => array(
                "notification_id" => $notification_id,
                "title" => $title,
                "alert" => $alert,
                "uri" => $uri,
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
        // return $response;
    }

    /**
     * @Route("/wpnotification")
     */
    public function wpnotificationAction()
    {
        $header = array(
            "content-type" => "application/json",
            "charset" => "utf-8"
        );
        if (isset($_REQUEST['json']) && ! empty($_REQUEST['json'])) {
            $payload = json_decode($_REQUEST['json']);
            
            if (isset($payload->from) && isset($payload->title) && isset($payload->alert) && isset($payload->picture) && isset($payload->uri)) {
                return new Response($payload->title . "\n " . $payload->alert, 200, $header);
            } else {
                return new Response('nothing');
            }
        } else {
            return new Response('nothing');
        }
    }

    public static function sendWPNotification($passport_no, $notification_id, $title, $alert, $uri = "")
    {
        $push_payload = json_encode(array(
            "where" => array(
                "passport_no" => strtoupper($passport_no)
            ),
            "data" => array(
                "notification_id" => $notification_id,
                "title" => $title,
                "alert" => $alert,
                "uri" => $uri,
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
        // return $response;
    }
}

?>