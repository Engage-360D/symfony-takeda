<?php

namespace Engage360d\Bundle\TakedaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Engage360d\Bundle\TakedaBundle\Entity\Institution;

class LoadInstitutionsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('takeda:load-institutions');
        $this->addArgument('file', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $cmd = $em->getClassMetadata('Engage360dTakedaBundle:Institution');
        $connection = $em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
            $connection->executeUpdate($q);
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        }
        catch (\Exception $e) {
            $connection->rollback();
        }

        foreach (file($input->getArgument('file')) as $i => $row) {
            $row = str_getcsv($row, ';');

            $institution = new Institution();
            $institution->setSpecialization($row[0]);
            $institution->setName($row[1]);
            $institution->setAddress($row[2]);
            $institution->setGoogleAddress($row[3]);
            $institution->setRegion($row[4]);
            $institution->setParsedTown($row[5]);
            $institution->setParsedStreet($row[6]);
            $institution->setParsedHouse($row[7]);
            $institution->setParsedCorpus($row[8]);
            $institution->setParsedBuilding($row[9]);
            $institution->setParsedRegion($row[10]);
            $institution->setPriority($row[11]);

            $em->persist($institution);

            if ($i % 1000 === 0) {
                echo "Done $i\n";
                $em->flush();
                $em->clear();
            }
        }

        $em->flush();
    }
}
