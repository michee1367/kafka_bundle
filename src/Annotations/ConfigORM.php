<?php
namespace Mink67\KafkaConnect\Annotations;

use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\ManyToMany;

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
        return !is_null($this->oneToOne) || !is_null($this->manyToOne) || !is_null($this->manyToMany);
    }
    /**
     * @return string
     */
    public function getTargetEntity(): ?string
    {
        
        $targetOneToOne = !is_null($this->oneToOne) ? $this->oneToOne->targetEntity: null;
        $targetManyToOne = !is_null($this->manyToOne) ? $this->manyToOne->targetEntity: null;
        $targetManyToMany = !is_null($this->manyToMany) ? $this->manyToMany->targetEntity: null;

        if (!is_null($targetManyToOne)) {
            return $targetManyToOne;
        }elseif(!is_null($targetOneToOne)) {
            return $targetOneToOne;
        }elseif(!is_null($targetManyToMany)) {
            return $targetManyToMany;
        }else {
            return null;
        }
    }

}