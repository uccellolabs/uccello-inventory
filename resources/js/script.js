import {Datatable} from 'uccello-datatable';

class Inventory {
    constructor() {
        this.container = $('.inventory-container');
        this.table = $('.inventory-table');
        this.maxId = $('tr.detailed-line').length + 1;

        this.initListeners();

        if ($('tbody tr:not(.detailed-line-template):not(.detailed-line-description-template)', this.table).length > 0) {
            this.calculateTotal();
        } else {
            this.addLine();
        }
    }

    initListeners() {
        this.initAddLineListener();
        this.initLineListeners();
    }

    initAddLineListener() {
        $('.add-line', this.container).off('click');
        $('.add-line', this.container).on('click', (event) => {
            event.preventDefault();

            this.addLine();
        });
    }

    initRemoveLineListener() {
        $('.delete-line', this.container).off('click');
        $('.delete-line', this.container).on('click', (event) => {
            event.preventDefault();
            let line = $(event.currentTarget).parents('tr:first');
            let lineIndex = $(line).attr('data-index');

            $(`tr[data-index="${lineIndex}"]`).remove();

            this.calculateTotal();
        });

    }

    initLineListeners() {
        this.initButtonClickListener();
        this.initInputChangeListener();
        this.initInputClickListener();
        this.initRelatedModuleChangeListener();
        this.initRelatedEntityClickListener();
        this.initRemoveLineListener();
    }

    initButtonClickListener() {
        $('button.price-type', this.table).off('click');
        $('button.price-type', this.table).on('click', (event) => {
            event.preventDefault();

            let element = $(event.currentTarget);
            let line = element.parents('tr:first');
            let lineIndex = $(line).attr('data-index');

            let priceType = $(event.currentTarget).attr('data-value');

            if (priceType === 'excl') {
                element.attr('data-value', 'incl');
                $(`#price_type${lineIndex}`).val('incl');
                element.text(element.attr('data-trans-incl'));
            } else {
                element.attr('data-value', 'excl');
                $(`#price_type${lineIndex}`).val('excl');
                element.text(element.attr('data-trans-excl'));
            }

            this.calculateLineTotal(line);
        });
    }

    initInputChangeListener() {
        $('input:not(.line-total)', this.table).off('change');
        $('input:not(.line-total)', this.table).on('change', (event) => {
            let line = $(event.currentTarget).parents('tr:first');
            this.calculateLineTotal(line)
        });

        $('input.line-total', this.table).off('change');
        $('input.line-total', this.table).on('change', (event) => {
            let element = $(event.currentTarget);
            let line = $(event.currentTarget).parents('tr:first');

            if (element.hasClass('excl-tax')) {
                this.calculateLineUnitPriceFromExclTaxTotal(line)
            } else if (element.hasClass('incl-tax')) {
                this.calculateLineUnitPriceFromInclTaxTotal(line)
            }
        });
    }

    initInputClickListener() {
        $('input', this.table).off('click');
        $('input', this.table).on('click', (event) => {
            $(event.currentTarget).trigger('select');
        });
    }

    initRelatedModuleChangeListener() {
        $('.related-module-selector', this.table).off('change');
        $('.related-module-selector', this.table).on('change', (event) => {
            const element = $(event.currentTarget);
            const line = element.parents('tr:first');
            const lineIndex = $(line).attr('data-index');
            const relatedModule = element.val();

            if (element.val()) {
                $('.inventory-entity-modal', line).show();
                // $(`#label${lineIndex}`).css('margin-left', '3.5rem');
            } else {
                $('.inventory-entity-modal', line).hide();
                // $(`#label${lineIndex}`).css('margin-left', 0);
            }

            // Empty fields
            $(`#product_uuid${lineIndex}`).val('');
            $(`#label${lineIndex}`).val('');
            $(`#vat_rate${lineIndex}`).prop('disabled', false);

            $('a.inventory-entity-modal', line)
                .attr('href', '#inventoryModal_'+relatedModule)
                .attr('data-table', 'inventory_datatable_'+relatedModule)
                .attr('data-search', $('option:selected', element).attr('data-search'));
        });
    }

    initRelatedEntityClickListener() {
        $('a.inventory-entity-modal').off('click');
        $('a.inventory-entity-modal').on('click', event => {
            const element = $(event.currentTarget);
            const line = element.parents('tr:first');
            const lineIndex = $(line).attr('data-index');
            const tableId = element.attr('data-table');
            const searchField = element.attr('data-search');

            const searchValue = $(`#label${lineIndex}`).val();
            $(`table#${tableId} th input`).val('');
            $(`table#${tableId} th[data-field="${searchField}"] input`).val(searchValue);

            if (searchValue) {
                $(`table#${tableId} .clear-search`).show();
            } else {
                $(`table#${tableId} .clear-search`).hide();
            }

            // Remove lines else the rowClickCallback may not be initialized
            $('table#'+tableId+' tr.record').remove();

            // Click callback
            let rowClickCallback = (event, datatable, recordId, recordLabel) => {
                event.preventDefault();
                const modal = $(datatable.table).parents('.modal:first');

                let url = $(modal).data('record-detail-url').replace('%id%', recordId) + '?';

                // Add form input values in the URL
                $('.input-field :input').each((index, el) => {
                    let element = $(el);
                    let param = element.attr('name');
                    let value = element.val();

                    if (param && value) {
                        url += param+'='+value+'&';
                    }
                });

                $.get(url).then(record => {
                    // Uuid
                    $(`#product_uuid${lineIndex}`).val(record.uuid);

                    // Label
                    let label = $(modal).data('label');
                    if (label) {
                        $(`#label${lineIndex}`).val(record[label]);
                    } else {
                        $(`#label${lineIndex}`).val(recordLabel);
                    }

                    // Description
                    let description = $(modal).data('description');
                    if (description) {
                        $(`#description${lineIndex}`).val(record[description]);
                    }

                    // VAT Rate
                    let vatRate = $(modal).data('vat-rate');
                    if (vatRate) {
                        $(`#vat_rate${lineIndex}`).val(record[vatRate]).prop('disabled', true);
                    } else {
                        $(`#vat_rate${lineIndex}`).prop('disabled', false);
                    }

                    // Unit Price
                    let unitPrice = $(modal).data('unit-price');
                    if (unitPrice) {
                        $(`#unit_price${lineIndex}`).val(record[unitPrice]);
                    }

                    // Unit
                    let unit = $(modal).data('unit');
                    if (unit) {
                        $(`#unit${lineIndex}`).val(record[unit]);
                    }

                    // Dispatch custom event with line and product information
                    let customEvent = new CustomEvent('inventory.product.retrieved', {
                        detail: {
                            line: line,
                            lineIndex: lineIndex,
                            product: record
                        }
                    })
                    dispatchEvent(customEvent);

                    // Calculate line total
                    this.calculateLineTotal(line);
                });

                $(modal).modal('close');
            }

            let el = $('table#'+tableId)
            let datatable = new Datatable()
            datatable.init(el, function(){}, rowClickCallback)
            datatable.makeQuery();
        })
    }

    addLine() {
        let template = $('.detailed-line-template:first', this.table);

        // Clone line
        let line = template.clone().show();
        line.removeClass('detailed-line-template').addClass('detailed-line')
        line.attr('data-index', this.maxId);

        // Replace ids
        $('#id0', line).attr('id', `id${this.maxId}`).attr('name', `lines[${this.maxId}][id]`);
        $('#product_uuid0', line).attr('id', `product_uuid${this.maxId}`).attr('name', `lines[${this.maxId}][product_uuid]`);
        $('#label0', line).attr('id', `label${this.maxId}`).attr('name', `lines[${this.maxId}][label]`);
        $('#vat_rate0', line).attr('id', `vat_rate${this.maxId}`).attr('name', `lines[${this.maxId}][vat_rate]`);
        $('#unit_price0', line).attr('id', `unit_price${this.maxId}`).attr('name', `lines[${this.maxId}][unit_price]`);
        $('#quantity0', line).attr('id', `quantity${this.maxId}`).attr('name', `lines[${this.maxId}][quantity]`);
        $('#price_type0', line).attr('id', `price_type${this.maxId}`).attr('name', `lines[${this.maxId}][price_type]`);
        $('#unit0', line).attr('id', `unit${this.maxId}`).attr('name', `lines[${this.maxId}][unit]`);
        $('#price_excl_tax0', line).attr('id', `price_excl_tax${this.maxId}`).attr('name', `lines[${this.maxId}][price_excl_tax]`);
        $('#price_incl_tax0', line).attr('id', `price_incl_tax${this.maxId}`).attr('name', `lines[${this.maxId}][price_incl_tax]`);
        $('.delete-line', line).show();

        // Append line to table
        $('tbody', this.table).append(line);

        // Add description
        let descriptionTemplate = $('.detailed-line-description-template:first', this.table);

        // Clone line
        let descriptionLine = descriptionTemplate.clone().show();
        descriptionLine.removeClass('detailed-line-description-template').addClass('detailed-line-description')
        descriptionLine.attr('data-index', this.maxId);

        $('#description0', descriptionLine).attr('id', `description${this.maxId}`).attr('name', `lines[${this.maxId}][description]`);

        // Append description line to table
        $('tbody', this.table).append(descriptionLine);


        this.initLineListeners();

        // Increment max id
        this.maxId++;
    }

    calculateLineTotal(line) {
        let lineIndex = $(line).attr('data-index');
        let vatRate = $(`#vat_rate${lineIndex}`).val() || 0;
        let unitPrice = $(`#unit_price${lineIndex}`).val() || 0;
        let priceType = $(`#price_type${lineIndex}`).val();
        let quantity = $(`#quantity${lineIndex}`).val() || 1;

        let priceExclTax, priceInclTax;
        if (priceType === 'incl') {
            priceExclTax = unitPrice * quantity / (1 + (vatRate / 100));
            priceInclTax = unitPrice * quantity;
        } else {
            priceExclTax = unitPrice * quantity;
            priceInclTax = unitPrice * quantity * (1 + (vatRate / 100));
        }

        priceExclTax = Math.round(priceExclTax * 100) / 100;
        priceInclTax = Math.round(priceInclTax * 100) / 100;

        $(`#price_excl_tax${lineIndex}`).val(priceExclTax);
        $(`#price_incl_tax${lineIndex}`).val(priceInclTax);

        this.calculateTotal();
    }

    calculateLineUnitPriceFromExclTaxTotal(line) {
        let lineIndex = $(line).attr('data-index');
        let vatRate = $(`#vat_rate${lineIndex}`).val() || 0;
        let priceType = $(`#price_type${lineIndex}`).val();
        let quantity = $(`#quantity${lineIndex}`).val() || 1;
        let priceExclTax = $(`#price_excl_tax${lineIndex}`).val() || 0;

        let unitPrice;
        let unitPriceExclTax = priceExclTax / quantity;
        if (priceType === 'incl') {
            unitPrice = unitPriceExclTax + (unitPriceExclTax * vatRate / 100)
        } else {
            unitPrice = unitPriceExclTax;
        }

        unitPrice = Math.round(unitPrice * 100) / 100;

        $(`#unit_price${lineIndex}`).val(unitPrice).trigger('change');
    }

    calculateLineUnitPriceFromInclTaxTotal(line) {
        let lineIndex = $(line).attr('data-index');
        let vatRate = $(`#vat_rate${lineIndex}`).val() || 0;
        let priceType = $(`#price_type${lineIndex}`).val();
        let quantity = $(`#quantity${lineIndex}`).val() || 1;
        let priceInclTax = $(`#price_incl_tax${lineIndex}`).val() || 0;

        let unitPrice;
        let unitPriceInclTax = priceInclTax / quantity;
        if (priceType === 'incl') {
            unitPrice = unitPriceInclTax
        } else {
            unitPrice = unitPriceInclTax / (1 + (vatRate / 100));
        }

        unitPrice = Math.round(unitPrice * 100) / 100;

        $(`#unit_price${lineIndex}`).val(unitPrice).trigger('change');
    }

    calculateTotal() {
        let totaltable = $('.inventory-total');

        // Remove all lines
        $('tbody tr:not(.total-line-template)', totaltable).remove();
        $('tfoot .total .total-excl-tax').text(0);
        $('tfoot .total .total-vat').text(0);
        $('tfoot .total .total-incl-tax').text(0);

        // Get tax totals
        let taxTotals = this.getTaxTotals();

        let template = $('.total-line-template:first', totaltable);

        let totalExclTax = 0;
        let totalVat = 0;
        let totalInclTax = 0;

        for (let i in taxTotals) {
            let taxTotal = taxTotals[i];
            let line = template.clone().removeClass('total-line-template').show();
            $('.vat-rate', line).text(taxTotal.vatRate + '%');
            $('.total-excl-tax', line).text(Math.round(taxTotal.totalExclTax * 100) / 100);
            $('.total-vat', line).text(Math.round(taxTotal.totalVat * 100) / 100);
            $('.total-incl-tax', line).text(Math.round(taxTotal.totalInclTax * 100) / 100);
            totaltable.append(line);

            // Calculate totals
            totalExclTax += taxTotal.totalExclTax;
            totalVat += taxTotal.totalVat;
            totalInclTax += taxTotal.totalInclTax;
        }

        // Round totals
        totalExclTax = Math.round(totalExclTax * 100) / 100;
        totalVat = Math.round(totalVat * 100) / 100;
        totalInclTax = Math.round(totalInclTax * 100) / 100;

        $('tfoot .total .total-excl-tax').text(totalExclTax);
        $('tfoot .total .total-vat').text(totalVat);
        $('tfoot .total .total-incl-tax').text(totalInclTax);
    }

    getTaxTotals() {
        // Calculate totals
        let taxTotals = [];
        $('tbody tr:not(.detailed-line-template):not(.detailed-line-description-template):not(.detailed-line-description)', this.table).each((index, line) => {
            let lineIndex = $(line).attr('data-index');
            let vatRate = parseFloat($(`#vat_rate${lineIndex}`).val()) || 0;
            let priceInclTax = parseFloat($(`#price_incl_tax${lineIndex}`).val()) || 0;
            let priceExclTax = parseFloat($(`#price_excl_tax${lineIndex}`).val()) || 0;

            if (typeof taxTotals[vatRate] === "undefined") {
                taxTotals[vatRate] = {
                    vatRate: vatRate,
                    totalExclTax: 0,
                    totalVat: 0,
                    totalInclTax: 0
                };
            }

            taxTotals[vatRate].totalExclTax += priceExclTax;
            taxTotals[vatRate].totalVat += Math.round((priceInclTax - priceExclTax) * 100) / 100;
            taxTotals[vatRate].totalInclTax += priceInclTax;
        });

        taxTotals.sort(this.sortOnKey("vatRate", false, false))

        return taxTotals;
    }

    sortOnKey(key, string, desc) {
        const caseInsensitive = string && string === "CI";
        return (a, b) => {
            a = caseInsensitive ? a[key].toLowerCase() : a[key];
            b = caseInsensitive ? b[key].toLowerCase() : b[key];
            if (string) {
                return desc ? b.localeCompare(a) : a.localeCompare(b);
            }
            return desc ? b - a : a - b;
        }
      };
}

new Inventory();
