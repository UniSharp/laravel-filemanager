$.mockjax({
    url: '*',
    response: function(options) {
        this.responseText = ExampleData.example_data;
    },
    responseTime: 0
});

$(function() {
    var $tree = $('#tree1');
    $tree.tree({
        dragAndDrop: true,
        autoOpen: 0
    });
});
