<?php
namespace Zim32\RequestLimitBundle\Lib;

use \Symfony\Component\HttpKernel\Event\GetResponseEvent;
use \Symfony\Component\HttpFoundation\RequestMatcher;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\DependencyInjection\ContainerInterface;
use \Symfony\Component\HttpFoundation\Session;
use \Zim32\RequestLimitBundle\Lib\LimitEvent;


class RequestLimit {
	protected $rules = array();

	protected $session;

	protected $dispatcher;

	public function __construct(ContainerInterface $container){
		$this->session = $container->get('session');
		$this->dispatcher = $container->get('event_dispatcher');
	}

	public function addRule($path, $limit, $per, $ip){
		$this->rules[] = compact('path', 'limit', 'per', 'ip');
	}

	public function onKernelRequest(GetResponseEvent $event){
		$request = $event->getRequest();
		foreach($this->rules as $rule){
			$matcher = new RequestMatcher($rule['path'], null, null, $rule['ip']);
			if($matcher->matches($request)){
				$this->logRequest($request, $rule);
				if(!$this->checkRule($rule)){
					$e = new LimitEvent($this, $request);
					$this->dispatcher->dispatch('handle.limit', $e);
					if($e->hasResponse()){
						$response = $e->getResponse();
					}else{
						$response = new Response('',503);
					}
					$event->setResponse($response);
				}
			}
		}
	}

	protected function clearHistory(array $rule){
		$history = $this->session->get('zim32_request_limit.history', array());
		$history[$this->getRuleHash($rule)] = array();
		$this->session->set('zim32_request_limit.history', $history);
	}

	protected function logRequest(Request $request, array $rule){
		$history = $this->session->get('zim32_request_limit.history', array());
		$history[$this->getRuleHash($rule)][] = time();
		$this->session->set('zim32_request_limit.history', $history);
	}

	protected function getRuleHash(array $rule){
		return md5(serialize($rule));
	}

	protected function getHistory($rule){
		$history = $this->session->get('zim32_request_limit.history', array());
		return isset($history[$this->getRuleHash($rule)])?$history[$this->getRuleHash($rule)]:array();
	}

	protected function checkRule(array $rule){
		$history = $this->getHistory($rule);
		if(($count = count($history)) <= 1) return true;
		$start = $history[0];
		$end = $history[count($history)-1];
		$delta = $end-$start;
		if($delta == 0) return true;
		$a = $count/$delta;
		$b = $rule['limit']/$rule['per'];
		if($delta > (int)$rule['per']) $this->clearHistory($rule);
		return ($a > $b)?false:true;
	}
}