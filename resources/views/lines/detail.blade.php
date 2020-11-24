
<div class="card">
    <div class="card-content inventory-container">
        {{-- <span class="card-title">Card Title</span> --}}
        <table class="striped responsive-table inventory-table">
            <thead>
                <tr>
                    {{-- <th>Type</th> --}}
                    <th>{{ uctrans('inventory::inventory.product') }}</th>
                    <th class="right-align">{{ uctrans('inventory::inventory.vat_rate') }}</th>
                    <th class="right-align">{{ uctrans('inventory::inventory.unit_price') }}</th>
                    <th class="center-align">{!! uctrans('inventory::inventory.price_type') !!}</th>
                    <th class="right-align">{{ uctrans('inventory::inventory.quantity') }}</th>
                    <th>{{ uctrans('inventory::inventory.unit') }}</th>
                    <th class="right-align">{{ uctrans('inventory::inventory.price_excl_tax') }}</th>
                    <th class="right-align">{{ uctrans('inventory::inventory.price_incl_tax') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($record->lines as $line)
                <tr>
                    <td>
                        {{ $record->getLineLabel($line)}}
                    </td>
                    <td class="right-align">
                        {{ $record->getLineVatRate($line)}}%
                    </td>
                    <td class="right-align">
                        {{ $record->getLineUnitPrice($line)}}
                    </td>
                    <td class="center-align">
                        {{ $record->getLinePriceType($line)}}
                    </td>
                    <td class="right-align">
                        {{ $record->getLineQuantity($line)}}
                    </td>
                    <td>
                        {{ $record->getLineUnit($line)}}
                    </td>
                    <td class="right-align">
                        {{ $record->getLineTotalExclTax($line)}}
                    </td>
                    <td class="right-align">
                        {{ $record->getLineTotalInclTax($line)}}
                    </td>
                    <td>&nbsp;</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@section('extra-script')
    {{ Html::script(mix('js/app.js', 'vendor/uccello/inventory')) }}
@append
