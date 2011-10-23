<?php

namespace Zim32\RequestLimitBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('Zim32RequestLimitBundle:Default:index.html.twig', array('name' => $name));
    }
}
