export class Animated {
    constructor () {
        this.initElements();
        this.launchEvents();
    }

    initElements ()
    {
        this.$elts = {
            $animated:  $('.js-animated') || null,
            $body:      $('body')
        }
    }

    launchEvents ()
    {
        let self = this;
        if (this.$elts.$animated.length > 0) {
            this.$elts.$animated.each(function(){
                self.initAnimated($(this));
            });
        }
    }

    initAnimated ($element)
    {
        let self    = this;
        let action  = $element.data('action');

        switch (action) {
            case 'submit' :
                self.sendForm($element);
                break;
            default :
                self.log('action "' + action + '" not found for animated');
        }
    }

    sendForm ($element)
    {
        $element.submit(() => {
            let item = $element.data('item');
            let $item = $('#'+item);
            if (undefined !== typeof $item) {
                $item.addClass('active');
            }
        });
    }

    log (message)
    {
        if ('dev' === alita_environment) {
            console.log(message)
        }
    }
}
new Animated();
