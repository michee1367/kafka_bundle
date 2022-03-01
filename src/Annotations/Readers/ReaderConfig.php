<?php
namespace Mink67\KafkaConnect\Annotations\Readers;

use Doctrine\Common\Annotations\Reader;
use Mink67\KafkaConnect\Annotations\Copy;
use Mink67\KafkaConnect\Annotations\Copyable;

class ReaderConfig {

    /**
     * @var Reader
     */
    private $reader;
    

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }
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