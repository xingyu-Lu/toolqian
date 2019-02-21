<?php


namespace Cilex\Command;


use Cilex\Provider\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MobileNumberSegment extends Command
{
    protected function configure()
    {
        $this
            ->setName('mobile:number_segment')
            ->setDescription('Count Number Segment');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = __DIR__ . '/../File/mobile_number.log';
        $res_path = __DIR__ . '/../File/res_number.log';
        $handle = fopen($path, 'r');
        $mobile_array = [];
        if ($handle) {
            try {
                while (!feof($handle)) {
                    $buffer = fgets($handle);
                    $number = substr(trim($buffer), 1, 7);
                    if (!empty($mobile_array[$number])) {
                        $mobile_array[$number]++;
                    }
                    if (empty($mobile_array[$number])) {
                        $mobile_array[$number] = 1;
                    }
                }
                fclose($handle);
            } catch (\Exception $e) {
                $output->writeln('message:' . $e->getMessage());
            }
        }
        foreach ($mobile_array as $key => $item) {
            error_log($key . ',次数：' . $item . "\n", 3, $res_path);
            if ($item > 10) {
                $output->writeln($key . ',次数：' . $item);
            }
        }
    }
}