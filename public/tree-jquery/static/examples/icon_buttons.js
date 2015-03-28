$.mockjax({
    url: '*',
    response: function(options) {
        this.responseText = ExampleData.example_data;
    },
    responseTime: 0
});

$(function() {
    $('#tree1').tree({
        closedIcon: $('<i class="fa fa-arrow-circle-right"></i>'),
        openedIcon: $('<i class="fa fa-arrow-circle-down"></i>')
    });
});
