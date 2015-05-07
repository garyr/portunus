<?php

namespace Portunus\Console\Command\Safe;

use Portunus\ContainerAwareTrait;
use Portunus\Controller\SafeController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Question\Question;

class CreateCommand extends Command
{
    use ContainerAwareTrait;

    protected function configure()
    {
        $this
            ->setName('safe:create')
            ->setDescription('Create a Portunus safe')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Safe name'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        if (empty($name)) {
            $helper = $this->getHelper('question');
            $question = new Question('<question>Please enter the name of the safe to create:</question> ');
            $name = $helper->ask($input, $output, $question);
        }

        if (empty($name)) {
            throw new \Exception("Invalid safe name");
        }

        $output->writeln('');
        $output->write(sprintf("Creating safe '%s'... ", $name));


        $SafeController = new SafeController();

        try {
            $PrivateKey = $SafeController->create($name);
        } catch (\Exception $e) {
            $output->writeln('<error>FAILED</error>');
            $output->writeln('');
            $output->writeln('<error>' . $e->getMessage() .'</error>');
            return;
        }

        $output->writeln('<info>DONE</info>');
        $output->writeln('');

        $output->writeln('<comment>PLEASE STORE PRIVATE KEY (CANNOT BE RECOVERED)</comment>');
        $output->writeln($PrivateKey->getKey());
        $output->writeln('');
    }
}