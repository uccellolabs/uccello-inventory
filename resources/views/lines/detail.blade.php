<div class="card">
    <div class="card-content inventory-container">
        {{-- <span class="card-title">Card Title</span> --}}
        <table class="inventory-table">
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
                @foreach ($record->lines as $i => $line)
                @php ($lineModule = $record->getLineModule($line))
                @php ($lineProductId = $record->getLineProductId($line))
                @php ($lineDescription = $record->getLineDescription($line))
                <tr style="@if ($lineDescription)border-bottom: none;@endif @if ($i % 2 === 0)background-color: #f9f9f9;@endif">
                    <td>
                        @if ($lineProductId)
                        <a href="{{ ucroute('uccello.detail', $domain, $lineModule, ['id' => $lineProductId]) }}">{{ $record->getLineLabel($line)}}</a>
                        @else
                            {{ $record->getLineLabel($line)}}
                        @endif
                    </td>
                    <td class="right-align">
                        {{ number_format($record->getLineVatRate($line), config('inventory.format.decimals'), config('inventory.format.decimal_point'), config('inventory.format.thousands_separator')) }}%
                    </td>
                    <td class="right-align">
                        {{ number_format($record->getLineUnitPrice($line), config('inventory.format.decimals'), config('inventory.format.decimal_point'), config('inventory.format.thousands_separator')) }}
                    </td>
                    <td class="center-align">
                        {{ $record->getLinePriceType($line, true) }}
                    </td>
                    <td class="right-align">
                        {{ $record->getLineQuantity($line) }}
                    </td>
                    <td>
                        {{ $record->getLineUnit($line) }}
                    </td>
                    <td class="right-align">
                        {{ number_format($record->getLineTotalExclTax($line), config('inventory.format.decimals'), config('inventory.format.decimal_point'), config('inventory.format.thousands_separator')) }}
                    </td>
                    <td class="right-align">
                        {{ number_format($record->getLineTotalInclTax($line), config('inventory.format.decimals'), config('inventory.format.decimal_point'), config('inventory.format.thousands_separator')) }}
                    </td>
                    <td>&nbsp;</td>
                </tr>
                @if ($lineDescription)
                <tr @if ($i % 2 === 0)style="background-color: #f9f9f9;"@endif>
                    <td colspan="100%" style="padding: 10px 25px">{!! nl2br($lineDescription) !!}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
