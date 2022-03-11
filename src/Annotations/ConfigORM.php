<?php
namespace Mink67\KafkaConnect\Annotations;

use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;

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
        return !is_null($this->oneToOne) || !is_null($this->manyToOne);
    }
    /**
     * @return string
     */
    public function getTargetEntity(): ?string
    {
        
        $targetOneToOne = !is_null($this->oneToOne) ? $this->oneToOne->targetEntity: null;
        $targetManyToOne = !is_null($this->manyToOne) ? $this->manyToOne->targetEntity: null;

        if (!is_null($targetManyToOne)) {
            return $targetManyToOne;
        }elseif(!is_null($targetOneToOne)) {
            return $targetOneToOne;
        }else {
            return null;
        }
    }

}