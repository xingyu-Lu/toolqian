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
                'd39c8d2b743596871d4b1690fe1fa870',
                'fc094ce1a2c517ade0c5e0f79d75641a',
                'dfee213b56a58dec917d5472128e5b44',
                '091c17510cf912151867be8f47a2cb53',
                'ed1e408a789f7ae8e0a1591b9e5d9ce1',
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