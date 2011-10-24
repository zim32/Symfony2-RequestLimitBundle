<?php
namespace Zim32\RequestLimitBundle\Lib;

use Symfony\Component\EventDispatcher\Event;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpFoundation\Request;
use \Zim32\RequestLimitBundle\Lib\RequestLimit;

class LimitEvent extends Event {

	private $response;

	private $requestLimit;

	private $request;

	public function __construct(RequestLimit $l, Request $r){
		$this->requestLimit = $l;
		$this->request = $r;
	}

	public function getResponse(){
		return $this->response;
	}

	public function setResponse(Response $response){
		$this->response = $response;
		$this->stopPropagation();
	}

	public function hasResponse(){
		return null !== $this->response;
	}

	public function getRequestLimitService(){
		return $this->requestLimit;
	}
}
 
