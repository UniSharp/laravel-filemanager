$(function() {
    var $tree = $('#tree1');
    $tree.tree({
        data: ExampleData.example_data,
        dragAndDrop: true,
        autoOpen: true
    });
});
