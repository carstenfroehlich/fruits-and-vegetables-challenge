<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Service\ItemProcessorService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:load-data',
    description: 'Load fruits and vegetables from JSON file',
)]
final class LoadDataCommand extends Command
{
    public function __construct(
        private ItemProcessorService $itemProcessorService,
        private string $projectDir
    ) {
        parent::__construct();
    }
    
    protected function configure(): void
    {
        $this
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'JSON file path', 'request.json');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $file = $input->getOption('file');
        $filePath = $this->projectDir . '/' . $file;
        
        if (!file_exists($filePath)) {
            $io->error(sprintf('File not found: %s', $filePath));
            return Command::FAILURE;
        }
        
        try {
            $content = file_get_contents($filePath);
            $data = json_decode($content, true);
            
            if ($data === null) {
                $io->error('Invalid JSON file');
                return Command::FAILURE;
            }
            
            $this->itemProcessorService->processItems($data);
            
            $io->success('Data loaded successfully!');
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}