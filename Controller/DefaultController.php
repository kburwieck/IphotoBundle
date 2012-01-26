<?php

namespace Burwieck\IphotoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
	Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
	Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
	public function indexAction()
	{
		$em = $this->getDoctrine()->getEntityManager();
		$images = $em->getRepository('BurwieckIphotoBundle:Image')->findAll();

        return $this->render('BurwieckIphotoBundle::index.html.twig', array(
	        'images' => $images
        ));
	}

	public function showImageAction($slug) 
	{
		$em = $this->getDoctrine()->getEntityManager();
		$image = $em->getRepository('BurwieckIphotoBundle:Image')->findOneBySlug($slug);
		if($image) {
			return $this->render('BurwieckIphotoBundle::showImage.html.twig', array(
				'image' => $image
			));
		} 
		return $this->createNotFoundException('not found');
	}

	public function showFaceAction($slug)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$face = $em->getRepository('BurwieckIphotoBundle:Face')->findOneBySlug($slug);
		if($face) {
			return $this->render('BurwieckIphotoBundle::showFace.html.twig', array(
				'face' => $face
			));
		} 
		return $this->createNotFoundException('not found');
	}
}