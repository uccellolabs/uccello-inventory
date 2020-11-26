<?php

namespace Uccello\Inventory\Support\Traits;

use Uccello\Core\Models\Entity;
use Uccello\Core\Models\Module;

trait IsInventoryModule
{
    // Default mapping
    // protected $inventoryMapping = [
    //     'header' => [
    //         'total_excl_tax' => 'total_excl_tax',
    //         'total_incl_tax' => 'total_incl_tax',
    //     ],
    //     'lines' => [
    //         'related_id' => 'related_id',
    //         'product_uuid' => 'product_uuid',
    //         'label' => 'label',
    //         'description' => 'description',
    //         'vat_rate' => 'vat_rate',
    //         'unit_price' => 'unit_price',
    //         'price_type' => 'price_type',
    //         'quantity' => 'quantity',
    //         'unit' => 'unit',
    //         'price_excl_tax' => 'price_excl_tax',
    //         'price_incl_tax' => 'price_incl_tax',
    //         'sequence' => 'sequence',
    //     ]
    // ];

    protected $stopUpdateEvent = false;

    public static function bootIsInventoryModule()
    {
        static::created(function ($model) {
            $model->saveLines();
            $model->saveHeaderTotals();
        });

        static::updated(function ($model) {
            if (!$model->stopUpdateEvent) {
                $model->saveLines();
                $model->saveHeaderTotals();
            }
        });
    }

    public function getVatTotalsAttribute()
    {
        $vatTotals = [];

        foreach ($this->lines as $line) {
            $vatRate = $this->getLineVatRate(($line));

            if (empty($vatTotals[$vatRate])) {
                $vatTotals[$vatRate] = [
                    'vat_rate' => $vatRate,
                    'total_excl_tax' => 0,
                    'total_vat' => 0,
                    'total_incl_tax' => 0
                ];
            }

            $vatTotals[$vatRate]['total_excl_tax'] += $this->getLineTotalExclTax($line);
            $vatTotals[$vatRate]['total_vat'] += $this->getLineVatAmount($line);
            $vatTotals[$vatRate]['total_incl_tax'] += $this->getLineTotalInclTax($line);
        }

        return $vatTotals;
    }

    public function getTotalsAttribute()
    {
        $totals = [
            'total_excl_tax' => 0,
            'total_vat' => 0,
            'total_incl_tax' => 0
        ];

        foreach ($this->vatTotals as $vatTotal) {
            $totals['total_excl_tax'] += $vatTotal['total_excl_tax'];
            $totals['total_vat'] += $vatTotal['total_vat'];
            $totals['total_incl_tax'] += $vatTotal['total_incl_tax'];
        }

        return $totals;
    }

    public function getLineModule($line)
    {
        $module = null;

        $uuid = $line->{$this->inventoryMapping['lines']['product_uuid']};
        if ($uuid) {
            $entity = Entity::find($uuid);

            if ($entity) {
                $module = Module::find($entity->module_id);
            }
        }

        return $module;
    }

    public function getLineProduct($line)
    {
        $product = null;

        $uuid = $line->{$this->inventoryMapping['lines']['product_uuid']};
        if ($uuid) {
            $entity = Entity::find($uuid);

            if ($entity) {
                $module = Module::find($entity->module_id);
                if ($module) {
                    $modelClass = $module->model_class;
                    $product = $modelClass::find($entity->record_id);
                }
            }
        }

        return $product;
    }

    public function getLineProductId($line)
    {
        $productId = null;

        $product = $this->getLineProduct($line);
        if ($product) {
            $productId = $product->getKey();
        }

        return $productId;
    }

    public function getLineProductUuid($line)
    {
        return $line->{$this->inventoryMapping['lines']['product_uuid']};
    }

    public function getLineLabel($line)
    {
        return $line->{$this->inventoryMapping['lines']['label']};
    }

    public function getLineDescription($line)
    {
        return $line->{$this->inventoryMapping['lines']['description']};
    }

    public function getLineUnitPrice($line) : float
    {
        return $line->{$this->inventoryMapping['lines']['unit_price']};
    }

    public function getLineVatRate($line): float
    {
        return $line->{$this->inventoryMapping['lines']['vat_rate']} ?? config('inventory.default_vat_rate');
    }

    public function getLinePriceType($line, $translate = false)
    {
        $lignePriceType = $line->{$this->inventoryMapping['lines']['price_type']} ?? 'excl';
        if ($translate) {
            $priceType = trans('inventory::inventory.' . $lignePriceType);
        } else {
            $priceType = $lignePriceType;
        }

        return $priceType;
    }

    public function getLineQuantity($line) : float
    {
        return $line->{$this->inventoryMapping['lines']['quantity']} ?? 1;
    }

    public function getLineUnit($line)
    {
        return $line->{$this->inventoryMapping['lines']['unit']} ?? config('inventory.default_unit');
    }

    public function getLineUnitPriceExclTax($line) : float
    {
        $priceType = $line->{$this->inventoryMapping['lines']['price_type']};
        $unitPrice = $line->{$this->inventoryMapping['lines']['unit_price']};
        $vatRate = $line->{$this->inventoryMapping['lines']['vat_rate']};

        if ($priceType === 'incl') {
            $unitPriceExclTax = $unitPrice / (1 + ($vatRate / 100));
        } else {
            $unitPriceExclTax = $unitPrice;
        }

        return $unitPriceExclTax;
    }

    public function getLineUnitPriceInclTax($line) : float
    {
        $priceType = $line->{$this->inventoryMapping['lines']['price_type']};
        $unitPrice = $line->{$this->inventoryMapping['lines']['unit_price']};
        $vatRate = $line->{$this->inventoryMapping['lines']['vat_rate']};

        if ($priceType === 'excl') {
            $unitPriceInclTax = $unitPrice * (1 + ($vatRate / 100));
        } else {
            $unitPriceInclTax = $unitPrice;
        }

        return $unitPriceInclTax;
    }

    public function getLineTotalExclTax($line) : float
    {
        return $line->{$this->inventoryMapping['lines']['price_excl_tax']} ?? 0;
    }

    public function getLineTotalInclTax($line) : float
    {
        return $line->{$this->inventoryMapping['lines']['price_incl_tax']} ?? 0;
    }

    public function getLineVatAmount($line) : float
    {
        return $this->getLineTotalInclTax($line) - $this->getLineTotalExclTax($line) ?? 0;
    }

    protected function saveHeaderTotals()
    {
        $save = false;

        $totals = $this->totals;
        if ($this->{$this->inventoryMapping['header']['total_excl_tax']}) {
            $this->{$this->inventoryMapping['header']['total_excl_tax']} = (float) $totals['total_excl_tax'];
            $save = true;
        }

        if ($this->{$this->inventoryMapping['header']['total_incl_tax']}) {
            $this->{$this->inventoryMapping['header']['total_incl_tax']} = (float) $totals['total_incl_tax'];
            $save = true;
        }

        if ($save) {
            $this->stopUpdateEvent = true; // Mandatory else the event is launch a lot of times
            $this->save();
            $this->stopUpdateEvent = false;
        }
    }

    protected function saveLines()
    {
        $lineIds = [];

        foreach (request('lines') as $i => $line) {
            // Add line id
            if ($line['id']) {
                $lineIds[] = $line['id'];
            }

            $inventoryLine = $this->lines()->findOrNew($line['id']);
            $inventoryLine->{$this->inventoryMapping['lines']['related_id']} = $this->getKey();
            $inventoryLine->{$this->inventoryMapping['lines']['product_uuid']} = $line['product_uuid'];
            $inventoryLine->{$this->inventoryMapping['lines']['label']} = $line['label'];
            $inventoryLine->{$this->inventoryMapping['lines']['description']} = $line['description'];
            $inventoryLine->{$this->inventoryMapping['lines']['vat_rate']} = $line['vat_rate'];
            $inventoryLine->{$this->inventoryMapping['lines']['unit_price']} = $line['unit_price'];
            $inventoryLine->{$this->inventoryMapping['lines']['price_type']} = $line['price_type'];
            $inventoryLine->{$this->inventoryMapping['lines']['quantity']} = $line['quantity'];
            $inventoryLine->{$this->inventoryMapping['lines']['unit']} = $line['unit'];
            $inventoryLine->{$this->inventoryMapping['lines']['price_excl_tax']} = $line['price_excl_tax'];
            $inventoryLine->{$this->inventoryMapping['lines']['price_incl_tax']} = $line['price_incl_tax'];
            $inventoryLine->{$this->inventoryMapping['lines']['sequence']} = $i;
            $this->lines()->save($inventoryLine);

            $lineIds[] = $inventoryLine->getKey();
        }

        // Delete removed lines
        $this->lines()->whereNotIn('id', $lineIds)->delete(); // TODO: replace id by getKeyName()
    }
}
