const mobile = require('is-mobile');

export class Phone
{
    constructor ()
    {
        this.initEls();
        this.initEvents();
    }

    initEls ()
    {
        this.elts = {
            phone:      '.js-phone',
            showPhone:  '.showPhone',
        };

        this.$elts = {
            $body: $('body'),
        }
    }

    initEvents ()
    {
        let self = this ;
        self.$elts.$body
            .off('click.phone')
            .on('click.phone',
                this.elts.phone,
                (evt) => {
                    self.showPhone(evt);
                }
            );

        $(self.elts.showPhone).hide();
    }

    showPhone (evt)
    {
        let $target =  $(evt.currentTarget);
        let self = this;

        if (mobile.isMobile()) {
            window.open('tel:' + $target.data('phone'), '_blank');
        } else {
            $target.find(self.elts.showPhone).html($target.data('phone')).toggle('slow');
        }
    }
}
new Phone();
