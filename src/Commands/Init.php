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
use Mink67\KafkaConnect\Services\Init as ServicesInit;
use Mink67\KafkaConnect\Services\Utils\MessageDbValidator;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Perment de créer un un kafka connect
 */
class Init extends Command {
    /**
     * @var ServicesInit
     */
    private $init;

    protected static $defaultName = 'kafka:init';
    protected static $defaultDescription = 'Permet d\'inserer les données init dans kafka...';


    public function __construct(ServicesInit $init) {
        $this->init = $init;
        parent::__construct();

    }
    protected function configure(): void
    {
        $this
            // If you don't like using the $defaultDescription static property,
            // you can also define the short description using this method:
            // ->setDescription('...')

            // the command help shown when running the command with the "--help" option
            ->setHelp('Permet d\'inserer les données init dans kafka...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $output->writeln([
            'Debut',
            '============',
            '',
        ]);
        $init = $this->init;

        $init();
        
        return Command::SUCCESS;
    }
    

}