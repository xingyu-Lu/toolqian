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
                                $data = @unserialize($str);
                                //输出不能正常反序列化的数据
                                if (!$data) {
                                    $output->writeln($array[2]);
                                    $output->writeln($str);
                                    echo PHP_EOL;
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
                                $data = @unserialize($str);
                                //输出不能正常反序列化的数据
                                if (!$data) {
                                    $output->writeln($array[2]);
                                    $output->writeln($str);
                                    echo PHP_EOL;
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
            $this->PrintOut($type, $search_ios12_count, $search_ios12_count_success, $search_ios12_count_fail, $search_ios12_count_timeout,  $output);
        } elseif ($type == 'create') {
            $this->PrintOut($type, $rank_created_count, $rank_created_count_success, $rank_created_count_fail, $rank_created_count_timeout,  $output);
        } elseif ($type == 'amazon') {
            $this->PrintOut($type, $search_amazon_count, $search_amazon_count_success, $search_amazon_count_fail, $search_amazon_count_timeout, $output);
            $this->PrintOut($type . '12', $search_amazon_12_count, $search_amazon_12_count_success, $search_amazon_12_count_fail, $search_amazon_12_count_timeout, $output);
        } elseif ($type == 'china') {
            $this->PrintOut($type, $search_china_count, $search_china_count_success, $search_china_count_fail, $search_china_count_timeout, $output);
        }
    }

    protected function PrintOut($type, $counts, $success_counts, $fail_counts, $time_out_counts, $output)
    {
        $success_rate = $success_counts/$counts;
        $fail_rate = $fail_counts/$counts;
        $timeout_rate = $time_out_counts/$counts;
        $output->writeln("$type 总次数：$counts" . "$type ，成功次数：$success_counts" . "$type ，失败次数：$fail_counts" . "$type ，超时次数：$time_out_counts" . "$type ，成功率：$success_rate" . "$type ，失败率：$fail_rate" . "$type ，超时率：$timeout_rate");
    }
}