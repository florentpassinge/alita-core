export class Alita {
    constructor () {
        this.initElements();
        this.launchEvents();
    }

    initElements ()
    {
        this.$elts = {
            $ajaxify    : $('.js-ajaxify')  || null,
            $alert      : $('.js-alert') || null,
            $animated   : $('.js-animated') || null,
            $form       : $('.js-form')     || null,
            $phone      : $('.js-phone')    || null,
        };
    }

    launchEvents ()
    {
        import(/* webpackMode: "eager" *//* webpackChunkName: "toolkit" */ './alita/toolkit')
          .catch(error => 'An error occurred while loading the component Toolkit');

        if (this.$elts.$ajaxify.length > 0) {
            import(/* webpackMode: "lazy" *//* webpackChunkName: "ajaxify" */ './alita/ajaxify')
              .catch(error => 'An error occurred while loading the component Ajaxify');
        }

        if (this.$elts.$alert.length > 0) {
            import(/* webpackMode: "lazy" *//* webpackChunkName: "alert" */ './alita/alert')
              .catch(error => 'An error occurred while loading the component Alert');
        }

        if (this.$elts.$animated.length > 0) {
            import(/* webpackMode: "lazy" *//* webpackChunkName: "animated" */ './alita/animated')
              .catch(error => 'An error occurred while loading the component Animated');
        }

        if (this.$elts.$form.length > 0) {
            import(/* webpackMode: "lazy" *//* webpackChunkName: "form" */ './alita/form')
              .catch(error => 'An error occurred while loading the component Form');
        }

        if (this.$elts.$phone.length > 0) {
            import(/* webpackMode: "lazy" *//* webpackChunkName: "phone" */ './alita/phone')
              .catch(error => 'An error occurred while loading the component Phone');
        }
    }
}
