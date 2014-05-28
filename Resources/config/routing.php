<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('praktika_test_homepage', new Route('', array(
    '_controller' => 'PraktikaTestBundle:Default:index',
)));
$collection->add('praktika_test_viewp1', new Route('/points1', array(
    '_controller' => 'PraktikaTestBundle:Default:viewp1',
)));
$collection->add('praktika_test_viewp2', new Route('/points2', array(
    '_controller' => 'PraktikaTestBundle:Default:viewp2',
)));
$collection->add('praktika_test_dist', new Route('/dist', array(
    '_controller' => 'PraktikaTestBundle:Default:dist',
)));
$collection->add('praktika_test_editpoint1', new Route('/editpoint1', array(
    '_controller' => 'PraktikaTestBundle:Default:editpoint1',
)));
$collection->add('praktika_test_editpoint2', new Route('/editpoint2', array(
    '_controller' => 'PraktikaTestBundle:Default:editpoint2',
)));

$collection->add('praktika_test_googlemaps', new Route('/googlemaps', array(
    '_controller' => 'PraktikaTestBundle:Default:googlemaps',
)));

$collection->add('praktika_test_yamaps', new Route('/yamaps', array(
    '_controller' => 'PraktikaTestBundle:Default:yamaps',
)));

$collection->add('praktika_test_maps2', new Route('/maps2', array(
    '_controller' => 'PraktikaTestBundle:Default:maps2',
)));


return $collection;
