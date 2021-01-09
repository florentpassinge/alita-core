import iziToast from 'izitoast/dist/js/iziToast';

export class Alert
{
    constructor ()
    {
        this.initEls();
        this.initEvents();
    }

    initEls ()
    {
        iziToast.settings({
            animateInside: false,
            layout: 2,
            position: 'topCenter',
            resetOnHover: true,
            timeout: 5000,
            transitionIn: 'flipInX',
            transitionOut: 'flipOutX'
        });

        this.elts = {
            icons: {
                error: 'far fa-times-cicle',
                info: 'fas fa-exclamation-circle',
                question: 'far fa-question-circle',
                success: 'far fa-check-circle',
                warning: 'fas fa-exclamation-triangle',
            }
        }

        this.$elts = {
            $alert: $('.js-alert') || null,
        }
    }

    initEvents ()
    {
        let self = this;

        if (self.$elts.$alert.length > 0) {
            self.$elts.$alert.each(function(index, element){
                let $element = $(element);

                let title = $element.data('title') ?? '';
                let message = $element.data('message');
                let type = $element.data('type');

                iziToast[type]({
                    title: title,
                    message: message
                });
            });
        }
    }
}
