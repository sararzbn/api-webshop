<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class ImportMasterdataTest extends TestCase
{

    use RefreshDatabase;

    public function testImportMasterdataCommand(): void
    {
        Artisan::call('import:masterdata');

        $this->assertDatabaseCount('customers', 10000);

        $this->assertDatabaseCount('products', 100);

    }

}
