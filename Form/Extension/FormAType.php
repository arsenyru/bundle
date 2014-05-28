<?php

namespace Praktika\TestBundle\Form\Extension;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class FormAType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$param = array();
		foreach (array('longitude','latitude','height','title','label_longitude','label_latitude','label_title','label_height','azimuth','label_azimuth','distance','label_distance') as $v)
			if (isset($options['data'][$v]))
				$param[$v] = $options['data'][$v];
		
        $builder->add('geo', 'geoapoint', $param);
    }
	
	
	public function getName()
    {
        return 'geoapoint2';
    }
}

?>