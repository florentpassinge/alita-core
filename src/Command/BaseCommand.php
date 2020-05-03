<?php

declare(strict_types = 1);

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BaseCommand extends Command
{
    protected EntityManagerInterface $em;

    protected OutputInterface $output;

    protected InputInterface $input;

    protected static $defaultName = 'alita:info';

    public function __construct(EntityManagerInterface $em, $name = null)
    {
        $this->em = $em;
        parent::__construct($name);
    }
}
