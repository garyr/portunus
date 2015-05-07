<?php

namespace Portunus\Console\Command\Secret;

use Portunus\ContainerAwareTrait;
use Portunus\Controller\SafeController;
use Portunus\Controller\SecretController;
use Portunus\Crypt\RSA\PrivateKey;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class DecryptCommand extends Command
{
    use ContainerAwareTrait;

    protected function configure()
    {
        $this
            ->setName('secret:decrypt')
            ->setDescription('Decrypt a Portunus secret')
            ->addArgument(
                'privatekey',
                InputArgument::REQUIRED,
                'Private key file'
            )
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

        $output->writeln(sprintf("<info>Using safe '%s'... </info>", $safeName));

        $safe = $SafeController->view($safeName);

        $keyName = $input->getArgument('key');
        if (empty($keyName)) {
            $keyNames = $SecretController->listSecrets($safe);
            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion(
                '<question>Please select the key to decrypt:</question> ',
                $keyNames
            );
            $keyName = $helper->ask($input, $output, $question);
        }

        if (empty($keyName)) {
            throw new \Exception("Invalid key name");
        }

        $privateKey = $input->getArgument('privatekey');

        if (empty($privateKey) || !file_exists($privateKey)) {
            throw new \Exception("Invalid private key");
        }

        $output->writeln('');
        $output->write(sprintf("Decrypting secret '%s'... ", $keyName));

        $PrivateKey = new PrivateKey();
        $PrivateKey->setKey(file_get_contents($privateKey));

        try {
            $secret = $SecretController->view($safe, $keyName);
            $plainText = $secret->getValue($PrivateKey);
        } catch (\Exception $e) {
            $output->writeln('<error>FAILED</error>');
            $output->writeln('');
            $output->writeln('<error>' . $e->getMessage() .'</error>');
            return;
        }

        $output->writeln('<info>DONE</info>');
        $output->writeln('');

        $output->writeln(sprintf("<comment>'%s'</comment> = '%s'", $keyName, $plainText));
        $output->writeln('');
    }
}