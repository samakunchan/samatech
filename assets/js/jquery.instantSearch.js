/**
 * jQuery plugin for an instant searching.
 *
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
(function ($) {
    'use strict';

    String.prototype.render = function (parameters) {
        return this.replace(/({{ (\w+) }})/g, function (match, pattern, name) {
            return parameters[name];
        })
    };

    // INSTANTS SEARCH PUBLIC CLASS DEFINITION
    // =======================================

    var InstantSearch = function (element, options) {
        this.$input = $(element);
        this.$form = this.$input.closest('form');
        this.$preview = $('<ul class="search-preview list-group row">').appendTo(this.$form);
        this.options = $.extend({}, InstantSearch.DEFAULTS, this.$input.data(), options);

        this.$input.keyup(this.debounce());
    };

    InstantSearch.DEFAULTS = {
        minQueryLength: 2,
        limit: 10,
        delay: 500,
        noResultsMessage: 'No results found',
        itemTemplate: '\
                <article class="post" style="border-bottom: solid 1px">\
                    <div class="blog_post">\
                        <div class="blog_details">\
                            <a href="{{ url }}">\
                                <h2>{{ title }}</h2>\
                            </a>\
                            <span class="d-flex align-items-center">\
                                <i class="lnr lnr-calendar-full mr-3 text-blue" style="font-size: 25px"></i>\
                                {{ date }}\
                            </span>\
                            <p>{{ content }}</p>\
                        </div>\
                    </div>\
                </article>\
                '
    };


    InstantSearch.prototype.debounce = function () {
        var delay = this.options.delay;
        var search = this.search;
        var timer = null;
        var self = this;

        return function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                search.apply(self);
            }, delay);
        };
    };

    InstantSearch.prototype.search = function () {
        var query = $.trim(this.$input.val()).replace(/\s{2,}/g, ' ');
        if (query.length < this.options.minQueryLength) {
            this.$preview.empty();
            return;
        }

        var self = this;
        var data = this.$form.serializeArray();
        data['l'] = this.limit;

        $.getJSON(this.$form.attr('action'), data, function (items) {
            self.show(items);
        });
    };

    InstantSearch.prototype.show = function (items) {
        var $preview = this.$preview;
        var itemTemplate = this.options.itemTemplate;

        if (0 === items.length) {
            $preview.html(this.options.noResultsMessage);
        } else {
            $preview.empty();
            $.each(items, function (index, item) {
                $preview.append(itemTemplate.render(item));
            });
        }
    };

    // INSTANTS SEARCH PLUGIN DEFINITION
    // =================================

    function Plugin(option) {
        return this.each(function () {
            var $this = $(this);
            var instance = $this.data('instantSearch');
            var options = typeof option === 'object' && option;

            if (!instance) $this.data('instantSearch', (instance = new InstantSearch(this, options)));

            if (option === 'search') instance.search();
        })
    }

    $.fn.instantSearch = Plugin;
    $.fn.instantSearch.Constructor = InstantSearch;

})(window.jQuery);
