export class FiltersModal
{
    constructor ()
    {
        this.initEls();
        this.initEvents();
    }

    initEls ()
    {
        this.elts = {
            btnApply:   '#modal-apply-button',
            btnClear:   '#modal-clear-button',
            fields:     '.filter-field'
        };

        this.$elts = {
            $body:          $('body'),
            $form:          $('#filters'),
            $modalFilter:   $('#modal-filters'),
        }
    }

    initEvents ()
    {
        let self = this ;

        self.$elts.$body
            .off('click.applyFiltersModal')
            .on('click.applyFiltersModal', self.elts.btnApply,
                (evt) => {
                    self.applyModal(evt);
                });

        self.$elts.$body
            .off('click.closeFiltersModal')
            .on('click.closeFiltersModal', self.elts.btnClear,
                () => {
                    self.closeModal();
                });
    }

    applyModal (evt)
    {
        let self = this;
        
        evt.preventDefault();

        $('.filter-checkbox:not(:checked)').each(function () {
            let $filterCheckbox = $(this);
            let $filter = $filterCheckbox.closest(self.elts.fields);

            self.removeFilter($filter);
        });

        $('#filters').submit();
    }

    closeModal ()
    {
        let self = this;

        $(self.elts.fields).each(function () {
            let $filter = $(this);
            self.removeFilter($filter);
        });

        $('#filters').submit();
    }

    removeFilter ($filter)
    {
        let self = this;

        const property = $filter.data('filterProperty');

        document.querySelectorAll('input[name^="filters['+property+']"]').forEach((hidden) => {
            hidden.remove();
        })

        $filter.remove();
    }
}
new FiltersModal();
