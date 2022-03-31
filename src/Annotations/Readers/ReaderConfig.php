<?php
namespace Mink67\KafkaConnect\Annotations\Readers;

use Doctrine\Common\Annotations\Reader;
use Mink67\KafkaConnect\Annotations\Copy;
use Mink67\KafkaConnect\Annotations\Copyable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\ManyToMany;
use Mink67\KafkaConnect\Annotations\ConfigORM;

class ReaderConfig {

    /**
     * @var Reader
     */
    private $reader;
    

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    //ORM


    /**
     * @param string $className
     * @return ConfigORM[]
     */
    private function getORMAnnotation(string $className): ?array
    {
        $reflection = new \ReflectionClass($className);
        /**
         * @var \ReflectionProperty[]
         */
        $properties = $reflection->getProperties();
        
        $configsORMAnn = [];
        
        foreach ($properties as $property) {
                /**
                 * @var ManyToOne
                 */
                $annotationManyToOne = $this->reader->getPropertyAnnotation($property, ManyToOne::class);
                /**
                 * @var OneToOne
                 */
                $annotationOneToOne = $this->reader->getPropertyAnnotation($property, OneToOne::class);
                /**
                 * @var ManyToMany
                 */
                $annotationManyToMany = $this->reader->getPropertyAnnotation($property, ManyToMany::class);


                $configOrmAnn = new ConfigORM;

                $configOrmAnn->setOneToOne($annotationOneToOne);
                $configOrmAnn->setManyToOne($annotationManyToOne);
                $configOrmAnn->setManyToMany($annotationManyToMany);

                if ($configOrmAnn->isValid()) {
                    $configOrmAnn->setFieldName($property->getName());
                    array_push($configsORMAnn, $configOrmAnn);                    
                }


        }

        return $configsORMAnn;
    }
    /**
     * 
     */

    /**
     * @param string $className
     * @return ConfigORM[] 
     */
    private function getORMAttribute(string $className):?array
    {
        $reflection = new \ReflectionClass($className);

        /**
         * @var \ReflectionProperty[]
         */
        $properties = $reflection->getProperties();
        
        $configsORMAnn = [];

        
        foreach ($properties as $property) {
                $atts = $property->getAttributes();
                $configOrmAnn = new ConfigORM;

                foreach ($atts as $key => $att) {
                    //dump($property->getName());
                    if ($att->getName() == OneToOne::class) {
                        
                        $args = $att->getArguments();

                        $annotationOneToOne = new  OneToOne(
                            isset($args['mappedBy']) ?$args['mappedBy']:null,
                            isset($args['inversedBy'])?$args['inversedBy']:null,
                            isset($args['targetEntity'])?$args['targetEntity']:null,
                            isset($args['cascade'])?$args['cascade']:null,
                            isset($args['fetch'])?$args['fetch']:'LAZY',
                            isset($args['orphanRemoval'])?$args['orphanRemoval']:false
                        );
                        $configOrmAnn->setOneToOne($annotationOneToOne);
                    }
                    if ($att->getName() == ManyToOne::class) {
                        $args = $att->getArguments();

                        $annotationManyToOne = new ManyToOne(
                            isset($args['targetEntity'])?$args['targetEntity']:null,
                            isset($args['cascade'])?$args['cascade']:null,
                            isset($args['fetch'])?$args['fetch']:'LAZY',
                            isset($args['inversedBy'])?$args['inversedBy']:null
                        );
                        $configOrmAnn->setManyToOne($annotationManyToOne);
                    }
                    if ($att->getName() == ManyToMany::class) {
                        //dump($att->getName() == ManyToMany::class);
                        $args = $att->getArguments();

                        $annotationManyToMany = new ManyToMany(
                            isset($args['targetEntity'])?$args['targetEntity']:null,
                            isset($args['mappedBy']) ?$args['mappedBy']:null,
                            isset($args['inversedBy'])?$args['inversedBy']:null,
                            isset($args['cascade'])?$args['cascade']:null,
                            isset($args['fetch'])?$args['fetch']:'LAZY',
                            isset($args['orphanRemoval'])?$args['orphanRemoval']:false,
                            isset($args['indexBy'])?$args['indexBy']:null,

                        );
                        $configOrmAnn->setManyToMany($annotationManyToMany);
                    }
                }

                if ($configOrmAnn->isValid()) {
                    $configOrmAnn->setFieldName($property->getName());
                    array_push($configsORMAnn, $configOrmAnn);                    
                }

        }

        return $configsORMAnn;

    }
    /**
     * @param string $className
     * @return ConfigORM[] 
     */
    public function getConfigORM(string $className):?array
    {
        
        $configs = [
            ... $this->getORMAnnotation($className),
            ... $this->getORMAttribute($className)
        ];

        return $configs;
    }
    //END ORM
    /**
     * @param string $className
     * @return Copy 
     */
    private function getCopyAnnotation(string $className):?Copy
    {
        $reflection = new \ReflectionClass($className);
        $annotation = $this->reader->getClassAnnotation($reflection, Copy::class);

        if ($annotation !== null && ($annotation instanceof Copy)) {
            return $annotation;
        }

        return null;
    }
    /**
     * 
     */

    /**
     * @param string $className
     * @return Copy 
     */
    private function getCopyAttribute(string $className):?Copy
    {
        $reflection = new \ReflectionClass($className);
        $atts = $reflection->getAttributes();
        $attsFilter = array_filter(
            $atts,
            function (\ReflectionAttribute $att)
            {
                return $att->getName() == Copy::class;
            }
        );

        $reflectionAtt = array_shift($attsFilter);
        
        if ($reflectionAtt !== null) {
            return new Copy($reflectionAtt->getArguments());
        }

        return null;
    }
    /**
     * @param string $className
     * @return Copy 
     * 
     */
    public function getConfigCopy(string $className):?Copy
    {
        $config = $this->getCopyAnnotation($className);
        if (is_null($config)) {
            $config = $this->getCopyAttribute($className);
        }

        return $config;
    }
    /**
     * @param string $className
     * @return Copyable 
     */
    private function getCopyableAnnotation(string $className):?Copyable
    {
        $reflection = new \ReflectionClass($className);
        $annotation = $this->reader->getClassAnnotation($reflection, Copyable::class);

        if ($annotation !== null && ($annotation instanceof Copyable)) {
            return $annotation;
        }

        return null;
    }
    /**
     * @param string $className
     * @return Copy 
     */
    private function getCopyableAttribute(string $className):?Copyable
    {
        $reflection = new \ReflectionClass($className);
        $atts = $reflection->getAttributes();
        $attsFilter = array_filter(
            $atts,
            function (\ReflectionAttribute $att)
            {
                return $att->getName() == Copyable::class;
            }
        );

        $reflectionAtt = array_shift($attsFilter);
        
        if ($reflectionAtt !== null) {
            return new Copyable($reflectionAtt->getArguments());
        }

        return null;
    }
    /**
     * @param string $className
     * @return Copyable 
     * 
     */
    public function getConfigCopyable(string $className):?Copyable
    {
        $config = $this->getCopyableAnnotation($className);

        if (is_null($config)) {
            $config = $this->getCopyableAttribute($className);
        }

        return $config;
    }
    

}