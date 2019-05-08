<?php


namespace Cilex\Command;


use Cilex\Provider\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotSameRegionCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('region_count:not_same_region_count')
            ->setDescription('Not Same Region Count');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = __DIR__ . '/../File/not_same_region_count.log';
        $handle = fopen($path, 'r');
        $result_array = [];
        if ($handle) {
            while (!feof($handle)) {
                $buffer = fgets($handle);
                $array = explode(',', $buffer);
                try {
                    $data = explode(':', $array[2]);
                    if (!in_array($data[1], $result_array)) {
                        $result_array[] = $data[1];
                    }
                } catch (\Exception $e) {
                    echo 'message:' . $e->getMessage();
                }
            }
            fclose($handle);
        }
        $output->writeln("数量：" . count($result_array));
    }
}