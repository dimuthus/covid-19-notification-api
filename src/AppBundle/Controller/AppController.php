<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class AppController extends Controller {
	/**
	 * @Route("/")
	 */
	public function indexAction() {
		
		return new Response ( '<br />I am Healthy !!' );
	}
}
