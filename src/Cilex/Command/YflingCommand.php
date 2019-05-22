<?php


namespace Cilex\Command;


use Cilex\Provider\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class YflingCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('Yfling:statistics')
            ->setDescription('Yfling Yfling');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $old_see_time = strtotime('2018/07/21');
        $old_say_time = strtotime('2019/04/20');
        $see_day = (time()-$old_see_time)/(24*60*60);
        $say_day = (time()-$old_say_time)/(24*60*60);

        $output->writeln('2018/07/22号开始就没和你见过面了');
        $output->writeln('===================================');
        $output->writeln('2019/04/21号开始你就不和我说话了');
        $output->writeln('===================================');
        $output->writeln("$see_day . \"天未见你");
        $output->writeln("===================================");
        $output->writeln("$say_day . \"天未和我说话了");
    }
}