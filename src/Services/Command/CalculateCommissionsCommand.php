<?php
declare(strict_types=1);

namespace App\Services\Command;

use App\Domain\Exception\DomainException;
use App\Domain\Exception\DomainExceptionInterface;
use App\Domain\Service\TransactionService;
use App\Services\TransactionsFileReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @codeCoverageIgnore
 */
class CalculateCommissionsCommand extends Command
{
    private const FILENAME_ARGUMENT_NAME = 'filename';

    protected static $defaultName = 'app:calculate-commissions';

    private string $filesDirectory;
    private TransactionsFileReader $transactionsFileReader;
    private TransactionService $transactionService;

    public function __construct(
        string $filesDirectory,
        TransactionsFileReader $transactionsFileReader,
        TransactionService $transactionService
    ) {
        parent::__construct();
        $this->filesDirectory = $filesDirectory;
        $this->transactionsFileReader = $transactionsFileReader;
        $this->transactionService = $transactionService;
    }

    protected function configure()
    {
        $this->addArgument(
            self::FILENAME_ARGUMENT_NAME,
            InputArgument::REQUIRED,
            'Filename argument name.'
        );
    }

    /**
     * @throws DomainException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Calculating commissions',
            '============',
            '',
        ]);

        $filename = $input->getArgument(self::FILENAME_ARGUMENT_NAME);

        $output->writeln([
            'Reading input from file',
            '============',
            '',
            sprintf('Filename: %s', $filename)
        ]);

        $filepath = sprintf(
            '%s/%s',
            $this->filesDirectory,
            $filename
        );

        $transactions = $this->transactionsFileReader->getTransactions($filepath);

        foreach ($transactions as $transaction) {
            try {
                $output->writeln([
                    $this->transactionService->calculateCommission($transaction)
                ]);
            } catch (DomainExceptionInterface $exception) {
                throw new DomainException(
                    'Some error has occurred during commissions calculation',
                    0,
                    $exception
                );
            }
        }

        return 0;
    }
}
