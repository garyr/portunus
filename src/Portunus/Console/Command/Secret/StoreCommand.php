<?php

namespace Portunus\Console\Command\Secret;

use Portunus\ContainerAwareTrait;
use Portunus\Controller\SafeController;
use Portunus\Controller\SecretController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class StoreCommand extends Command
{
    use ContainerAwareTrait;

    protected function configure()
    {
        $this
            ->setName('secret:store')
            ->setDescription('Store a Portunus secret')
            ->addArgument(
                'safe',
                InputArgument::OPTIONAL,
                'Safe name'
            )
            ->addArgument(
                'key',
                InputArgument::OPTIONAL,
                'Secret key'
            )
            ->addArgument(
                'value',
                InputArgument::OPTIONAL,
                'Secret value'
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
        $output->writeln(sprintf("<info>Using safe '%s'... </info>", $safeName));

        $keyName = $input->getArgument('key');
        if (empty($keyName)) {
            $helper = $this->getHelper('question');
            $question = new Question('<question>Please enter the key name for this secret :</question> ');
            $keyName = $helper->ask($input, $output, $question);
        }

        if (empty($keyName)) {
            throw new \Exception("Invalid key name");
        }

        $value = $input->getArgument('value');
        if (empty($value)) {
            $helper = $this->getHelper('question');
            $question = new Question('<question>Please enter the value for this secret (output hidden):</question> ');
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            $value = $helper->ask($input, $output, $question);
        }

        if (empty($value) && !file_exists($value)) {
            throw new \Exception("Invalid value");
        }

        if (file_exists($value)) {
            $value = file_get_contents($value);
        }

        $output->writeln('');
        $output->write(sprintf("Creating secret '%s'... ", $keyName));

        $SecretController = new SecretController();

        try {
            $safe = $SafeController->view($safeName);
            $SecretController->create($safe, $keyName, $value);
        } catch (\Exception $e) {
            $output->writeln('<error>FAILED</error>');
            $output->writeln('');
            $output->writeln('<error>' . $e->getMessage() .'</error>');
            return;
        }

        $output->writeln('<info>DONE</info>');
        $output->writeln('');
    }
}
