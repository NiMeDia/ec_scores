<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation as Form;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * 
 * @Form\Name("User") 
 * @Form\Instance("\Application\Entity\User")
 * @Form\Hydrator("Zend\Hydrator\ClassMethods")
 */
class User
{
    /**
     * @var int
     * 
     * @ORM\Id
     * @ORM\Column(type="string")
     * 
     * @Form\Attributes({"type":"text"})
     * @Form\AllowEmpty(true)
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     * 
     * @Form\Required(false)
     * @Form\Type("Zend\Form\Element\Text")
     */
    protected $displayName;


    public function getId() {
        return $this->id;
    }

    public function getDisplayName() {
        return $this->displayName;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setDisplayName($displayName) {
        $this->displayName = $displayName;
    }

}
