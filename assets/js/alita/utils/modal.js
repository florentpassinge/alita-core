export class Modal
{
    constructor ()
    {
        this.initEls();
        this.initEvents();
    }

    initEls ()
    {
        this.elts = {
            body:       'body',
            modal:      '.modal',
            btnClose:   '.modal-close',
            btnConfirm: '.modal-confirm'
        }
        this.$elts = {
            $body:          $('body'),
            $modal:         $('#modal'),
            $modal_content: $('#modal-content'),
            $modal_title:   $('#modal-title'),
        };
    }

    initEvents ()
    {
        let self = this ;
        self.$elts.$body
            .off('click.modalClose')
            .on('click.modalClose',
                this.elts.btnClose,
                (evt) => {
                    self.removeModal();
                }
            );

        self.$elts.$body
            .on('click.modalConfirm',
                this.elts.btnConfirm,
                (evt) => {
                    self.confirmModal(evt);
                }
            );

        $('#confirm-delete').on('show.bs.modal', function(e) {
            $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
        });

        self.$elts.$body
            .off('keyup.modalPress')
            .on('keyup.modalPress',
                (evt) => {
                    self.checkRemoveClose(evt);
                }
            );
    }

    showModal (title, content, hasButtonCLose, hasButtonValid, hasButtonCancel)
    {
        this.$elts.$modal_title.html(title);
        this.$elts.$modal_content.html(content);

        this.$elts.$modal.addClass('active');
        this.$elts.$modal.removeClass('invisible');
    }

    removeModal ()
    {
        this.$elts.$modal_title.html('');
        this.$elts.$modal_content.html('');
        this.$elts.$modal.removeClass('active');
        this.$elts.$modal.addClass('invisible');
    }

    checkRemoveClose (e)
    {
        if(this.$elts.$modal.hasClass('active') && 27 === e.keyCode){
            this.removeModal();
        }
    }

    confirmModal (e)
    {
        $(this.elts.modal).find('.btn-ok').attr('href', $(e.currentTarget).data('href'));
    }
}
