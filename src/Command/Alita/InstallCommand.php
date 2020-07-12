<?php

declare(strict_types = 1);

namespace App\Command\Alita;

use App\Command\BaseCommand;
use App\Entity\Site;
use App\Entity\User;
use App\Service\alita\ForgotMailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class InstallCommand extends BaseCommand
{
    protected static $defaultName = 'alita:init';

    /** @var array<string, string[]> $tablesAction */
    protected array $tablesAction = [
        'database' => ['action' => 'installDatabase',   'description' => 'Install database'],
        'sites'    => ['action' => 'installSite',       'description' => 'Install Site', 'class' => Site::class],
        'users'    => ['action' => 'installUser',       'description' => 'Install User', 'class' => User::class],
    ];

    protected bool $install = false;

    protected ?Site $site = null;

    protected ForgotMailerService $forgotService;

    protected UserPasswordEncoderInterface $encoder;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $encoder,
        ForgotMailerService $forgotService,
        $name = null
    ) {
        parent::__construct($em, $name);
        $this->encoder       = $encoder;
        $this->forgotService = $forgotService;
    }

    public function configure(): void
    {
        $help = <<<EOF
Configure Alita step by Step.
EOF;
        $i = 1;
        foreach ($this->tablesAction as $table => $data) {
            $help .= <<<EOF

{$i}. {$data['description']}
EOF;
            ++$i;
        }

        $this->setDescription(<<<EOF
Command for install Alita step by step
EOF)
            ->setHelp($help);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input  = $input;
        $this->output = $output;
        $this->helper = $this->getHelper('question');

        $this->output->writeln('<info>OKay! Install Alita</info>');

        $this->preCheck();

        if ($this->install) {
            $this->upgrade(true);
        } elseif (1 <= count($this->tablesAction)) {
            $this->upgrade(false);
        } else {
            $this->output->writeln('<info>Install Ok!</info>');
        }

        return 0;
    }

    private function preCheck(): void
    {
        $progressBar = new ProgressBar($this->output, 1);
        $progressBar->setFormat('verbose');
        $this->output->writeln('<info>Precheck for Alita : </info>');
        $progressBar->start();
        $tables = [];
        try {
            $tables = $this->em->getConnection()->getSchemaManager()->listTableNames();
        } catch (\Exception $e) {
            $this->install = true;
            $progressBar->finish();
            $this->output->writeln('  <comment>No tables found, we need to install Alita</comment>');

            return;
        }

        if (0 === count($tables)) {
            $this->install = true;
            $progressBar->finish();
            $this->output->writeln('  <comment>No tables found, we need to install Alita</comment>');

            return;
        }

        unset($this->tablesAction['database']);

        foreach ($tables as $k => $v) {
            if (null !== $this->tablesAction[$v]) {
                if (null !== $this->em->getRepository($this->tablesAction[$v]['class'])->findOneBy([])) {
                    unset($this->tablesAction[$v]);
                }
            }
        }
        $progressBar->finish();
    }

    private function upgrade(bool $install): void
    {
        $this->output->writeln('<info>'.($install ? 'Install' : 'Update').' Alita</info>');

        $description = [
            '============================================',
            '== Alita - Summary of actions for '.($install ? 'install' : 'update').' ==',
            '============================================',
        ];

        $i = 1;
        foreach ($this->tablesAction as $table => $data) {
            $description[] = '== '.$i.'. '.$data['description'].str_repeat(' ', 35 - strlen($data['description'])).' ==';
            ++$i;
        }

        $description[] = '============================================';

        $this->output->writeln($description);

        $question = new ConfirmationQuestion('Are you agreed with this ? (y/n) [n] : ', false);

        if ($this->helper->ask($this->input, $this->output, $question)) {
            $progressBar = new ProgressBar($this->output, count($this->tablesAction));
            $this->output->writeln('<info>Okay, let\'s go!</info>');
            $progressBar->start();

            foreach ($this->tablesAction as $table => $data) {
                $this->{$data['action']}();
                $progressBar->advance();
            }
            $progressBar->finish();
        } else {
            $this->output->writeln('<comment>Abort '.($install ? 'install' : 'update').' </comment>');

            return;
        }
    }

    private function installDatabase(): void
    {
        /** @var Application $application */
        $application = $this->getApplication();
        $command     = $application->find('doctrine:database:create');

        $arguments = [
            'command' => 'doctrine:database:create',
        ];

        $args = new ArrayInput($arguments);
        $command->run($args, new NullOutput());

        $command = $application->find('doctrine:schema:create');

        $arguments = [
            'command' => 'doctrine:schema:create',
        ];

        $args = new ArrayInput($arguments);
        $command->run($args, new NullOutput());
    }

    private function installSite(): void
    {
        $this->output->writeln('');
        $questionSiteTitle = new Question('Title of website (alita) : ', 'alita');
        $questionSiteUrl   = new Question('URL of website (alita.localhost) : ', 'alita.localhost');

        $title = $this->helper->ask($this->input, $this->output, $questionSiteTitle);
        $url   = $this->helper->ask($this->input, $this->output, $questionSiteUrl);

        $site = new Site();
        $site->setTitle($title)
            ->setUrl($url)
            ->setCreatedBy('register_alita')
            ->setUpdatedBy('register_alita')
        ;

        $this->em->persist($site);
        $this->em->flush();
        $this->em->refresh($site);
        $this->site = $site;
    }

    private function installUser(): void
    {
        $this->output->writeln('');
        $this->output->writeln('Okay ! Please answer the following questions for creating super administrator');

        $callbackValidString = function ($value): string {
            if (!is_string($value) || empty($value)) {
                throw new InvalidArgumentException('Value cannot be empty');
            }

            return (string) $value;
        };

        $callbackValidEmail = function ($value): string {
            if (!is_string($value) || empty($value)) {
                throw new InvalidArgumentException('Value can not be empty');
            }

            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException('Value is not a email');
            }

            return (string) $value;
        };

        $questionUserLastName = new Question('Last name : ');
        $questionUserLastName->setValidator($callbackValidString);

        $questionUserFirstName = new Question('First name : ');
        $questionUserFirstName->setValidator($callbackValidString);

        $questionUserEmail = new Question('Email : ');
        $questionUserEmail->setValidator($callbackValidEmail);

        $lastname  = $this->helper->ask($this->input, $this->output, $questionUserLastName);
        $firstname = $this->helper->ask($this->input, $this->output, $questionUserFirstName);
        $email     = $this->helper->ask($this->input, $this->output, $questionUserEmail);

        if (null === $this->site) {
            /** @var Site $site */
            $site       = $this->em->getRepository(Site::class)->findAll()[0];
            $this->site = $site;
        }

        $user = new User();
        $user
            ->generateSalt()
            ->setActive(true)
            ->setFirstName($firstname)
            ->setLastName($lastname)
            ->setEmail($email)
            ->setPassword($this->encoder->encodePassword($user, uniqid()))
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])
            ->setSite($this->site)
            ->setCreatedBy('register_alita')
            ->setUpdatedBy('register_alita')
        ;

        $this->em->persist($user);
        $this->em->flush();
        $this->em->refresh($user);

        $this->forgotService->send($user, false, $this->site);

        $this->output->writeln('<info>Mail sended for initialize password</info>');
    }
}
