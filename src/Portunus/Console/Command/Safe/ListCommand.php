<?php

namespace Portunus\Console\Command\Safe;

use Portunus\ContainerAwareTrait;
use Portunus\Controller\SafeController;
use Portunus\Controller\SecretController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends Command
{
    use ContainerAwareTrait;

    protected function configure()
    {
        $this
            ->setName('safe:list')
            ->setDescription('List the Portunus safes')
            ->addOption(
                'signature',
                's',
                InputOption::VALUE_NONE,
                'If set, the safe signature will be displayed'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('');

        $SafeController = new SafeController();

        try {
            $safes = $SafeController->listSafes();
        } catch (\Exception $e) {
            $output->writeln('<error>FAILED</error>');
            $output->writeln('');
            $output->writeln('<error>' . $e->getMessage() .'</error>');
            return;
        }

        $SecretController = new SecretController();

        $rows = array();
        foreach ($safes as $key => $safe) {
            if ($input->getOption('signature')) {
                $rows[] = array(
                    $safe->getName(),
                    $safe->getPublicKey()->getKeySignature(),
                    count($SecretController->listSecrets($safe)),
                    $safe->getCreated()->format('Y-m-d H:i:s'),
                    $safe->getUpdated()->format('Y-m-d H:i:s'),
                );
            } else {
                $rows[] = array(
                    $safe->getName(),
                    count($SecretController->listSecrets($safe)),
                    $safe->getCreated()->format('Y-m-d H:i:s'),
                    $safe->getUpdated()->format('Y-m-d H:i:s'),
                );
            }
        }

        $table = $this->getHelper('table');
        if ($input->getOption('signature')) {
            $table->setHeaders(array('Safe Name', 'Signature', '# Secrets', 'Created', 'Updated'))->setRows($rows);
        } else {
            $table->setHeaders(array('Safe Name', '# Secrets', 'Created', 'Updated'))->setRows($rows);
        }

        $table->render($output);
        $output->writeln('');
    }
}