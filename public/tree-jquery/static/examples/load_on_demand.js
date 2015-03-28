$.mockjax({
    url: '*',
    responseTime: 1000,
    response: function(options) {
        if (options.data && options.data.node) {
            this.responseText = ExampleData.getChildrenOfNode(options.data.node);
        }
        else {
            this.responseText = ExampleData.getFirstLevelData();
        }
    }
});

$(function() {
    var $tree = $('#tree1');

    $tree.tree({
        saveState: true
    });
});
