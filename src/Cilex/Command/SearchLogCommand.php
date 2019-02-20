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

        $search_amazon_count = $search_amazon_count_success = $search_amazon_count_fail = $search_amazon_count_timeout = 0;
        $search_amazon_12_count = $search_amazon_12_count_success = $search_amazon_12_count_fail = $search_amazon_12_count_timeout = 0;
        $search_china_count = $search_china_count_success = $search_china_count_fail = $search_china_count_timeout = 0;

        if ($type == 'ios12') {
            $path = __DIR__ . '/../File/ios12.log';
        } elseif ($type == 'create') {
            $path = __DIR__ . '/../File/create.log';
        } elseif ($type == 'amazon') {
            $path = __DIR__ . '/../File/amazon.log';
        } elseif ($type == 'china') {
            $path = __DIR__ . '/../File/china.log';
        }
        $handle = fopen($path, 'r');
        if ($handle) {
            while (!feof($handle)) {
                $buffer = fgets($handle);
                $array = explode(',', $buffer, 4);
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
                        case 'amazon':
                            if (substr($array[2], -5) == 'ios12') {
                                $search_amazon_12_count++;
                                $str = substr(trim($array[3]), 4);
                                $data = unserialize($str);
                                if (!$data) {
                                    $output->writeln($str);
                                }
                                $res_data = json_decode($data['data'], true);
                                if ($data['error'] == 'Operation timed out after 5000 milliseconds with 0 bytes received') {
                                    $search_amazon_12_count_timeout++;
                                }
                                if ($data['errno'] == 0 && $res_data['status'] == 0 && isset($res_data['pos']) && $res_data['pos'] != -1 && $res_data['pos'] != 10000) {
                                    $search_amazon_12_count_success++;
                                } else {
                                    $search_amazon_12_count_fail++;
                                }
                            } else {
                                $search_amazon_count++;
                                $str = substr(trim($array[3]), 4);
                                $data = unserialize($str);
                                if (!$data) {
                                    $output->writeln($str);
                                }
                                $res_data = json_decode($data['data'], true);
                                if ($data['error'] == 'Operation timed out after 5000 milliseconds with 0 bytes received') {
                                    $search_amazon_count_timeout++;
                                }
                                if ($data['errno'] == 0 && $res_data['status'] == 0 && isset($res_data['pos']) && $res_data['pos'] != -1) {
                                    $search_amazon_count_success++;
                                } else {
                                    $search_amazon_count_fail++;
                                }
                            }
                            break;
                        case 'china':
                            $search_china_count++;
                            $str = substr(trim($array[3]), 4);
                            $data = unserialize($str);
                            $res_data = json_decode($data['data'], true);
                            if ($data['error'] == 'Operation timed out after 5000 milliseconds with 0 bytes received') {
                                $search_china_count_timeout++;
                            }
                            if ($data['errno'] == 0 && $res_data['status'] == 0 && isset($res_data['pos']) && $res_data['pos'] != -1 && $res_data['pos'] != 10000) {
                                $search_china_count_success++;
                            } else {
                                $search_china_count_fail++;
                            }
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
        } elseif ($type == 'amazon') {
            $success_rate = $search_amazon_count_success/$search_amazon_count;
            $success_rate_12 = $search_amazon_12_count_success/$search_amazon_12_count;
            $fail_rate = $search_amazon_count_fail/$search_amazon_count;
            $fail_rate_12 = $search_amazon_12_count_fail/$search_amazon_12_count;
            $timeout_rate = $search_amazon_count_timeout/$search_amazon_count;
            $timeout_rate_12 = $search_amazon_12_count_timeout/$search_amazon_12_count;
            $output->writeln("总次数：$search_amazon_count" . "，成功次数：$search_amazon_count_success" . "，失败次数：$search_amazon_count_fail" . "，超时次数：$search_amazon_count_timeout" . "，成功率：$success_rate" . "，失败率：$fail_rate" . "，超时率：$timeout_rate");
            $output->writeln("ios12总次数：$search_amazon_12_count" . "，ios12成功次数：$search_amazon_12_count_success" . "，ios12失败次数：$search_amazon_12_count_fail" . "，ios12超时次数：$search_amazon_12_count_timeout" . "，ios12成功率：$success_rate_12" . "，ios12失败率：$fail_rate_12" . "，ios12超时率：$timeout_rate_12");
        } elseif ($type == 'china') {
            $success_rate = $search_china_count_success/$search_china_count;
            $fail_rate = $search_china_count_fail/$search_china_count;
            $timeout_rate = $search_china_count_timeout/$search_china_count;
            $output->writeln("总次数：$search_china_count" . "，成功次数：$search_china_count_success" . "，失败次数：$search_china_count_fail" . "，超时次数：$search_china_count_timeout" . "，成功率：$success_rate" . "，失败率：$fail_rate" . "，超时率：$timeout_rate");
        }
    }
}