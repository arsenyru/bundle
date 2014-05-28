<?php
 namespace Praktika\TestBundle\Entity;
 use Doctrine\ORM\Mapping as ORM;
 use Knp\DoctrineBehaviors\Model as ORMBehaviors;
 use Knp\DoctrineBehaviors\ORM\Geocodable\Type\Point;
 /**
 * @ORM\Entity
 * @ORM\Table(name="points")
 */
 class Points {
 /**
 * @ORM\Id
 * @ORM\Column(type="integer")
 * @ORM\GeneratedValue(strategy="AUTO")
 */
 protected $id;
 /**
 * @ORM\Column(type="text")
 */
 protected $title;
 /**
 * @ORM\Column(type="point")
 */
 protected $point;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Points
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set point
     *
     * @param Point $point
     * @return Points
     */
    public function setPoint($point)
    {
        $this->point = $point;
    
        return $this;
    }

    /**
     * Get point
     *
     * @return Point 
     */
    public function getPoint()
    {
		//print_r($this->point);
        return $this->point;
    }
}
