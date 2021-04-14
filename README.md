# Uccello Inventory

Thanks to this package it becomes very easy to transform an existing module into an accounting module (e.g. quote, order, invoice).



## Install package

You can easily install this package using the following command line:

```bash
$ composer require uccello/inventory

$ php artisan vendor:publish --tag inventory-assets
$ php artisan vendor:publish --tag inventory-config
```

You can modify `config/inventory.php` and adapt the config.


## Transform a classic module into an Inventory module

### IsInventoryModule trait

Add the following lines to the model related to the module you want to transform into an Inventory Module. Here an example with `Order` module

```php
<?php

  ....

  use Uccello\Inventory\Support\Traits\IsInventoryModule;

  class Order
  {
    use IsInventoryModule;

    protected $inventoryMapping = [
        'header' => [
            'total_excl_tax' => 'total_excl_tax', // Replace the value by the name of the field in your module (for automatic update)
            'total_incl_tax' => 'total_incl_tax', // Replace the value by the name of the field in your module (for automatic update)
            'total_vat' => 'total_vat', // Replace the value by the name of the field in your module (for automatic update)
        ],

      	// Replace all values by your columns names
        'lines' => [
            'related_id' => 'order_id',
            'product_uuid' => 'product_uuid',
            'label' => 'label',
            'description' => 'description',
            'vat_rate' => 'vat_rate',
            'unit_price' => 'unit_price',
            'price_type' => 'price_type',
            'quantity' => 'qty',
            'unit' => 'unit',
            'price_excl_tax' => 'price_excl_tax',
            'price_incl_tax' => 'price_incl_tax',
            'sequence' => 'sequence',
        ],
    ];

    public function lines() // This relation must be called "lines"
    {
        return $this->hasMany(OrderLine::class)->orderBy('sequence'); // Replace OrderLine by the model class you use for saving the lines
    }

    // ...
  }
```



 ### Create a migration

Create a new migration and create the table that will contains all lines

```php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Uccello\Core\Models\Module;

class AlterOrderLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('order_lines', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id')->nullable();
            $table->unsignedInteger('product_id')->nullable();
            $table->string('product_uuid')->nullable();
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->decimal('vat_rate', 5, 2)->nullable();
            $table->decimal('unit_price', 13, 2)->nullable();
            $table->string('price_type')->nullable();
            $table->decimal('qty', 13, 2)->nullable();
            $table->decimal('price', 13, 2)->nullable();
            $table->string('unit')->nullable();
            $table->decimal('price_excl_tax', 13, 2)->nullable();
            $table->decimal('price_incl_tax', 13, 2)->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('product_id')->references('id')->on('crm_products');
        });

        // Update module data
        $module = Module::where('name', 'order')->first();
        $module->data = [
            'private' => true,
            'header' => [
                'total_excl_tax' => 'total_excl_tax',
                'total_incl_tax' => 'total_incl_tax',
                'total_vat' => 'total_vat',
            ],
            'related_modules' => [ // You can add as many modules as you want
                'product' =>  [ // Name of the related module
                    'search' => 'name', // The name of the field you want use to search from the inventory line
                  	// Replace mapping values with the name of product fields if exist
                    'mapping' => [
                        'unit_price' => 'unit_price',
                      	'label' => 'label',
                      	'description' => 'description',
                      	'vat_rate' => 'vat_rate',
                      	'unit_price' => 'unit_price',
                      	'unit' => 'unit'
                    ],
                ]
            ],
        ];
        $module->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_lines');

        // Update module data
        $module = Module::where('name', 'order')->first();
        $module->data = null;
        $module->save();
    }
}

```



### Override views

- Create or edit your module edit view (e.g. `resources/views/uccello/modules/order/edit/main.blade.php`):

  ```html
  @extends('uccello::modules.default.edit.main')

  @section('other-blocks')
      @include('inventory::lines.edit')

      @include('inventory::total.edit')
  @endsection
  ```

- Create or edit your module detail view (e.g. `resources/views/uccello/modules/order/detail/main.blade.php`):

  ```html
  @extends('uccello::modules.default.detail.main')

  @section('other-blocks')
      @include('inventory::lines.detail')

      @include('inventory::total.detail')
  @endsection
  ```



## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
