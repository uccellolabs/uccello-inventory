<div class="row">
    <div class="col s12">
        <div class="card">
            <div class="card-content inventory-container">
                {{-- <span class="card-title">Card Title</span> --}}
                <table class="striped responsive-table inventory-table">
                    <thead>
                        <tr>
                            {{-- <th>Type</th> --}}
                            <th>{{ uctrans('inventory::inventory.product') }}</th>
                            <th>{{ uctrans('inventory::inventory.vat_rate') }}</th>
                            <th>{{ uctrans('inventory::inventory.unit_price') }}</th>
                            <th class="center-align">{!! uctrans('inventory::inventory.price_type') !!}</th>
                            <th>{{ uctrans('inventory::inventory.quantity') }}</th>
                            <th>{{ uctrans('inventory::inventory.unit') }}</th>
                            <th>{{ uctrans('inventory::inventory.price_excl_tax') }}</th>
                            <th>{{ uctrans('inventory::inventory.price_incl_tax') }}</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr class="detailed-line-template" data-index="0" style="display: none">
                            {{-- <td>
                                <div class="input-field col s12">
                                    <select class="browser-default">
                                        <option value="" disabled selected></option>
                                        <option value="1">Option 1</option>
                                        <option value="2">Option 2</option>
                                        <option value="3">Option 3</option>
                                    </select>
                                </div>
                            </td> --}}
                            <td>
                                <div class="input-field">
                                    {{-- <a href="#" class="btn-floating primary waves-effect modal-trigger prefix entity-modal" style="margin-right: 10px" data-table="datatable">
                                        <i class="material-icons">search</i>
                                    </a>
                                    <input placeholder="{{ uctrans('lines.product', $module) }}" id="label0" type="text"  style="margin-left: 3.5rem"> --}}
                                    <input type="hidden" id="id0">
                                    <input type="hidden" id="product_id0">
                                    <input placeholder="{{ trans('inventory::inventory.product') }}" id="label0" type="text">
                                </div>
                            </td>
                            <td>
                                <div class="input-field">
                                    <input placeholder="{{ trans('inventory::inventory.vat_rate') }}" id="vat_rate0" type="number" value="20" class="right-align">
                                </div>
                            </td>
                            <td>
                                <div class="input-field">
                                    <input placeholder="{{ trans('inventory::inventory.unit_price') }}" id="unit_price0" type="number" class="right-align">
                                </div>
                            </td>
                            <td class="center-align">
                                <button class="btn deep-orange price-type"
                                    data-value="excl"
                                    data-trans-excl="{{ trans('inventory::inventory.excl') }}"
                                    data-trans-incl="{{ trans('inventory::inventory.incl') }}">
                                    {{ trans('inventory::inventory.excl') }}
                                </button>
                                <input type="hidden" id="price_type0" value="excl">
                            </td>
                            <td>
                                <div class="input-field">
                                    <input placeholder="{{ trans('inventory::inventory.quantity') }}" id="quantity0" type="number" value="1" class="right-align">
                                </div>
                            </td>
                            <td>
                                <div class="input-field">
                                    <input placeholder="{{ trans('inventory::inventory.unit') }}" id="unit0" type="text" value="u">
                                </div>
                            </td>
                            <td>
                                <div class="input-field">
                                    <input placeholder="{{ trans('inventory::inventory.price_excl_tax') }}" id="price_excl_tax0" class="line-total excl-tax right-align" type="number" value="0">
                                </div>
                            </td>
                            <td>
                                <div class="input-field">
                                    <input placeholder="{{ trans('inventory::inventory.price_incl_tax') }}" id="price_incl_tax0" class="line-total incl-tax right-align" type="number" value="0">
                                </div>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                    </tbody>
                </table>

                <div class="card-action">
                    <button class="btn green add-line">Ajouter</button>
                </div>
            </div>
        </div>
    </div>
</div>

@section('extra-script')
    {{ Html::script(mix('js/app.js', 'vendor/uccello/inventory')) }}
@append
