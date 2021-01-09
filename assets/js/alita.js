import {Ajaxify}        from './atila/ajaxify';
import {Alert}          from './atila/alert';
import {Animated}       from './atila/animated';
import {FiltersModal}   from './atila/filtersModal';
import {Form}           from './atila/form';
import {Phone}          from './atila/phone';
import {Toolkit}        from './atila/toolkit';

export class Alita {
    constructor () {
        this.initElements();
        this.launchEvents();
    }

    initElements ()
    {
        this.$elts = {
            $ajaxify    : $('.js-ajaxify')  || null,
            $alert      : $('.js-alert') || null,
            $animated   : $('.js-animated') || null,
            $filters    : $('.js-filters-modal') || null,
            $form       : $('.js-form')     || null,
            $phone      : $('.js-phone')    || null,
        };
    }

    launchEvents ()
    {
        new Toolkit();

        if (this.$elts.$ajaxify.length > 0) {
            new Ajaxify();
        }

        if (this.$elts.$alert.length > 0) {
            new Alert();
        }

        if (this.$elts.$animated.length > 0) {
            new Animated();
        }

        if (this.$elts.$filters.length > 0) {
            new FiltersModal();
        }

        if (this.$elts.$form.length > 0) {
            new Form();
        }

        if (this.$elts.$phone.length > 0) {
            new Phone();
        }
    }
}
