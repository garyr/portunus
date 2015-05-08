<?php

namespace Portunus\Console\Command\Secret;

use Portunus\ContainerAwareTrait;
use Portunus\Controller\SafeController;
use Portunus\Controller\SecretController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class ListCommand extends Command
{
    use ContainerAwareTrait;

    protected function configure()
    {
        $this
            ->setName('secret:list')
            ->setDescription('List the Portunus secrets')
            ->addArgument(
                'safe',
                InputArgument::OPTIONAL,
                'Safe name'
            )
            ->addOption(
                'signature',
                's',
                InputOption::VALUE_NONE,
                'If set, the secret signature will be displayed'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $SafeController = new SafeController();

        $safeName = $input->getArgument('safe');
        if (empty($safeName)) {
            $safeNames = $SafeController->getSafeNames();
            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion(
                '<question>Please select the safe for this secret:</question> ',
                $safeNames
            );
            $safeName = $helper->ask($input, $output, $question);
        }

        if (empty($safeName)) {
            throw new \Exception("Invalid safe name");
        }

        $output->writeln('');

        $SafeController = new SafeController();
        $safe = $SafeController->view($safeName);
        $SecretController = new SecretController();

        try {
            $secrets = $SecretController->listSecrets($safe);
        } catch (\Exception $e) {
            $output->writeln('<error>FAILED</error>');
            $output->writeln('');
            $output->writeln('<error>' . $e->getMessage() .'</error>');
            return;
        }

        $rows = array();
        foreach ($secrets as $key => $secret) {
            if ($input->getOption('signature')) {
                $rows[] = array(
                    $secret->getKey(),
                    hash('sha256', $secret->getValue()),
                    strlen($secret->getValue()),
                    $secret->getCreated()->format('Y-m-d H:i:s'),
                    $secret->getUpdated()->format('Y-m-d H:i:s'),
                );
            } else {
                $rows[] = array(
                    $secret->getKey(),
                    strlen($secret->getValue()),
                    $secret->getCreated()->format('Y-m-d H:i:s'),
                    $secret->getUpdated()->format('Y-m-d H:i:s'),
                );
            }
        }

        $table = $this->getHelper('table');

        if ($input->getOption('signature')) {
            $table->setHeaders(array('Key Name', 'Signature', 'Length', 'Created', 'Updated'))->setRows($rows);
        } else {
            $table->setHeaders(array('Key Name', 'Length', 'Created', 'Updated'))->setRows($rows);
        }

        $table->render($output);
        $output->writeln('');
    }
}