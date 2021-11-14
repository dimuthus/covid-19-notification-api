<?php

namespace CovidBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use CovidBundle\Model\Covid;

class CovidController extends Controller {
	/**
	 * @Route("/")
	 */
	public function indexAction() {
		/*
	   * try { //$databasefunctions = new DB_Functions (); $databasefunctions = new db ( 1 ); $stmt=$databasefunctions->stmt("SELECT * FROM `clinics_info` ORDER BY `id` ASC "); $row=array (); $databasefunctions->stmt_bind_assoc($stmt,$row); $j=0; $clinicinfos=array(1); while($stmt->fetch ()) { $clinicinfo [$j] =new \StdClass (); $clinicinfo [$j]->id = trim($row ['id']); $clinicinfo [$j]->clinic_id = trim($row ['clinic_id']); $clinicinfo [$j]->clinic_name = trim($row ['clinic_name']); $clinicinfo [$j]->clinic_add = trim($row ['clinic_add']); $clinicinfo [$j]->clinic_latitude = trim($row ['clinic_latitude']); $clinicinfo [$j]->clinic_longitude = trim($row ['clinic_longitude']); $j ++; } header ( 'content-type: application/json; charset=utf-8' ); // header("access-control-allow-origin: *"); echo '{"clinic":' . json_encode ( $clinicinfo ) . '}'; exit (); } catch ( Exception $e ) { header ( 'content-type: application/json; charset=utf-8' ); // header("access-control-allow-origin: *"); //$api_response_message = $databasefunctions->getAPIResponseMessage ( '500' ); echo '{"isValid":"false", "message_title":"' . $api_response_message ["message_title"] . '", "message_details":"' . $api_response_message ["message_details"] . '" }'; exit (); } $header = array ( "content-type" => "application/json", "charset" => "utf-8", "token" =>"ok go" ); // return new Response ( json_encode ( $ikad->getData () ), 200, $header );
	   */
		$header = array (
				"content-type" => "application/json",
				"charset" => "utf-8" 
		);
		$data = '{"isValid":"false"}';
		return new Response ( $data, 200, $header );
	}
	
	/**
	 * @Route("/testinsurance/{nationality}/{passport_no}")
	 */
	public function indextestinsurance($nationality, $passport_no) {
		$header = array (
				"content-type" => "application/json",
				"charset" => "utf-8" 
		);
		
		$insurancemodel = new Insurance ();
		$im = $insurancemodel->getInsuranceInfo ( $nationality, $passport_no );
		
		if ($im != false) {
			$im = json_encode ( $im );
			$data = '{"isValid":"true","insurance":' . $im . '}';
			return new Response ( $data, 200, $header );
		} else {
			$data = '{"isValid":"false"}';
			return new Response ( $data, 200, $header );
		}
	}
	
	/**
	 * @Route("/testmedical/{nationality}/{passport_no}")
	 */
	public function indextestmedical($nationality, $passport_no) {
		$header = array (
				"content-type" => "application/json",
				"charset" => "utf-8" 
		);
		
		$medicalmodel = new Insurance ();
		$im = $medicalmodel->getMedicalInfo ( $nationality, $passport_no );
		
		if ($im != false) {
			$im = json_encode ( $im );
			$data = '{"isValid":"true","medical":' . $im . '}';
			return new Response ( $data, 200, $header );
		} else {
			$data = '{"isValid":"false"}';
			return new Response ( $data, 200, $header );
		}
	}
}
