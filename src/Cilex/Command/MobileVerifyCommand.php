<?php


namespace Cilex\Command;


use Cilex\Provider\Console\Command;
use Cilex\Tools\Time;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MobileVerifyCommand extends Command
{
    const API = 'http://120.27.32.132:8080/phone/name';//诺证通身份认证接口（真实姓名+身份证号）
    const NUOZHENGTONG_MALL_ID = '110860';//诺证通 mall_id
    const NUOZHENGTONG_KEY = 'bc51b601d3c709e6634d1867d8762dfd';//诺证通身份认证接口key

    protected function configure()
    {
        $this
            ->setName('mobile:verify')
            ->setDescription('Check Mobile And Get Something Information')
            ->addArgument('mobile', InputArgument::REQUIRED, 'Please input mobile?')
            ->addArgument('real_name', InputArgument::REQUIRED, 'Please input real_name?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mobile = $input->getArgument('mobile');
        $real_name = $input->getArgument('real_name');
        $time = Time::getMillisecond();
        $sign = $this->createSign($real_name, $mobile, $time);
        $url = $this->createApi($real_name, $mobile, $time, $sign);
        $output->writeln('test');
    }

    private function createApi($real_name, $mobile, $time, $sign)
    {
        $url = self::API . '?mall_id' . self::NUOZHENGTONG_MALL_ID . '&realname=' . $real_name . '&phone=' . $mobile . '&tm=' . $time . '&sign=' . $sign;
        return $url;
    }

    private function createSign($real_name, $mobile, $time, $key = self::NUOZHENGTONG_KEY, $mall_id = self::NUOZHENGTONG_MALL_ID)
    {
        $str = $mall_id . $real_name . $mobile . $time . $key;
        return md5($str);
    }
}