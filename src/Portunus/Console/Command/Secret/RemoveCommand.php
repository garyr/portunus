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
use Symfony\Component\Console\Question\ConfirmationQuestion;

class RemoveCommand extends Command
{
    use ContainerAwareTrait;

    protected function configure()
    {
        $this
            ->setName('secret:remove')
            ->setDescription('Remove a Portunus secret')
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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $SafeController = new SafeController();
        $SecretController = new SecretController();
        $helper = $this->getHelper('question');

        $safeName = $input->getArgument('safe');
        if (empty($safeName)) {
            $safeNames = $SafeController->getSafeNames();
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

        $safe = $SafeController->view($safeName);

        $keyName = $input->getArgument('key');
        if (empty($keyName)) {
            $keys = $SecretController->getKeys($safe);
            $question = new ChoiceQuestion(
                '<question>Please select the secret key to remove:</question> ',
                $keys
            );
            $keyName = $helper->ask($input, $output, $question);
        }

        if (empty($keyName)) {
            throw new \Exception("Invalid secret ket");
        }

        $question = "<question>Are you sure you want to delete this secret?</question>\n";
        $removeSecret = new ConfirmationQuestion($question, false);
        if (!$helper->ask($input, $output, $removeSecret)) {
            return;
        }

        if (!$removeSecret) {
            return;
        }

        $output->writeln('');
        $output->write(sprintf("Removing secret '%s'... ", $keyName));

        $removed = $SecretController->remove($safe, $keyName);

        if (!$removed) {
            $output->writeln('<error>FAILED</error>');
            $output->writeln('<error>Error removing secret</error>');
            $output->writeln('');
            return;
        }

        $output->writeln('<info>DONE</info>');
        $output->writeln('');
    }
}