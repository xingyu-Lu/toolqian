<?php


namespace Cilex\Command;


use Cilex\Provider\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DecodeCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('decode:md5')
            ->setDescription('Md5 Decode');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->decode($input, $output);
    }

    private function decode($input, $output)
    {
        $handle = fopen(__DIR__ . '/../File/data.csv', 'r');
        while (($buffer = fgets($handle, 4096)) !== false) {
            $data = [
                '6c628cce013221407f3546ee9c0d88da',
                '69a68d1d3937985a4e9d43ad70942477',
                '49b321c50da678800ab0619507353342',
                '8d6f16104fa26bbe44f8a62b031901c0',
                '91f57a38ce15afe463ac5125f9a3e994',
            ];
            $buffer = trim($buffer);
            foreach ($data as $datum) {
                if ($datum == md5($buffer)) {
                    $output->writeln($buffer);
                }
            }
        }
        if (!feof($handle)) {
            $output->writeln("Error: unexpected fgets() fail\n");
        }
        fclose($handle);
    }
}