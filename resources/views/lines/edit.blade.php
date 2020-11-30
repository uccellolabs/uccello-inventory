@php($relatedModules = $module->data->related_modules ?? [])

<div class="row">
    <div class="col s12">
        <div class="card">
            <div class="card-content inventory-container">
                {{-- <span class="card-title">Card Title</span> --}}
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th style="width: 140px">{{ uctrans('inventory::inventory.type') }}</th>
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
                        <tr class="detailed-line-template" data-index="0" style="border-bottom: none; display: none">
                            <td>
                                @if ($relatedModules)
                                <div class="input-field col s12">
                                    <select class="browser-default related-module-selector">
                                        <option value=""></option>
                                        @foreach ($relatedModules as $moduleName => $moduleData)
                                            @php($relatedModule = ucmodule($moduleName))
                                            @continue(empty($relatedModule))
                                            <option value="{{ $relatedModule->name }}" data-search="{{ $moduleData->search ?? '' }}">{{ uctrans($relatedModule->name, $relatedModule) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                            </td>
                            <td>
                                <div class="input-field" style="margin-right: 1rem">
                                    <a href="#inventoryModal" class="btn-floating primary modal-trigger prefix inventory-entity-modal" style="margin-right: 10px; display: none;" data-table="inventory_datatable" data-search="">
                                        <i class="material-icons">search</i>
                                    </a>
                                    <input placeholder="{{ uctrans('lines.product', $module) }}" id="label0" type="text"  style="margin-left: 3.5rem; margin-right: 2rem">
                                    <input type="hidden" id="id0">
                                    <input type="hidden" id="product_uuid0">
                                </div>
                            </td>
                            <td>
                                <div class="input-field">
                                    <input placeholder="{{ trans('inventory::inventory.vat_rate') }}" id="vat_rate0" type="number" value="{{ config('inventory.default_vat_rate') }}" class="right-align" style="width: 60px">
                                </div>
                            </td>
                            <td>
                                <div class="input-field">
                                    <input placeholder="{{ trans('inventory::inventory.unit_price') }}" id="unit_price0" type="number" class="right-align" style="width:90px">
                                </div>
                            </td>
                            <td class="center-align">
                                <button class="btn primary waves-effect price-type"
                                    data-value="excl"
                                    data-trans-excl="{{ trans('inventory::inventory.excl') }}"
                                    data-trans-incl="{{ trans('inventory::inventory.incl') }}">
                                    {{ trans('inventory::inventory.excl') }}
                                </button>
                                <input type="hidden" id="price_type0" value="excl">
                            </td>
                            <td>
                                <div class="input-field">
                                    <input placeholder="{{ trans('inventory::inventory.quantity') }}" id="quantity0" type="number" value="1" class="right-align" style="width:70px">
                                </div>
                            </td>
                            <td>
                                <div class="input-field">
                                    <input placeholder="{{ trans('inventory::inventory.unit') }}" id="unit0" type="text" value="u" style="width:30px">
                                </div>
                            </td>
                            <td>
                                <div class="input-field">
                                    <input placeholder="{{ trans('inventory::inventory.price_excl_tax') }}" id="price_excl_tax0" class="line-total excl-tax right-align" type="number" value="0" @if(!config('inventory.can_enter_ligne_total'))readonly="readonly"@endif style="width:80px">
                                </div>
                            </td>
                            <td>
                                <div class="input-field">
                                    <input placeholder="{{ trans('inventory::inventory.price_incl_tax') }}" id="price_incl_tax0" class="line-total incl-tax right-align" type="number" value="0" @if(!config('inventory.can_enter_ligne_total'))readonly="readonly"@endif style="width:80px">
                                </div>
                            </td>
                            <td>
                                <a href="javascript:void(0)" class="btn-floating red delete-line" style="display: none;">
                                    <i class="material-icons">delete</i>
                                </a>
                            </td>
                        </tr>

                        <tr class="detailed-line-description-template" data-index="0" style="display: none">
                            <td colspan="100%">
                                <textarea id="description0" rows="5" placeholder="{{ trans('inventory::inventory.description') }}" style="padding: 15px; height: 100px"></textarea>
                            </td>
                        </tr>

                        @if ($record->lines)
                            @include('inventory::lines.edit-line')
                        @endif
                    </tbody>
                </table>

                <div class="card-action">
                    <button class="btn green waves-effect add-line">
                        <i class="material-icons left">add</i>{{ trans('inventory::inventory.add_line') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@section('extra-content')
@foreach ($relatedModules as $moduleName => $moduleData)
        @php($relatedModule = ucmodule($moduleName))
        @if (!empty($relatedModule) && Auth::user()->canRetrieve($domain, $relatedModule))
        <div id="inventoryModal_{{ $relatedModule->name }}"
            class="modal"
            data-record-detail-url="{{ ucroute('uccello.detail.record', $domain, $relatedModule, ['%id%']) }}"
            data-label="{{ $_relatedModule->mapping->label ?? '' }}"
            data-description="{{ $_relatedModule->mapping->description ?? '' }}"
            data-vat-rate="{{ $_relatedModule->mapping->vat_rate ?? '' }}"
            data-unit-price="{{ $_relatedModule->mapping->unit_price ?? '' }}"
            data-unit="{{ $_relatedModule->mapping->unit ?? '' }}">
            <div class="modal-content">
                <div class="row">
                    <div class="col s12 modal-body">
                        <div class="row search-related-record" style="margin-top : 10px;">
                            <div class="progress transparent loader" data-table="{{ 'inventory_datatable_'.$relatedModule->name }}" style="margin: 0">
                                <div class="indeterminate green"></div>
                            </div>
                            <div class="col s12">
                                {{-- Table --}}
                                <?php $datatableColumns = Uccello::getDatatableColumns($relatedModule, null, 'related-list'); ?>
                                @include('uccello::modules.default.detail.relatedlists.table', [ 'datatableId' => 'inventory_datatable_'.$relatedModule->name, 'datatableContentUrl' => ucroute('uccello.list.content', $domain, $relatedModule, ['action' => 'select']), 'relatedModule' => $relatedModule, 'searchable' => true ])
                            </div>
                            <div class="loader center-align" data-table="{{ 'inventory_datatable_'.$relatedModule->name }}">
                                <div class="preloader-wrapper big active">
                                    <div class="spinner-layer spinner-primary-only">
                                        <div class="circle-clipper left">
                                            <div class="circle"></div>
                                        </div>
                                        <div class="gap-patch">
                                            <div class="circle"></div>
                                        </div>
                                        <div class="circle-clipper right">
                                            <div class="circle"></div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    {{ uctrans('datatable.loading', $module) }}
                                </div>
                            </div>
                        </div>
                        <div class="row create-related-record" style="display: none">
                            <div class="col s12 create-ajax">
                                {{-- Will be loaded dynamicly through AJAX --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endforeach
@append

@section('extra-script')
    {{ Html::script(mix('js/script.js', 'vendor/uccello/inventory')) }}
@append
