// require('materialize-css')

class Inventory {
    constructor() {
        this.container = $('.inventory-container');
        this.table = $('.inventory-table');
        this.maxId = 1;

        this.initListeners();
        this.addLine();
    }

    initListeners() {
        this.initAddLineListener();
        this.initRemoveLineListener();
        this.initLineListeners();
    }

    initAddLineListener() {
        $('.add-line', this.container).on('click', (event) => {
            event.preventDefault();

            this.addLine();
        });
    }

    initRemoveLineListener() {

    }

    initLineListeners() {
        this.initButtonClickListener();
        this.initInputChangeListener();
        this.initInputClickListener();
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

    addLine() {
        let template = $('.detailed-line-template:first', this.table);

        // Clone line
        let line = template.clone().show();
        line.removeClass('detailed-line-template').addClass('detailed-line')
        line.attr('data-index', this.maxId);

        // Replace ids
        $('#id0', line).attr('id', `id${this.maxId}`).attr('name', `lines[${this.maxId}][id]`);
        $('#product_id0', line).attr('id', `product_id${this.maxId}`).attr('name', `lines[${this.maxId}][product_id]`);
        $('#label0', line).attr('id', `label${this.maxId}`).attr('name', `lines[${this.maxId}][label]`);
        $('#vat_rate0', line).attr('id', `vat_rate${this.maxId}`).attr('name', `lines[${this.maxId}][vat_rate]`);
        $('#unit_price0', line).attr('id', `unit_price${this.maxId}`).attr('name', `lines[${this.maxId}][unit_price]`);
        $('#quantity0', line).attr('id', `quantity${this.maxId}`).attr('name', `lines[${this.maxId}][quantity]`);
        $('#price_type0', line).attr('id', `price_type${this.maxId}`).attr('name', `lines[${this.maxId}][price_type]`);
        $('#unit0', line).attr('id', `unit${this.maxId}`).attr('name', `lines[${this.maxId}][unit]`);
        $('#price_excl_tax0', line).attr('id', `price_excl_tax${this.maxId}`).attr('name', `lines[${this.maxId}][price_excl_tax]`);
        $('#price_incl_tax0', line).attr('id', `price_incl_tax${this.maxId}`).attr('name', `lines[${this.maxId}][price_incl_tax]`);

        //TODO:
        // $('#description0', line).attr('id', `description${this.maxId}`).attr('name', `lines[${this.maxId}][description]`);


        // $('select', line).each((index, el) => {
        //     $(el).formSelect({
        //         dropdownOptions: {
        //             alignment: $(el).data('alignment') ? $(el).data('alignment') : 'left',
        //             constrainWidth: $(el).data('constrain-width') === false ? false : true,
        //             container: $(el).data('container') ? $($(el).data('container')) : null,
        //             coverTrigger: $(el).data('cover-trigger') === true ? true : false,
        //             closeOnClick: $(el).data('close-on-click') === false ? false : true,
        //             hover: $(el).data('hover') === true ? true : false,
        //         }
        //     })
        // })

        // Append line to table
        $('tbody', this.table).append(line);

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
        taxTotals = this.getTaxTotals();

        let template = $('.total-line-template:first', totaltable);

        let totalExclTax = 0;
        let totalVat = 0;
        let totalInclTax = 0;

        for (let i in taxTotals) {
            let taxTotal = taxTotals[i];
            let line = template.clone().removeClass('total-line-template').show();
            $('.vat-rate', line).text(taxTotal.vatRate + '%');
            $('.total-excl-tax', line).text(taxTotal.totalExclTax);
            $('.total-vat', line).text(taxTotal.totalVat);
            $('.total-incl-tax', line).text(taxTotal.totalInclTax);
            totaltable.append(line);

            // Calculate totals
            totalExclTax += taxTotal.totalExclTax;
            totalVat += taxTotal.totalVat;
            totalInclTax += taxTotal.totalInclTax;
        }

        $('tfoot .total .total-excl-tax').text(totalExclTax);
        $('tfoot .total .total-vat').text(totalVat);
        $('tfoot .total .total-incl-tax').text(totalInclTax);
    }

    getTaxTotals() {
        // Calculate totals
        let taxTotals = [];
        $('tbody tr:not(.detailed-line-template)', this.table).each((index, line) => {
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
