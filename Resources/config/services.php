<?php

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Parameter;

$container->setParameter("form.longitude","");
$container->setParameter("form.latitude","");
$container->setParameter("form.height","");
$container->setParameter("form.title","");
$container->setParameter("form.label_longitude", "");
$container->setParameter("form.label_latitude", "");
$container->setParameter("form.label_height", "");
$container->setParameter("form.label_title", "");

$container->setParameter("form.label_azimuth", "");
$container->setParameter("form.label_distance", "");
$container->setParameter("form.distance", "");
$container->setParameter("form.azimuth", "");

$container
    ->setDefinition('praktika.form.type.geopoint', new Definition(
        'Praktika\TestBundle\Form\Extension\geoType',
        array(	'%form.longitude%', 
				'%form.latitude%', 
				'%form.height%', 
				'%form.title%',
				'%form.label_longitude%', 
				'%form.label_latitude%', 
				'%form.label_height%', 
				'%form.label_title%')
    ))
    ->addTag('form.type', array(
        'alias' => 'geopoint',
    ))
;


$container
    ->setDefinition('praktika.form.type.geoapoint', new Definition(
        'Praktika\TestBundle\Form\Extension\geoAType',
        array(	'%form.longitude%', 
				'%form.latitude%', 
				'%form.height%', 
				'%form.title%',
				'%form.azimuth%',
				'%form.distance%',
				'%form.label_longitude%', 
				'%form.label_latitude%', 
				'%form.label_height%', 
				'%form.label_title%',
				'%form.label_azimuth%',
				'%form.label_distance%')
    ))
    ->addTag('form.type', array(
        'alias' => 'geoapoint',
    ))
;