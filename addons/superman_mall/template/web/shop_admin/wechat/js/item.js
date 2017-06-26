require(['jquery'], function($) {
    var ItemDisplay = {
        init: function(){
            this.binding();
        },
        binding: function(){
            var t = this;
            //example
            $('button[type=submit]').click(function(){
                t.btnSearch();
            });
            $('.status_update').click(function(){
                t.statusUpdate(this);
            });
        },
        //example
        btnSearch: function(){
            //TODO
        },
        statusUpdate: function(element){
            var id = $(element).attr('data-id');
            var status = $(element).attr('data-status');
            alert('TODO,js交互暂停一下，先写模板');
        }
    };
    ItemDisplay.init();
    var ItemPost = {
        init: function(){
            this.binding();
        },
        binding: function(){
            //TODO
        }
    };
    ItemPost.init();
});