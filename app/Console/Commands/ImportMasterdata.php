<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ImportMasterdata extends Command
{

    /**
     * @var string
     */
    protected $signature = 'import:masterdata';
    /**
     * @var string
     */
    protected $description = 'Import masterdata from CSV files';

    /**
     * @return void
     */
    public function handle(): void
    {
        try {
            $this->importData('Customers', 'customers.csv', 'ID', [
                'job_title' => 'Job Title',
                'email' => 'Email Address',
                'first_name_last_name' => 'FirstName LastName',
                'registered_since' => 'registered_since',
                'phone' => 'phone',
            ]);

            $this->importData('Products', 'products.csv', 'ID', [
                'product_name' => 'productname',
                'price' => 'price',
            ]);

            $this->info('Masterdata import completed successfully.');

        } catch (Exception $e) {
            Log::error('Error during masterdata import: ' . $e->getMessage());
            $this->error('Failed to import masterdata. Check the logs for details.');
        }
    }

    /**
     * @param $entity
     * @param $filename
     * @param $primaryKey
     * @param $columns
     * @return void
     * @throws Exception
     */
    private function importData($entity, $filename, $primaryKey, $columns): void
    {
        $filePath = "app/uploads/$filename";
        $this->info("Importing $entity from $filePath...");

        $handle = fopen(storage_path($filePath), 'r');
        $header = fgetcsv($handle);

        $count = 0;

        while ($data = fgetcsv($handle)) {
            $record = array_combine($header, $data);

            try {
                $formattedDate = $entity === 'Customers' ? $this->parseDateString($record['registered_since']) : null;
                $model = $entity === 'Customers' ? Customer::class : Product::class;

                $model::create(array_merge([
                    'id' => $record[$primaryKey],
                ], $this->mapColumns($record, $columns, $formattedDate)));

                $count++;

                if ($count % 100 === 0) {
                    $this->info("Processed $count $entity");
                }
            } catch (Exception $e) {
                Log::error("Error importing $entity: " . $e->getMessage());
                $this->error("Error importing $entity: " . $e->getMessage());
                throw $e;
            }
        }

        fclose($handle);
        $this->info("$entity imported successfully. Total: $count");
    }

    /**
     * @param $rawDate
     * @return string|null
     */
    private function parseDateString($rawDate): ?string
    {
        try {
            $carbonDate = Carbon::createFromFormat('l, F j, Y', $rawDate);

            return $carbonDate->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            Log::error("Error Date Format: " . $e->getMessage());
        }

        return null;
    }

    /**
     * @param $record
     * @param $columns
     * @param $formattedDate
     * @return array
     */
    private function mapColumns($record, $columns, $formattedDate): array
    {
        $mapped = [];
        foreach ($columns as $dbColumn => $csvColumn) {
            $mapped[$dbColumn] = $csvColumn === 'registered_since' ? $formattedDate : $record[$csvColumn];
        }

        return $mapped;
    }

}
