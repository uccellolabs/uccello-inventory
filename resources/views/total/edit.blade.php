<div class="row">
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
                        <tr class="total-line-template" style="display: none">
                            <td class="vat-rate"></td>
                            <td class="total-excl-tax right-align"></td>
                            <td class="total-vat right-align"></td>
                            <td class="total-incl-tax right-align"></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="total">
                            <th>{{ trans('inventory::inventory.totals') }}</th>
                            <th class="total-excl-tax right-align">0</th>
                            <th class="total-vat right-align">0</th>
                            <th class="total-incl-tax right-align">0</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
