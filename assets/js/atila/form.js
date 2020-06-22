import $ from 'jquery';

import {Modal}              from './modal.js';
import passwordValidator    from 'password-validator';
import validator            from 'validator';

export class Form
{
    constructor ()
    {
        this.initElts();
        this.initEvents();
    }

    initElts ()
    {
        this.elts = {
            confirm:        '.js-confirm',
            email:          '.js-email',
            input:          '.js-input',
            password:       '.js-password',
            textDanger:     'text-danger',
            textSuccess:    'text-success',
        };

        this.$elts = {
            $body:          $('body'),
            $confirm:       $('.js-confirm'),
            $form:          $('.js-form'),
            $modal:         new Modal(),
            $passwordUtils: $('.js-password-utils'),
        };

        this.passwordFormat = new passwordValidator();
        this.passwordFormat.is().min(form_password_min_char)
            .is().max(form_password_max_char)
            .has().uppercase()
            .has().lowercase()
            .has().digits()
            .has().symbols()
            .has().not().spaces();
        this.formatPassword = ['min-max', 'uppercase', 'lowercase', 'digits', 'symbols', 'spaces' ];
    }

    initEvents ()
    {
        let self = this ;
        self.$elts.$passwordUtils.hide();

        self.$elts.$body
            .off('keyup.email')
            .on('keyup.email',
                this.elts.email,
                (evt) => {
                    self.checkValidityEmail(evt);
                }
            );

        self.$elts.$body
            .off('keyup.password')
            .on('keyup.password',
                this.elts.password,
                (evt) => {
                    self.checkPassword(evt);
                }
            );

        self.$elts.$body
            .off('focusin.password')
            .on('focusin.password',
                this.elts.password,
                (evt) => {
                    self.$elts.$passwordUtils.show();
                }
            );

        self.$elts.$body
            .off('focusout.password')
            .on('focusout.password',
                this.elts.password,
                (evt) => {
                    self.$elts.$passwordUtils.hide();
                }
            );

        self.$elts.$body
            .off('focusout.email')
            .on('focusout.email',
                this.elts.email,
                (evt) => {
                    self.formatEmail(evt);
                }
            );

        self.$elts.$body
            .off('keyup.inputConfirm')
            .on('keyup.inputConfirm',
                this.elts.confirm,
                (evt) => {
                    self.checkConfirm(evt);
                }
            );

        self.$elts.$body
            .off('focusout.inputForm')
            .on('focusout.inputForm',
                this.elts.input,
                (evt) => {
                    self.checkInput(evt);
                }
            );

        this.$elts.$form.on('submit', this.submit.bind(this));

    }

    checkValidityEmail(e)
    {
        let $currentTarget  = $(e.currentTarget);
        let value           = $currentTarget.val();

        if(!validator.isEmail(value)){
            this.fieldError($currentTarget);
        } else{
            this.fieldSuccess($currentTarget);
        }

        if(!$currentTarget.hasClass('js-confirm')){
            this.$elts.$confirm.trigger('keyup.inputConfirm');
        }

    }

    checkConfirm(e){
        let $currentTarget  = $(e.currentTarget);
        let target          = $currentTarget.data('confirm');

        if(!validator.isEmpty($currentTarget.val())){
            let $target = $('#' + target);
            if(validator.isEmpty($currentTarget.val()) || !validator.equals($currentTarget.val(), $target.val())){
                this.fieldError($currentTarget);
            } else{
                this.fieldSuccess($currentTarget);
            }
        }
    }

    checkInput(e)
    {
        let $currentTarget  = $(e.currentTarget);
        let action          = $currentTarget.data('action');

        switch(action){
            case 'uppercase' :
                this.formatUpperCase(e);
                break;
            case 'required' :
                this.requiredInput(e);
                break;
            default:
                break;
        }
    }

    checkPassword(e)
    {
        let self            = this;
        let $currentTarget  = $(e.currentTarget);
        let value           = $currentTarget.val();
        let aError          = self.passwordFormat.validate(value, { list: true });

        if(aError.length > 0){
            self.fieldError($currentTarget);
            self.formatPassword.forEach(function(e){
                if(e === 'min-max'){
                    if(aError.indexOf('min') || aError.indexOf('max')){
                        $('.min-max', '.js-password-utils').removeClass(self.elts.textSuccess);
                        $('.min-max', '.js-password-utils').addClass(self.elts.textDanger);
                    }else{
                        $('.min-max', '.js-password-utils').removeClass(self.elts.textDanger);
                        $('.min-max', '.js-password-utils').addClass(self.elts.textSuccess);
                    }
                }else{
                    if(aError.indexOf(e) !== -1){
                        $('.' + e, '.js-password-utils').removeClass(self.elts.textSuccess);
                        $('.' + e, '.js-password-utils').addClass(self.elts.textDanger);
                    }else{
                        $('.' + e, '.js-password-utils').removeClass(self.elts.textDanger);
                        $('.' + e, '.js-password-utils').addClass(self.elts.textSuccess);
                    }
                }
            });
        }else{
            $('.js-password-utils li').removeClass(self.elts.textDanger).addClass(self.elts.textSuccess);
            self.fieldSuccess($currentTarget);
        }
    }

    formatEmail(e)
    {
        let $currentTarget  = $(e.currentTarget);
        let value           = $currentTarget.val();
        if (validator.isEmail(value)) {
            $currentTarget.val(validator.normalizeEmail(value,{gmail_remove_dots:false, all_lowercase: true}));
            this.fieldSuccess($currentTarget);
        } else {
            this.fieldError($currentTarget);
        }
    }

    formatUpperCase(e){
        let $currentTarget  = $(e.currentTarget);
        let value           = $currentTarget.val();

        $currentTarget.val(value.toUpperCase());
    }

    requiredInput(e)
    {
        let $currentTarget  = $(e.currentTarget);
        let value           = $currentTarget.val();

        (0 === value.length ? this.fieldError($currentTarget): this.fieldSuccess($currentTarget));
    }

    submit(e)
    {
        let $currentTarget  = $(e.currentTarget);
        let $fieldError     = $currentTarget.find('.is-invalid');
        let nbError         = $fieldError.length;

        if(0 === nbError){
            return true ;
        }else{
            let string = '<ul>';
            $fieldError.each(function(i, e){
                let id = e.id;
                let label = $('label[for='+  id  +']').text();

                string += '<li>' + label + '</li>';
            });
            string += '</ul>';
            this.$elts.$modal.showModal(error_title, error_form_fields + string);
            return false;
        }
    }

    fieldError(target)
    {
        if(!target.hasClass('is-invalid')){
            target.removeClass('is-valid');
            target.addClass('is-invalid');
        }
    }

    fieldSuccess(target)
    {
        if(!target.hasClass('is-valid')){
            target.removeClass('is-invalid');
            target.addClass('is-valid');
        }
    }
}
