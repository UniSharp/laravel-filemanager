$(function() {
    var $tree = $('#tree1');
    $tree.tree({
        data: ExampleData.example_data,
        autoOpen: true
    });
    $tree.bind(
        'tree.click',
        function(e) {
            // Disable single selection
            e.preventDefault();

            var selected_node = e.node;

            if (selected_node.id == undefined) {
                console.log('The multiple selection functions require that nodes have an id');
            }

            if ($tree.tree('isNodeSelected', selected_node)) {
                $tree.tree('removeFromSelection', selected_node);
            }
            else {
                $tree.tree('addToSelection', selected_node);
            }
        }
    );
});
