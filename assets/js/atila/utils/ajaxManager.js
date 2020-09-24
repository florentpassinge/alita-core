export default class AjaxManager {
    constructor ()
    {
        this.requests   = [];
        this.xhr        = undefined;
        this.timer      = 1000;
        this.time       = undefined;
    }

    addRequest (request)
    {
        this.requests.push(request);
        this.run();
    }

    removeRequest (request)
    {
        if (this.requests.length > 0 && $.inArray(request , this.requests) > -1) {
            this.requests.splice($.inArray(request , this.requests), 1);
        }
    }

    run ()
    {
        let self = this;
        let oriSuc ;

        if (self.requests.length > 0) {
            oriSuc = self.requests[0].complete;

            self.requests[0].complete = function() {
                if( typeof oriSuc === 'function' ) oriSuc();
                self.requests.shift();
                self.run.apply(self, []);
            };

            $.ajax(self.requests[0]);
        } else {
            self.time = setTimeout(function () {
                self.run.apply(self, []);
            },self.timer);
        }
    }
}