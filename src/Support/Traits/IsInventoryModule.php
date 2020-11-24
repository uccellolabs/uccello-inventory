<?php

namespace Uccello\Inventory\Support\Traits;

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
    //         'product_id' => 'product_id',
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

    public static function bootIsInventoryModule()
    {
        static::created(function ($model) {
            $model->saveLines();
        });

        static::updated(function ($model) {
            $model->saveLines();
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

    protected function saveHeaderTotals()
    {
        $this->{$this->inventoryMapping['header']['total_excl_tax']} = (float) request('total_excl_tax');
        $this->{$this->inventoryMapping['header']['total_incl_tax']} = (float) request('total_incl_tax');
        $this->save();
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
            $inventoryLine->{$this->inventoryMapping['lines']['product_id']} = $line['product_id'];
            $inventoryLine->{$this->inventoryMapping['lines']['label']} = $line['label'];
            // $inventoryLine->{$this->inventoryMapping['lines']['description']} = $line['description'];
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

    /**
     * Returns fields mapping for a module.
     *
     * @param \Uccello\Core\Models\Module $module
     *
     * @return Stdclass|null
     */
    protected function getFieldsMapping(Module $module)
    {
        $fieldsMapping = null;

        $detailedModules = $this->module->data->detailed_modules ?? null;
        if (is_array($detailedModules)) {
            foreach ($detailedModules as $detailedModule) {
                if ($detailedModule->name === $module->name) {
                    $fieldsMapping = $detailedModule->mapping;
                    break;
                }
            }
        }

        return $fieldsMapping;
    }

    public function getLineProductId($line)
    {
        return $line->{$this->inventoryMapping['lines']['product_id']};
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
        return $line->{$this->inventoryMapping['lines']['vat_rate']};
    }

    public function getLinePriceType($line)
    {
        return trans('inventory::inventory.' . $line->{$this->inventoryMapping['lines']['price_type']});
    }

    public function getLineQuantity($line) : float
    {
        return $line->{$this->inventoryMapping['lines']['quantity']};
    }

    public function getLineUnit($line)
    {
        return $line->{$this->inventoryMapping['lines']['unit']};
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
        return $line->{$this->inventoryMapping['lines']['price_excl_tax']};
    }

    public function getLineTotalInclTax($line) : float
    {
        return $line->{$this->inventoryMapping['lines']['price_incl_tax']};
    }

    public function getLineVatAmount($line) : float
    {
        return $this->getLineTotalInclTax($line) - $this->getLineTotalExclTax($line);
    }
}
