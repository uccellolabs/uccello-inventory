@foreach ($record->lines as $i => $line)
@php ($index = $i + 1)
@php ($lineModule = $record->getLineModule($line))
<tr class="detailed-line" data-index="{{ $index }}" style="border-bottom: none">
    <td>
        @if ($relatedModules)
        <div class="input-field col s12">
            <select class="browser-default related-module-selector">
                <option value=""></option>
                @foreach ($relatedModules as $_relatedModule)
                    @php($relatedModule = ucmodule($_relatedModule->name))
                    <option value="{{ $relatedModule->name }}"
                        data-search="{{ $_relatedModule->search ?? '' }}"
                        @if ($lineModule && $lineModule->name === $relatedModule->name)selected="selected"@endif>{{ uctrans($relatedModule->name, $relatedModule) }}</option>
                @endforeach
            </select>
        </div>
        @endif
    </td>
    <td>
        <div class="input-field" style="margin-right: 1rem">
            <a href="#inventoryModal" class="btn-floating primary modal-trigger prefix inventory-entity-modal" style="margin-right: 10px; @if(!$lineModule)display: none;@endif" data-table="inventory_datatable" data-search="">
                <i class="material-icons">search</i>
            </a>
            <input placeholder="{{ uctrans('lines.product', $module) }}" id="label{{ $index }}" name="lines[{{ $index }}][label]" type="text" value="{{ $record->getLineLabel($line) }}" style="margin-left: 3.5rem; margin-right: 2rem">
            <input type="hidden" id="id{{ $index }}" name="lines[{{ $index }}][id]" value="{{ $line->getKey() }}">
            <input type="hidden" id="product_uuid{{ $index }}" name="lines[{{ $index }}][product_uuid]" value="{{ $record->getLineProductUuid($line) }}">
        </div>
    </td>
    <td>
        <div class="input-field">
            <input placeholder="{{ trans('inventory::inventory.vat_rate') }}" id="vat_rate{{ $index }}"name="lines[{{ $index }}][vat_rate]"  type="number" value="{{ $record->getLineVatRate($line) }}" class="right-align" style="width: 60px">
        </div>
    </td>
    <td>
        <div class="input-field">
            <input placeholder="{{ trans('inventory::inventory.unit_price') }}" id="unit_price{{ $index }}"name="lines[{{ $index }}][unit_price]"  type="number" class="right-align" value="{{ $record->getLineUnitPrice($line) }}" style="width:90px">
        </div>
    </td>
    <td class="center-align">
        @php($priceType = $record->getLinePriceType($line))
        <button class="btn primary waves-effect price-type"
            data-value="{{ $priceType }}"
            data-trans-excl="{{ trans('inventory::inventory.excl') }}"
            data-trans-incl="{{ trans('inventory::inventory.incl') }}">
            @if ($priceType === 'incl'){{ trans('inventory::inventory.incl') }}@else{{ trans('inventory::inventory.excl') }}@endif
        </button>
        <input type="hidden" id="price_type{{ $index }}"name="lines[{{ $index }}][price_type]" value="{{ $record->getLinePriceType($line) }}">
    </td>
    <td>
        <div class="input-field">
            <input placeholder="{{ trans('inventory::inventory.quantity') }}" id="quantity{{ $index }}"name="lines[{{ $index }}][quantity]"  type="number"  value="{{ $record->getLineQuantity($line) }}" class="right-align" style="width:70px">
        </div>
    </td>
    <td>
        <div class="input-field">
            <input placeholder="{{ trans('inventory::inventory.unit') }}" id="unit{{ $index }}" name="lines[{{ $index }}][unit]" type="text" value="{{ $record->getLineUnit($line) }}" style="width:30px">
        </div>
    </td>
    <td>
        <div class="input-field">
            <input placeholder="{{ trans('inventory::inventory.price_excl_tax') }}" id="price_excl_tax{{ $index }}" name="lines[{{ $index }}][price_excl_tax]" class="line-total excl-tax right-align" type="number" value="{{ $record->getLineTotalExclTax($line) }}" @if(!config('inventory.can_enter_ligne_total'))readonly="readonly"@endif style="width:80px">
        </div>
    </td>
    <td>
        <div class="input-field">
            <input placeholder="{{ trans('inventory::inventory.price_incl_tax') }}" id="price_incl_tax{{ $index }}" name="lines[{{ $index }}][price_incl_tax]" class="line-total incl-tax right-align" type="number" value="{{ $record->getLineTotalInclTax($line) }}" @if(!config('inventory.can_enter_ligne_total'))readonly="readonly"@endif style="width:80px">
        </div>
    </td>
    <td>
        <a href="javascript:void(0)" class="btn-floating red delete-line">
            <i class="material-icons">delete</i>
        </a>
    </td>
</tr>

<tr class="detailed-line-description" data-index="{{ $index }}">
    <td colspan="100%">
        <textarea id="description{{ $index }}" rows="5" placeholder="{{ trans('inventory::inventory.description') }}" name="lines[{{ $index }}][description]" style="padding: 15px; height: 100px">{{ $record->getLineDescription($line) }}</textarea>
    </td>
</tr>
@endforeach
