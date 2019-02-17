<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation as Form;

/**
 * @ORM\Entity
 * @ORM\Table(name="scores")
 * 
 * @Form\Name("Score") 
 * @Form\Instance("\Application\Entity\Score")
 * @Form\Hydrator("Zend\Hydrator\ClassMethods")
 */
class Score {

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     * @Form\Attributes({"type":"hidden"})
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Entity\User", cascade={"persist", "merge"})
     * @ORM\JoinColumn(name="user", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * 
     * @Form\Required(true)
     * @Form\Options({"label":"User: "})
     * ---Form\Type("DoctrineModule\Form\Element\ObjectSelect")
     * @Form\ComposedObject({
     *      "target_object": "Application\Entity\User",
     *      "allow_add":true,
     *      "is_collection":false,
     *      "options":{"count":1} 
     * })
     * ---Form\Attributes({"id":"user", "data-placeholder":"Choose tags...", "class" : "chosen-select"})
     * ---Form\Attributes({"id":"tags", "data-placeholder":"Choose tags...", "multiple" : "multiple", "class" : "chosen-select"})
     **/
    protected $user;

    /**
     * @ORM\Column(type="float")
     * 
     * @Form\Type("Zend\Form\Element\Text")
     * @Form\Filter({"name": "StripTags"})
     * @Form\Filter({"name": "StringTrim"})
     * @Form\Filter({"name": "NumberFormat", "options":{ "locale": "en_GB"}})
     * @Form\Options({"label":"Year Score: "})
     */
    protected $year;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" = 0})
     * 
     * @Form\Type("Zend\Form\Element\Checkbox")
     * @Form\Required(false)
     * @Form\Options({"label":"Started with zero? ", "use_hidden_element": false})
     */
    protected $startedWithZero = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Form\Exclude()
     */
    protected $publishDate;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * 
     * @Form\Required(true)
     * @Form\Type("Zend\Form\Element\Text")
     */
    protected $version;

    public function getId() {
        return $this->id;
    }

    public function getUser() {
        return $this->user;
    }

    public function getYear() {
        return $this->year;
    }

    public function getStartedWithZero() {
        return $this->startedWithZero;
    }

    public function getPublishDate() {
        return $this->publishDate;
    }

    public function getVersion() {
        return $this->version;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    public function setYear($year) {
        $this->year = $year;
    }

    public function setStartedWithZero($startedWithZero) {
        $this->startedWithZero = $startedWithZero;
    }

    public function setPublishDate($publishDate) {
        $this->publishDate = $publishDate;
    }

    public function setVersion($version) {
        $this->version = $version;
    }


}
