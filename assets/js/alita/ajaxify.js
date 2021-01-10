import AjaxManager from './utils/ajaxManager';

export class Ajaxify
{
    constructor ()
    {
        this.initEls();
        this.initEvents();
    }

    initEls ()
    {
        this.elts = {
            ajaxify: '.js-ajaxify',
        };

        this.$elts = {
            $body: $('body'),
        }

        this.html = {
            waiting: `
                <div class="fa-3x px-3 py-3 text-muted text-center">
                    <i class="fas fa-circle-notch fa-spin"></i>
                </div>`
        }

        this.ajaxManager= new AjaxManager()
    }

    initEvents ()
    {
        let self = this;

        self.$elts.$body
            .off('click.ajaxify')
            .on('click.ajaxify',
                this.elts.ajaxify,
                (evt) => {
                    self.initAjax(evt);
                }
            );
    }

    initAjax (e)
    {
        e.preventDefault();
        e.stopPropagation();

        let self = this;
        let $target = $(e.currentTarget);

        const url = $target.data('href') ?? null;
        const modal = $target.data('modal') ?? null ;

        if (null === url) {
            console.log('not url');
            return;
        }

        let request = {
            url: url,
            error: function(e){
                console.log(e)
            },
            success: function(data) {
                $(modal).find('.modal-body').html(data);
            },
            beforeSend: function (){
                if (null === modal) {
                    return ;
                }

                $(modal).modal({ backdrop: true, keyboard: true });
                $(modal).find('.modal-body').html(self.html.waiting);
            }
        }

        self.ajaxManager.addRequest(request);
    }
}
new Ajaxify();
