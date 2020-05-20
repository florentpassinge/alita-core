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
    }

    externalLink(evt)
    {
        let $currentTarget = $(evt.currentTarget);
        evt.preventDefault();
        evt.stopPropagation();

        window.open($currentTarget.attr('href'),'_blank');
    }
}
