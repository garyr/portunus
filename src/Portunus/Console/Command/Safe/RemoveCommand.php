<?php

namespace Portunus\Console\Command\Safe;

use Portunus\ContainerAwareTrait;
use Portunus\Controller\SafeController;
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
            ->setName('safe:remove')
            ->setDescription('Remove a Portunus safe')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Safe name'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $SafeController = new SafeController();
        $helper = $this->getHelper('question');

        $output->writeln('');

        $safeName = $input->getArgument('name');
        if (empty($safeName)) {
            $safeNames = $SafeController->getSafeNames();
            $question = new ChoiceQuestion(
                '<question>Please select the safe to delete:</question> ',
                $safeNames
            );
            $safeName = $helper->ask($input, $output, $question);
        }

        if (empty($safeName)) {
            throw new \Exception("Invalid safe name");
        }

        $question = "<question>Are you sure you want to remove this safe?</question>\n";
        $question .= '<info>(This action will remove all secrets stored for this safe): </info>';
        $removeSafe = new ConfirmationQuestion($question, false);
        if (!$helper->ask($input, $output, $removeSafe)) {
            return;
        }

        if (!$removeSafe) {
            return;
        }

        $output->writeln('');
        $output->write(sprintf("Removing safe '%s'... ", $safeName));

        $SafeController = new SafeController();

        $removed = $SafeController->remove($safeName);

        if (!$removed) {
            $output->writeln('<error>FAILED</error>');
            $output->writeln('<error>Error removing safe</error>');
            $output->writeln('');
            return;
        }

        $output->writeln('<info>DONE</info>');
        $output->writeln('');
    }
}