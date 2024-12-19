import './scripts/back-link';
import './scripts/accordion';
import './scss/app.scss';

import {Modal} from 'bootstrap';

import $ from 'jquery';

class DajnatoForm {
    $form;
    recurringAmount;
    onetimeAmount;
    expenses;

    constructor($form) {
        this.$form = $form;

        this.onetimeAmount = this.$form.find('.dajnato-expenses').data('onetime-amount');
        this.recurringAmount = this.$form.find('.dajnato-expenses').data('recurring-amount');
        this.expenses = this.$form.find('.js-expenses:checked').length > 0;

        const $holder = $form.closest('.js-donation-form');

        $holder
            .on('submit', 'form', (event) => {

                event.preventDefault();
                const $button = this.$form.find('button[type=submit]');
                $button.text('Odosielam...').prop('disabled', true);

                $.ajax({
                    url: this.$form.attr('action') ?? location.href, data: this.$form.serialize() + '&' + this.$form.attr('name') + '[button]=' + $button.attr('name'), type: 'POST'
                }).done((response, status, jqXHR) => {
                    const $html = $('<div>' + response + '</div>');

                    if ($html.find('.js-darujme-form form').length > 0) {
                        // valid dajnato form was sent => append Darujme form and POST it
                        $('#darujme-form').html('');

                        const $form = $html.find('#darujme-form');
                        $('body').append($form);

                        $form.submit();
                    } else {
                        this.$form.replaceWith($html.find('.js-donation-form form'));
                        this.$form = $holder.find('form');

                        // let top = 0;
                        //
                        // this.$form.find('.is-invalid:first').parents('.modal-dialog *').each(function () {
                        //     top = Math.max(top, $(this).position().top);
                        // });
                        //
                        // this.$form.scrollTop(top);
                    }
                });
            })
            .on('change', '.js-onetimeOrRecurring input', (event) => {
                this.$form.find('.onetime-or-recurring').addClass('d-none');
                this.$form.find('.onetime-or-recurring.' + $(event.target).val()).removeClass('d-none');
            })
            .on('change', '.js-expenses', (event) => {
                this.expenses = event.target.checked;
                this.updateButtonAmount();
            })
            .on('input', '.js-otherAmount', (event) => {
                this.onetimeAmount = event.target.value;
                this.recurringAmount = event.target.value;

                this.updateButtonAmount();
            })
            .on('change', '.js-onetimeAmount input, .js-recurringAmount input', (event) => {
                const $triggeredInput = $(event.target);

                // maintain clicked button at the same position for onetime and recurring payments

                if ('T' === $triggeredInput.data('is-other')) {
                    this.$form.find('.js-onetimeAmount input:last').prop('checked', true);
                    this.$form.find('.js-recurringAmount input:last').prop('checked', true);

                    this.onetimeAmount = this.recurringAmount = this.$form.find('.js-otherAmount').val();

                    this.$form.find('.js-other-sum').removeClass('d-none');
                } else {
                    let index = 0;
                    this.$form.find('input[name="' + $triggeredInput.attr('name') + '"]').each((i, input) => {
                        if ($(input).prop('checked')) {
                            index = i;
                        }
                    });

                    const maxOnetimeIndex = this.$form.find('.js-onetimeAmount input').length - 2;
                    const maxRecurringIndex = this.$form.find('.js-recurringAmount input').length - 2;

                    const $onetimeCheckbox = this.$form.find('.js-onetimeAmount input:eq(' + (index <= maxOnetimeIndex ? index : maxOnetimeIndex) + ')');
                    const $recurringCheckbox = this.$form.find('.js-recurringAmount input:eq(' + (index <= maxRecurringIndex ? index : maxRecurringIndex) + ')');

                    $onetimeCheckbox.prop('checked', true);
                    $recurringCheckbox.prop('checked', true);

                    this.onetimeAmount = $onetimeCheckbox.val();
                    this.recurringAmount = $recurringCheckbox.val();

                    this.$form.find('.js-other-sum').addClass('d-none');
                }

                this.updateButtonAmount();
            });
    }

    updateButtonAmount() {
        const expensesCoef = this.expenses ? 1.039 : 1;

        this.$form.find('.button-onetime-amount')
            .html(!this.onetimeAmount ? '' : (Math.round(this.onetimeAmount * expensesCoef) + '&nbsp;€'));
        this.$form.find('.button-recurring-amount')
            .html(!this.recurringAmount ? '' : (Math.round(this.recurringAmount * expensesCoef) + '&nbsp;€'));
    }
}

$(document).ready(function () {
    $('form[name=donation]').each(function () {
        const form = new DajnatoForm($(this));
    });

    const dajnatoCTAModal = new Modal('#donationFormModal');

    $('body')
        .on('click', '.js-widget-form-submit', function (event) {
            event.preventDefault();
            const $button = $(this);
            $button.text('Odosielam...').prop('disabled', true);

            const $form = $(this).closest('.js-donation-form').children('form');

            $.ajax({
                type: $form.attr('method'), url: $form.attr('action'), data: $form.serialize() + '&' + $form.attr('name') + '[button]=continue', success: function (response) {
                    const $modalContent = $('#donationFormModalContent');
                    $modalContent.html(response);
                    new DajnatoForm($modalContent.find('form'));
                    dajnatoCTAModal.show();
                    setTimeout(() => $button.text('Darovať').prop('disabled', false), 1000);
                }
            });

            // const $button = $(this);
            // let url = $button.data('form-url');
            // const dialog = $('#donationFormModal .modal-dialog');
            // const buttonText = $button.text();
            //
            // if ('BUTTON' === $button.prop('tagName')) {
            //     $button.text('Čakaj, prosím...').prop('disabled', true);
            // }
            //
            // const $formWidget = $button.closest('.form-widget');
            //
            // if ($formWidget.length > 0) {
            //     url += (url.indexOf('?') === -1 ? '?' : '&') + 'campaign_value=' +
            //         $formWidget.find('input[type=radio]:checked').val();
            // }
            //
            // $.get(url).then((response) => {
            //     dialog.html($(response).find('.modal-dialog').html());
            //     const form = new DajnatoForm($('form[name=donation_modal]'));
            //     dajnatoCTAModal.show();
            //
            //     if ('BUTTON' === $button.prop('tagName')) {
            //         $button.text(buttonText).prop('disabled', false);
            //     }
            // });
        });
});

