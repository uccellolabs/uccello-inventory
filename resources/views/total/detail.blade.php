<div class="row" style="margin-bottom: 70px">
    <div class="col s5 offset-s7">
        <div class="card">
            <div class="card-content">
                <table class="inventory-total">
                    <thead>
                        <tr>
                            <th>{{ trans('inventory::inventory.vat') }}</th>
                            <th class="right-align">{{ trans('inventory::inventory.total_excl_tax') }}</th>
                            <th class="right-align">{{ trans('inventory::inventory.total_vat') }}</th>
                            <th class="right-align">{{ trans('inventory::inventory.total_incl_tax') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($record->vatTotals as $vatTotal)
                            <tr>
                                <td class="vat-rate">{{ number_format($vatTotal['vat_rate'], config('inventory.format.decimals'), config('inventory.format.decimal_point'), config('inventory.format.thousands_separator')) }}%</td>
                                <td class="total-excl-tax right-align">{{ number_format($vatTotal['total_excl_tax'], config('inventory.format.decimals'), config('inventory.format.decimal_point'), config('inventory.format.thousands_separator')) }}</td>
                                <td class="total-vat right-align">{{ number_format($vatTotal['total_vat'], config('inventory.format.decimals'), config('inventory.format.decimal_point'), config('inventory.format.thousands_separator')) }}</td>
                                <td class="total-incl-tax right-align">{{ number_format($vatTotal['total_incl_tax'], config('inventory.format.decimals'), config('inventory.format.decimal_point'), config('inventory.format.thousands_separator')) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total">
                            @php($totals = $record->totals)
                            <th>{{ trans('inventory::inventory.totals') }}</th>
                            <th class="total-excl-tax right-align">{{ number_format($totals['total_excl_tax'], config('inventory.format.decimals'), config('inventory.format.decimal_point'), config('inventory.format.thousands_separator')) }}</th>
                            <th class="total-vat right-align">{{ number_format($totals['total_vat'], config('inventory.format.decimals'), config('inventory.format.decimal_point'), config('inventory.format.thousands_separator')) }}</th>
                            <th class="total-incl-tax right-align">{{ number_format($totals['total_incl_tax'], config('inventory.format.decimals'), config('inventory.format.decimal_point'), config('inventory.format.thousands_separator')) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
