<?php
namespace Mink67\KafkaConnect\Annotations;

/**
 */
abstract class Config {
    /**
     * @var string
     */
    protected $resourceName;
    /**
     * @var string
     */
    protected $topicName;
    /**
     * @var array
     */
    protected $groups = [];

    public function __construct(array $options)
    {
        //dd($options['options']);

        if (empty($options['resourceName'])) {
            throw new \InvalidArgumentException("L'annotation doit avoir un attribut 'resourceName'");
        }
        if (empty($options['topicName'])) {
            throw new \InvalidArgumentException("L'annotation doit avoir un attribut 'topicName'");
        }

        if (!is_string($options['resourceName'])) {
            throw new \InvalidArgumentException("L'attribut 'resourceName' doit être une chaine des caractère");
        }

        if (empty($options['groups'])) {
            throw new \InvalidArgumentException("L'annotation doit avoir un attribut 'groups'");
        }
        if (!is_array($options['groups'])) {
            throw new \InvalidArgumentException("L'attribut 'resourceName' doit être un tableau");
        }


        $this->resourceName = $options['resourceName'];
        $this->topicName = $options['topicName'];
        $groups = $options['groups'];

        foreach ($groups as $key => $group) {
            if (!is_string($group)) {
                throw new \InvalidArgumentException("les élément L'attribut 'groups' doivent être une chaine des caractère");
            }            
        }
        $this->groups = $options['groups'];
    }


    /**
     * @return string
     */
    public function getResourceName()
    {
        return $this->resourceName;
    }
    /**
     * @return string
     */
    public function getTopicName()
    {
        return $this->topicName;
    }
    /**
     * @return array 
     */
    public function getGroups()
    {
        return $this->groups;
    }
    
}