<?php

declare(strict_types = 1);

namespace App\Command\Alita;

use App\Command\BaseCommand;
use App\Entity\Site;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class InstallCommand extends BaseCommand
{
    protected static $defaultName = 'alita:init';

    private array $tables;

    private bool $install = false;

    public function configure(): void
    {
        $this->setDescription(<<<EOF
Command for install Alita step by step
EOF)
            ->setHelp(<<<EOF
Configure Alita step by Step.
1. Configure first website
EOF);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input  = $input;
        $this->output = $output;

        $this->output->writeln('<info>OKay! Install Alita</info>');

        $this->preCheck();

        if ($this->install) {
            $this->install();
        }

        return 0;
    }

    private function preCheck(): void
    {
        $progressBar = new ProgressBar($this->output, 1);
        $progressBar->setFormat('verbose');
        $this->output->writeln('<info>Precheck for Alita : </info>');
        $progressBar->start();
        $this->tables = $this->em->getConnection()->getSchemaManager()->listTableNames();

        if (0 === count($this->tables)) {
            $this->install = true;
            $progressBar->finish();
            $this->output->writeln('  <comment>No tables found, we need to install Alita</comment>');

            return;
        }
    }

    private function install(): void
    {
        $this->output->writeln('<info>Install Alita</info>');
        $this->output->writeln([
            '============================================',
            '== Alita - Summary of actions for install ==',
            '============================================',
            '== 1. Install Database                    ==',
            '== 2. Configure Website                   ==',
            '============================================',
        ]);

        $helper   = $this->getHelper('question');
        $question = new ConfirmationQuestion('Are you agreed with this ? (y/n) [n] : ', false);

        if ($helper->ask($this->input, $this->output, $question)) {
            $questionSiteTitle = new Question('Title of website (alita) : ', 'alita');
            $questionSiteUrl   = new Question('URL of website (alita.localhost) : ', 'alita.localhost');

            $siteTitle = $helper->ask($this->input, $this->output, $questionSiteTitle);
            $siteUrl   = $helper->ask($this->input, $this->output, $questionSiteUrl);

            $progressBar = new ProgressBar($this->output, 2);
            $progressBar->setFormat('verbose');
            $this->output->writeln('<info>Okay, let\'s go!</info>');
            $progressBar->start();
            $this->installDatabase();
            $progressBar->advance();
            $this->installSite($siteTitle, $siteUrl);
            $progressBar->finish();
        } else {
            $this->output->writeln('<comment>Uninstalling</comment>');

            return;
        }
    }

    public function installDatabase(): void
    {
        $command = $this->getApplication()->find('doctrine:schema:create');

        $arguments = [
            'command' => 'doctrine:schema:create',
        ];

        $args = new ArrayInput($arguments);
        $command->run($args, new NullOutput());
    }

    public function installSite(string $title, string $url): void
    {
        $site = new Site();
        $site->setTitle($title)
            ->setUrl($url)
            ->setCreatedBy('register_alita')
            ->setUpdatedBy('register_alita')
        ;

        $this->em->persist($site);
        $this->em->flush();
    }
}
