<?php

namespace Mink67\KafkaConnect\Commands;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\ArticleCopy;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Mink67\KafkaConnect\Services\Receive as ServicesReceive;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mink67\KafkaConnect\Annotations\Readers\ReaderConfig;
use Mink67\KafkaConnect\Constant;
use Mink67\KafkaConnect\Db\LockingDb;
use Mink67\KafkaConnect\Services\EntityManager;
use Mink67\KafkaConnect\Services\Utils\MessageDbValidator;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use ApiPlatform\Core\Bridge\Symfony\Routing\IriConverter;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * Perment de créer un un kafka connect
 */
class Receive extends Command {
    /**
     * @var ServicesReceive
     */
    private $receive;
    protected static $defaultName = 'kafka:receive';
    protected static $defaultDescription = 'Receptionne les notification kafka pour rna...';

    /**
     * @var MessageDbValidator
     */
    private $validator;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var ReaderConfig
     */
    private $reader;
    /**
     * @var DenormalizerInterface
     */
    private $denormalizer;
    /**
     * @var IriConverterInterface
     */
    private $iriConverter;
    /**
     * @var EntityManager
     */
    private $emKafka;

    /**
     * 
     */
    public function __construct(
        ServicesReceive $receive,
        EntityManagerInterface $em,
        MessageDbValidator $validator,
        ReaderConfig $reader,
        DenormalizerInterface $denormalizer,
        IriConverterInterface $iriConverter,
        EntityManager $emKafka
    ) {
        //dd(get_class($iriConverter));
        $this->receive = $receive;
        $this->em = $em;
        $this->reader = $reader;
        $this->validator = $validator;
        $this->denormalizer = $denormalizer;
        $this->iriConverter = $iriConverter;
        $this->emKafka = $emKafka;

        parent::__construct();
        
    }

    protected function configure(): void
    {
        
        $this
            // If you don't like using the $defaultDescription static property,
            // you can also define the short description using this method:
            // ->setDescription('...')

            // the command help shown when running the command with the "--help" option
            ->setHelp('Receptionne les notification kafka pour rna...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'En attente des données',
            '============',
            '',
        ]);


        // the value returned by someMethod() can be an iterator (https://secure.php.net/iterator)
        // that generates and returns the messages with the 'yield' PHP keyword
        $i=0;
        $j=0;
        do {
            $receive = $this->receive;
            
            $messageBase64 = $receive();
            $messageStr = base64_decode($messageBase64);

            $messageArr = \json_decode($messageStr, true);

            $resultValid = $this->validator->validate($messageArr);
            //dd($resultValid);
            if (!$resultValid) {

                $output->writeln([
                    $messageStr,
                    $i,
                    'Data not valid',
                ]);

            }else {

                $output->writeln([
                    $messageStr,
                    $i,
                    'Data valid',
                ]);

                $entity = $this->flushData($messageArr, $output);

                if (is_null($entity)) {
                    $j++;
                }

                $output->writeln([
                    'Not register',
                    $j,
                ]);

            }
            
            $i++;

            
        } while (true);


        return Command::SUCCESS;
    }

    /**
     * 
     */
    public function flushData(array $messageArr, OutputInterface $output)
    {
        $classes = $this->em->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();

        $resourceName = $messageArr["metaData"]["resourceName"];
        $output->writeln([
            $resourceName,
            'Resource name',
        ]);

        $classConcerns = [];
        $entity = null;
        $persistEntity = null;
        $lockEntity = null;

        foreach ($classes as $key => $className) {

            $config = $this->reader->getConfigCopy($className);

            if (is_null($config)) {
                continue;
            }

            $resourceNameClass = $config->getResourceName();

            if ($resourceNameClass != $resourceName) {
                continue;
            }

            $output->writeln([
                $config->getResourceName(),
            ]);

            $entity = $this->getEntity($className, $messageArr, $output);

            if (is_null($entity)) {
                continue;
            }

            $lockEntity = $entity;

            if ($this->isLock($lockEntity)) {

                $output->writeln([
                    get_class($entity). "/". $lockEntity->getId() . " is lock",
                ]);
                continue;
            }

            $output->writeln([
                get_class($entity),
            ]);

            try {
                $persistEntity = $this->denormalize($entity, $messageArr, $output);
                
            } catch (InvalidArgumentException $th) {
                
                $output->writeln([
                    $th->getMessage(),
                ]);
                return null;
            } catch (UnexpectedValueException $th) {
                
                $output->writeln([
                    $th->getMessage(),
                ]);
                return null;
            }
            
        }

        return $persistEntity;
    }

    /**
     * 
     */
    public function isLock($entity)
    {
        $lock = $this->emKafka->getLock($entity);        

        return !is_null($lock);

    }

    /**
     * 
     */
    private function getEntity(string $className, array $messageArr, OutputInterface $output)
    {
        if (!class_exists($className)) {
            return null;
        }

        $repository = $this->em->getRepository($className);

        $entity = $repository->find($messageArr["data"]["id"]);

        if (
            is_null($entity)
        ) {
            $entity = new $className;
        }

        $output->writeln([
            get_class($entity),
        ]);


        return $entity;

    }
    /**
     * 
     */
    private function denormalize($entity, array $messageArr, OutputInterface $output)
    {


        $groups = $messageArr["metaData"]["groups"];
        $data = $messageArr["data"];

        $data = $this->tranformData($entity, $data, $output);


        if (is_null($data)) {
            return null;
        }

        if (
            is_null($entity) ||
            !method_exists($entity, "getCreatedAt") ||
            !method_exists($entity, "getUpdatedAt") ||
            !method_exists($entity, "setCreatedAt") ||
            !method_exists($entity, "setUpdatedAt") || 
            !method_exists($entity, "getId") || 
            !method_exists($entity, "setId")
        ) {
            return null;            
        }

        $updateAt = $entity->getUpdatedAt();
        //$isNewerVersion = false;
        $dataUpdatedAt = new DateTime($data["updatedAt"]);

        $output->writeln([
            json_encode($dataUpdatedAt),
        ]);

        if (
            $messageArr["action"] === Constant::CREATE_ACTION &&
            !is_null($entity->getId())
        ) {
            $output->writeln([
                "the action was already done",
                "action: ". $messageArr["action"],//$messageArr["action"]
                "KO",
            ]);
            return null;
        }elseif (
            !is_null($updateAt) && 
            $updateAt instanceof \DateTimeInterface && 
            $updateAt->getTimestamp() >= $dataUpdatedAt->getTimestamp()
        ) {
            $output->writeln([
                "the action was already done",
                "create object exist: ". json_encode($updateAt),
                "create object update: ". json_encode($dataUpdatedAt),//$messageArr["action"]
                "action: ". $messageArr["action"],//$messageArr["action"]
                "KO",
            ]);
            return null;
        }


        $output->writeln([
            json_encode($groups),
        ]);

        $entity = $this->denormalizer
                        ->denormalize(
                            $data,
                            get_class($entity),
                            null,
                            [
                                'groups' => $groups,
                                AbstractNormalizer::OBJECT_TO_POPULATE => $entity
                            ]
                    );
        


        if (!$this->em->contains($entity)) {

            $entity->setId($data["id"]);
            $this->em->persist($entity);
            
            //dump($this->em->contains($entity));
        }
        

        $this->em->flush();

        $output->writeln([
            json_encode($entity->getId()),
            "OK",
        ]);

        return $entity;
        
    }

    /**
     * 
     */
    public function tranformData($entity, array $data, OutputInterface $output)
    {
        $output->writeln([
            "class is Article",
            get_class($entity) == ArticleCopy::class,
        ]);

        $configsORM = $this->reader->getConfigORM(get_class($entity));

        if (empty($configsORM)) {
            $output->writeln([
                "Name many to many /",
                
            ]);
            return $data;
        }

        //$configORM = array_shift($configsORM);
        $newData = unserialize(serialize($data));
        foreach ($configsORM as $key => $configORM) {
            $targetType = (!is_null($configORM) && $configORM->isValid()) ? $configORM->getTargetEntity() : null;
            $fieldName = $configORM->getFieldName();

            if (!isset($data[$fieldName])) {
                continue;
            }

    
            if (is_null($targetType) || (!isset($data[$fieldName]["id"]) && !is_array($data[$fieldName]) )) {
                
                $output->writeln([
                    "ORM config error"
                ]);
    
                return null;
    
            }

            if (isset($data[$fieldName]["id"])) {
                $iri = $this->iriConverter->getIriFromResourceClass($targetType) ."/".$data[$fieldName]["id"];
                //dd($iri);
                $newData[$fieldName] = $iri;                
            } else if(is_array($data[$fieldName]) ) {
                $iris = [];
                foreach ($data[$fieldName] as $key => $entity) {


                    if (!isset($entity["id"])) {
                        
                        $output->writeln([
                            "ORM config error"
                        ]);
            
                        return null;
                    }

                    array_push($iris, $this->iriConverter->getIriFromResourceClass($targetType) ."/".$entity["id"]);

                }

                $newData[$fieldName] = $iris;                
                //dump($newData);
                //return null;
            }
    
            
        }
        //dd($newData);
        return $newData;
        
    }


}
