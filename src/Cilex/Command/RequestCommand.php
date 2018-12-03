<?php


namespace Cilex\Command;

use http\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Cilex\Provider\Console\Command;

class RequestCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('request:active')
            ->setDescription('Request interface')
            ->addArgument('api', InputArgument::REQUIRED, 'Please input api?')
            ->addArgument('method', InputArgument::REQUIRED, 'Please input method?');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->preCheck($input, $output);
        $this->doOperate($input, $output);
        $output->writeln('all is over');
    }

    private function preCheck(InputInterface $input, OutputInterface $output)
    {
        $api = $input->getArgument('api');
        $method = $input->getArgument('method');

        $str = strstr($api, '//');
        if (!$str) {
            $output->writeln('api wrong');
            exit;
        }

        if (!in_array($method, ['get', 'post'])) {
            $output->writeln('method wrong');
            exit;
        }
    }

    private function doOperate(InputInterface $input, OutputInterface $output)
    {
        $api = $input->getArgument('api');
        $method = $input->getArgument('method');
        $array = $this->getArray();

        if ($method == 'get') {
            $this->goForeach($array, $api, $method);
        } elseif ($method == 'post') {
            $this->goForeach($array, $api, $method);
        }
    }

    private function goForeach($array, $api, $method)
    {
        foreach ($array as $item) {
            $arr = explode(',', $item);
            $param = [
                'keyword' => $arr[0],
                'idfa' => $arr[1],
                'ip' => $arr[2],
                'os' => $arr[3],
                'device' => $arr[5]
            ];
            if ($method == 'post') {
                $res = $this->httpPost($api, $param);
                var_dump($res);
            } elseif ($method == 'get') {
                $param_str = http_build_query($param);
                $api .= '?' . $param_str;
                $res = $this->httpGet($api);
                var_dump($res);
            }
        }
    }

    private function getArray()
    {
        $array = [
            '芝麻借款,36326091-A27A-4AFD-9837-438D93283456,36.100.37.90,12.1,2018年11月29日,iPhone5s',
            '芝麻借款,8080F28F-5950-45B7-9517-4F0453630BC1,58.21.52.146,12.1,2018年11月29日,iPhone6s Plus',
            '芝麻借款,D52C01FF-7741-4974-83DF-0D31ADC3B84B,222.184.184.149,12.1,2018年11月29日,iPhone8',
            '小黑鱼贷款,FBDAC087-03BD-415C-AD8D-934478896CAF,106.115.14.253,10.3.3,2018年11月29日,iPhone6',
            '小黑鱼贷款,2435C427-8D5B-4062-9214-7123831C7592,139.205.187.194,10.2,2018年11月29日,iPhone6 Plus',
            '小黑鱼贷款,AA5AD247-7069-4C0C-A004-D958479A55AF,120.43.62.139,12.1,2018年11月29日,iPhone6 Plus',
            '小黑鱼贷款,56DFE3F5-D54A-4FD9-9F93-1C7BFCEB4D0E,122.189.41.140,12.1,2018年11月29日,iPhone8',
            '小黑鱼贷款,51E28289-5392-44E4-920F-C80500AC368B,117.92.254.196,10.3.3,2018年11月29日,iPhone5',
            '分期乐app,85F464B9-2174-4C31-ACC1-B5A035BE857B,27.188.226.166,12.1,2018年11月29日,iPhone6',
            '分期乐app,4126FDC1-6EE3-4F90-B438-BA407DD5623F,112.50.8.65,12.1,2018年11月29日,iPhone6s',
            '分期乐app,19D0A0E9-91CC-498C-B362-4BEE825F39E1,221.192.179.85,10.3.1,2018年11月29日,iPhone SE',
            '分期乐app,9EB37399-FE28-4EE7-9616-D64AA4BC6F21,119.100.124.65,10.3.2,2018年11月29日,iPhone6s',
            '分期乐app,9826985A-DA90-4514-8757-5762D73B5D2E,111.8.123.244,11.4.1,2018年11月29日,iPhone5s',
            '分期乐app,F5FA2EAB-12FE-44CF-908E-3367256E75CC,116.149.196.222,10.0.2,2018年11月29日,iPhone6 Plus',
            '分期乐app,27B0CA64-9A37-4F9F-83D8-7351AEF67B62,182.106.100.226,12.0.1,2018年11月29日,iPhone SE',
        ];
        return $array;
    }

    private function httpPost($url, $data, $type = 'form', $timeout = 5)
    {
        $ret = [
            'errno' => 0,
            'error' => '',
            'data' => '',
            'http_code' => 0,
        ];

        if ($type == 'raw') {
            $post_data = http_build_query($data);
        } elseif ($type == 'json') {
            $post_data = json_encode($data);
        } elseif ($type == 'header_auth') {
            $header_auth_token = $data['header_auth_token'];
            unset($data['header_auth_token']);
            $post_data = json_encode($data);
        } elseif ($type == 'header_tangeche_auth') {
            $header_tangeche_auth_token = $data['sign'];
            unset($data['sign']);
            $post_data = json_encode($data);
        } else {
            $post_data = $data;
        }

        $ch = curl_init();

        if ($type == 'json') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Content-Length: ' . strlen($post_data)));
        }

        if ($type == 'header_auth') {
            $header_auth_token_str = 'token:' . $header_auth_token;
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept:application/json', $header_auth_token_str));
        }

        if ($type == 'header_doumi_auth') {
            if (isset($data['header_auth'])) {
                $header_auth_token_str = 'Authorization:' . $data['header_auth'];
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:application/vnd.doumi.v1+json', 'Content-Type: application/json', $header_auth_token_str));
                unset($data['header_auth']);
            } else {
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:application/vnd.doumi.v1+json', 'Content-Type: application/json'));
            }

            $post_data = json_encode($data);
        }

        if ($type == 'header_certificate') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('certification:7000deb1d3d591092a3fc0ce341e9d4b'));
        }

        if ($type == 'header_auth_md5') {
            $time_sign = time();
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('APP-KEY:ASO10000','APP-TIMESTAMP:' . time(), 'APP-SIGNATURE:' . md5('ASO10000' . '06933f0448a07c636cc25ce15e6a028a' . $time_sign)));
        }

        if ($type == 'header_tangeche_auth') {
            $header_auth_token_str = 'x-izayoi-sign:' . $header_tangeche_auth_token;
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept:application/json', $header_auth_token_str));
        }

        curl_setopt ( $ch,CURLOPT_TIMEOUT, $timeout);
        //curl_setopt ( $ch,CURLOPT_VERBOSE, 1);
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 QianZhuangApi/2.0");
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );

        $data = curl_exec($ch);

        $httpStatus = curl_getinfo($ch);
        $ret['http_code'] = $httpStatus['http_code'];

        if (empty($data)) {
            $ret['errno'] = 1;
            $ret['error'] = curl_error($ch);
        } else {
            $ret['data'] = $data;
        }
        curl_close($ch);

        return $ret;
    }

    private function httpGet($url, $timeout = 5)
    {
        $ret = [
            'errno' => '0',
            'error' => '',
            'data' => '',
            'http_code' => 0,
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 QianZhuangApi/2.0");
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);

        $httpStatus = curl_getinfo($ch);
        $ret['http_code'] = $httpStatus['http_code'];

        if (empty($data)) {
            $ret['errno'] = 1;
            $ret['error'] = curl_error($ch);
        } else {
            $ret['data'] = $data;
        }
        curl_close($ch);

        return $ret;
    }
}