$.mockjax({
    url: '*',
    response: function(options) {
        this.responseText = ExampleData.example_data;
    },
    responseTime: 0
});

$(function() {
    $('#tree1').tree();
});
