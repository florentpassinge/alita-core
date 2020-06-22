const $ = require('jquery');

import {Animated}   from './atila/animated';
import {Form}       from './atila/form';
import {Modal}      from './atila/modal';
import {Phone}      from './atila/phone';
import {Toolkit}    from './atila/toolkit';

export class Alita {
    constructor () {
        this.initElements();
        this.launchEvents();
    }

    initElements ()
    {
        this.$elts = {
            $animated   : $('.js-animated') || null,
            $form       : $('.js-form')     || null,
            $modal      : $('.js-modal')    || null,
            $phone      : $('.js-phone')    || null,
        };
    }

    launchEvents ()
    {
        new Toolkit();

        if (this.$elts.$animated.length > 0) {
            new Animated();
        }

        if (this.$elts.$form.length > 0) {
            new Form();
        }

        if (this.$elts.$modal.length > 0) {
            new Modal();
        }

        if (this.$elts.$phone.length > 0) {
            new Phone();
        }
    }
}
