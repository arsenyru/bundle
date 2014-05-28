<?php
namespace Praktika\TestBundle\Form\Extension;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class geoAType extends AbstractType {
    private $long, $lat, $height, $title, $label_long, $label_lat, $label_height, $label_title;
	private $azimuth, $distance, $label_azimuth, $label_distance;

	
	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'longitude'            => null,
            'latitude'             => null,
            'height'          => null,
			'title' => '',
			'azimuth' => null,
			'distance' => null,
			'label_longitude'=>'Longitude',
			'label_latitude'=>'Latitude',
			'label_height'=>'Height',
			'label_title'=>'Title',
			'label_azimuth'=>'Azimuth',
			'label_distance'=>'Distance'
        ));
    }
	
	public function buildView(FormView $view, FormInterface $form, array $options)
    {
       $view->vars = array_replace($view->vars, array(
            'longitude' => $this->long,
            'latitude' => $this->lat,
            'height' => $this->height,
			'title'=>$this->title,
			'azimuth'=>$this->azimuth,
			'distance'=>$this->distance,
			'label_longitude'=>$this->label_long,
			'label_latitude'=>$this->label_lat,
			'label_title'=>$this->label_title,
			'label_height'=>$this->label_height,
			'label_azimuth'=>$this->label_azimuth,
			'label_distance'=>$this->label_distance
        ));
    }
	
	
	public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$this->long = $options['longitude'];
		$this->lat = $options['latitude'];
		$this->height = $options['height'];
		$this->title = $options['title'];
		$this->azimuth = $options['azimuth'];
		$this->distance = $options['distance'];
		$this->label_long = $options['label_longitude'];
		$this->label_lat = $options['label_latitude'];
		$this->label_height = $options['label_height'];
		$this->label_title = $options['label_title'];
		$this->label_azimuth = $options['label_azimuth'];
		$this->label_distance = $options['label_distance'];
    }
	
	public function getParent()
    {
        return 'text';
    }
	 /**
     * Returns the name of the type
     *
     * @return string The name of the type 
     */
    public function getName()
    {
        return 'geoapoint';
    }
}

?>