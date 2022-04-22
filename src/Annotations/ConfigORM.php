<?php
namespace Mink67\KafkaConnect\Annotations;

use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToMany;

/**
 */
class ConfigORM {
    /**
    * @var ManyToOne
    */
   protected $manyToOne;

   /**
    * @var OneToOne
    */
    protected $oneToOne;
    /**
     * @var ManyToMany
     */
    protected $manyToMany;
    /**
     * @var OneToMany
     */
    protected $oneToMany;

   /**
    * @var string
    */
   protected $fieldName;

    public function __construct()
    {

    }

    /**
     * @return self
     */
    public function setManyToOne(ManyToOne $manyToOne = null)
    {
        $this->manyToOne = $manyToOne;
        return $this;
    }
    /**
     * @return self
     */
    public function setOneToMany(OneToMany $oneToMany = null)
    {
        $this->oneToMany = $oneToMany;
        return $this;
    }
    /**
     * @return string
     */
    public function setOneToOne(OneToOne $oneToOne = null)
    {
        $this->oneToOne = $oneToOne;
        return $this;
    }
    /**
     * @return string
     */
    public function setManyToMany(ManyToMany $manyToMany = null)
    {
        $this->manyToMany = $manyToMany;
        return $this;
    }

    /**
     * @return string
     */
    public function setFieldName(string $fieldName)
    {
        $this->fieldName = $fieldName;
        return $this;
    }
    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }
    /**
     * @return bool
     */
    public function isValid()
    {
        return !is_null($this->oneToOne) || !is_null($this->manyToOne) || !is_null($this->manyToMany)  || !is_null($this->oneToMany);
    }
    /**
     * @return string
     */
    public function getTargetEntity(): ?string
    {
        
        $targetOneToOne = !is_null($this->oneToOne) ? $this->oneToOne->targetEntity: null;
        $targetManyToOne = !is_null($this->manyToOne) ? $this->manyToOne->targetEntity: null;
        $targetManyToMany = !is_null($this->manyToMany) ? $this->manyToMany->targetEntity: null;
        $targetOneToMany = !is_null($this->oneToMany) ? $this->oneToMany->targetEntity: null;

        if (!is_null($targetManyToOne)) {
            return $targetManyToOne;
        }elseif(!is_null($targetOneToOne)) {
            return $targetOneToOne;
        }elseif(!is_null($targetManyToMany)) {
            return $targetManyToMany;
        }elseif(!is_null($targetOneToMany)) {
            return $targetOneToMany;
        }else {
            return null;
        }
    }

}