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

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
'',
'',
'               AAA               LLLLLLLLLLL             IIIIIIIIIITTTTTTTTTTTTTTTTTTTTTTT         AAA',
'              A:::A              L:::::::::L             I::::::::IT:::::::::::::::::::::T        A:::A    ',
'             A:::::A             L:::::::::L             I::::::::IT:::::::::::::::::::::T       A:::::A   ',
'            A:::::::A            LL:::::::LL             II::::::IIT:::::TT:::::::TT:::::T      A:::::::A     ',
'           A:::::::::A             L:::::L                 I::::I  TTTTTT  T:::::T  TTTTTT     A:::::::::A      ',
'          A:::::A:::::A            L:::::L                 I::::I          T:::::T            A:::::A:::::A       ',
'         A:::::A A:::::A           L:::::L                 I::::I          T:::::T           A:::::A A:::::A        ',
'        A:::::A   A:::::A          L:::::L                 I::::I          T:::::T          A:::::A   A:::::A        ',
'       A:::::A     A:::::A         L:::::L                 I::::I          T:::::T         A:::::A     A:::::A       ',
'      A:::::AAAAAAAAA:::::A        L:::::L                 I::::I          T:::::T        A:::::AAAAAAAAA:::::A      ',
'     A:::::::::::::::::::::A       L:::::L                 I::::I          T:::::T       A:::::::::::::::::::::A     ',
'    A:::::AAAAAAAAAAAAA:::::A      L:::::L         LLLLLL  I::::I          T:::::T      A:::::AAAAAAAAAAAAA:::::A    ',
'   A:::::A             A:::::A   LL:::::::LLLLLLLLL:::::LII::::::II      TT:::::::TT   A:::::A             A:::::A   ',
'  A:::::A               A:::::A  L::::::::::::::::::::::LI::::::::I      T:::::::::T  A:::::A               A:::::A  ',
' A:::::A                 A:::::A L::::::::::::::::::::::LI::::::::I      T:::::::::T A:::::A                 A:::::A ',
'AAAAAAA                   AAAAAAALLLLLLLLLLLLLLLLLLLLLLLLIIIIIIIIII      TTTTTTTTTTTAAAAAAA                   AAAAAAA',
]);

        return 1;
    }
}
