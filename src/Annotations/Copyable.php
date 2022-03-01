<?php
namespace Mink67\KafkaConnect\Annotations;
use Attribute;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("CLASS")
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Copyable extends Config {    
}