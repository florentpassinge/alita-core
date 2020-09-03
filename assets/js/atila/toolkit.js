import $ from 'jquery';

export class Toolkit
{
    constructor ()
    {
        this.initEls();
        this.initEvents();
    }

    initEls ()
    {
        this.elts = {
            external: 'a.external',
        };

        this.$elts = {
            $body: $('body'),
            $progressBarAlert: $('.progress-bar-alert'),
        }
    }

    initEvents ()
    {
        let self = this ;
        self.$elts.$body
            .off('click.external')
            .on('click.external',
                this.elts.external,
                (evt) => {
                    self.externalLink(evt);
                }
            );
        $(document).ready(function() {
            if (self.$elts.$progressBarAlert.length > 0) {
                self.$elts.$progressBarAlert.each(function () {
                    self.progressBar($(this));
                });
            }
        });
    }

    externalLink(evt)
    {
        let $currentTarget = $(evt.currentTarget);
        evt.preventDefault();
        evt.stopPropagation();

        window.open($currentTarget.attr('href'),'_blank');
    }

    progressBar(item)
    {
        let $item = $(item);
        setTimeout((e) => {
            $item.css('width', 0 + '%');
            setTimeout((e) =>{
                $item.closest('.alert-container').remove();
            },5100);
        }, 2000);
    }
}
