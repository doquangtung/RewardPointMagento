define(
    [
        'jquery',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals',
        'Magento_Catalog/js/price-utils'
    ],
    function ($,Component,quote,totals,priceUtils) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'LoyaltyProgram_RewardPoint/checkout/summary/point-fee'
            },
            totals: quote.getTotals(),
            isDisplayedDiscountTotal : function () {
                return true;
            },
            getDiscountTotal : function () {
                var price = totals.getSegment('point').value;
                return this.getFormattedPrice(price);
            }
        });
    }
 );