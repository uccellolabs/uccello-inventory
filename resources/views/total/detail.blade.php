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
                            <tr class="total-line-template">
                                <td class="vat-rate">{{ $vatTotal['vat_rate'] }}%</td>
                                <td class="total-excl-tax right-align">{{ $vatTotal['total_excl_tax'] }}</td>
                                <td class="total-vat right-align">{{ $vatTotal['total_vat'] }}</td>
                                <td class="total-incl-tax right-align">{{ $vatTotal['total_incl_tax'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total">
                            @php($totals = $record->totals)
                            <th>{{ trans('inventory::inventory.totals') }}</th>
                            <th class="total-excl-tax right-align">{{ $totals['total_excl_tax'] }}</th>
                            <th class="total-vat right-align">{{ $totals['total_vat'] }}</th>
                            <th class="total-incl-tax right-align">{{ $totals['total_incl_tax'] }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
