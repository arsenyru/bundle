<?php
namespace Praktika\TestBundle\Form\Extension;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class geoType extends AbstractType {
    private $long, $lat, $height, $title, $label_long, $label_lat, $label_height, $label_title;

	
	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'longitude'            => null,
            'latitude'             => null,
            'height'          => null,
			'title' => '',
			'label_longitude'=>'Longitude',
			'label_latitude'=>'Latitude',
			'label_height'=>'Height',
			'label_title'=>'Title'
        ));
    }
	
	public function buildView(FormView $view, FormInterface $form, array $options)
    {
       $view->vars = array_replace($view->vars, array(
            'longitude' => $this->long,
            'latitude' => $this->lat,
            'height' => $this->height,
			'title'=>$this->title,
			'label_longitude'=>$this->label_long,
			'label_latitude'=>$this->label_lat,
			'label_title'=>$this->label_title,
			'label_height'=>$this->label_height
        ));
    }
	
	
	public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$this->long = $options['longitude'];
		$this->lat = $options['latitude'];
		$this->height = $options['height'];
		$this->title = $options['title'];
		$this->label_long = $options['label_longitude'];
		$this->label_lat = $options['label_latitude'];
		$this->label_height = $options['label_height'];
		$this->label_title = $options['label_title'];
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
        return 'geopoint';
    }
}

?>