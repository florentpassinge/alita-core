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
            sidebarToggle: '#sidebarToggle',
            sidebarToggleTop: '#sidebarToggleTop'

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
                self.elts.external,
                (evt) => {
                    self.externalLink(evt);
                }
            );

        self.$elts.$body
            .off('click.sidebarToggle')
            .on('click.sidebarToggle',
                self.elts.sidebarToggle,
                () => {
                    self.sideBarToggle()
                }
            );

        self.$elts.$body
            .off('click.sidebarToggleTop')
            .on('click.sidebarToggleTop',
                self.elts.sidebarToggleTop,
                () => {
                    self.sideBarToggle()
                }
            );

        $(document).on('scroll', function() {
            const scrollDistance = $(this).scrollTop();
            if (scrollDistance > 100) {
                $('.scroll-to-top').fadeIn();
            } else {
                $('.scroll-to-top').fadeOut();
            }
        });

        $('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', function(e) {
            if ($(window).width() > 768) {
                var e0 = e.originalEvent,
                    delta = e0.wheelDelta || -e0.detail;
                this.scrollTop += (delta < 0 ? 1 : -1) * 30;
                e.preventDefault();
            }
        });

        $(document).on('click', 'a.scroll-to-top', function(e) {
            var $anchor = $(this);
            $('html, body').stop().animate({
                scrollTop: ($($anchor.attr('href')).offset().top)
            }, 1000);
            e.preventDefault();
        });

        self.$elts.$body.addClass('ea-content-width-' + (localStorage.getItem('ea/content/width') || 'normal'));
        self.$elts.$body.addClass('ea-sidebar-width-' + (localStorage.getItem('ea/sidebar/width') || 'normal'));
    }

    externalLink(evt)
    {
        let $currentTarget = $(evt.currentTarget);
        evt.preventDefault();
        evt.stopPropagation();

        window.open($currentTarget.attr('href'),'_blank');
    }

    sideBarToggle()
    {
        $("body").toggleClass("sidebar-toggled");
        $(".sidebar").toggleClass("toggled");
        if ($(".sidebar").hasClass("toggled")) {
            $('.sidebar .collapse').collapse('hide');
        };
    }
}
