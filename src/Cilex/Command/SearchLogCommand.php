<?php


namespace Cilex\Command;


use Cilex\Provider\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SearchLogCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('log:search')
            ->setDescription('Get something from log')
            ->addArgument('type', InputArgument::REQUIRED, 'Please input type?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');
        $search_ios12_count = 0;
        $search_ios12_count_success = 0;
        $search_ios12_count_fail = 0;
        $search_ios12_count_timeout = 0;
        $rank_created_count = 0;
        $rank_created_count_success = 0;
        $rank_created_count_fail = 0;
        $rank_created_count_timeout = 0;
        if ($type == 'ios12') {
            $path = __DIR__ . '/../File/ios12.log';
        } elseif ($type == 'create') {
            $path = __DIR__ . '/../File/create.log';
        }
        $handle = fopen($path, 'r');
        if ($handle) {
            while (!feof($handle)) {
                $buffer = fgets($handle);
                $array = explode(',', $buffer);
//                var_dump($array);
                try {
                    switch (trim($array[1])) {
                        case 'IOS12':
                            $search_ios12_count++;
                            $str = substr(trim($array[3]), 4);
//                        echo $str;exit;
                            $data = unserialize($str);
//                            var_dump($data);
                            if ($data['error'] == 'Operation timed out after 5000 milliseconds with 0 bytes received') {
                                $search_ios12_count_timeout++;
                            }
                            if ($data['errno'] == 0 && $data['data']['status'] == 0 && isset($data['data']['data']['rank']) && $data['data']['data']['rank'] != 0) {
                                $search_ios12_count_success++;
                            } else {
                                $search_ios12_count_fail++;
                            }
//                        var_dump($data);
                            break;
                        case 'get_rank_created':
                            $rank_created_count++;
                            $str = substr(trim($array[3]), 4);
                            $data = unserialize($str);
                            if ($data['error'] == 'Operation timed out after 5000 milliseconds with 0 bytes received') {
                                $rank_created_count_timeout++;
                            }
                            if ($data['errno'] == 0 && $data['data']['status'] == 0) {
                                $rank_created_count_success++;
                            } else {
                                $rank_created_count_fail++;
                            }
//                        var_dump($data);
                            break;
                    }
                } catch (\Exception $e) {
                    echo 'message:' . $e->getMessage();
                }
            }
            fclose($handle);
        }
        if ($type == 'ios12') {
            $success_rate = $search_ios12_count_success/$search_ios12_count;
            $fail_rate = $search_ios12_count_fail/$search_ios12_count;
            $timeout_rate = $search_ios12_count_timeout/$search_ios12_count;
            $output->writeln("总次数：$search_ios12_count" . "，成功次数：$search_ios12_count_success" . "，失败次数：$search_ios12_count_fail" . "，超时次数：$search_ios12_count_timeout" . "，成功率：$success_rate" . "，失败率：$fail_rate" . "，超时率：$timeout_rate");
        } elseif ($type == 'create') {
            $success_rate = $rank_created_count_success/$rank_created_count;
            $fail_rate = $rank_created_count_fail/$rank_created_count;
            $timeout_rate = $rank_created_count_timeout/$rank_created_count;
            $output->writeln("总次数：$rank_created_count" . "，成功次数：$rank_created_count_success" . "，失败次数：$rank_created_count_fail" . "，超时次数：$rank_created_count_timeout" . "，成功率：$success_rate" . "，失败率：$fail_rate" . "，超时率：$timeout_rate");
        }
    }
}