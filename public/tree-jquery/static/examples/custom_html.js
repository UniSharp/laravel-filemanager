$(function() {
    var $tree = $('#tree1');

    $tree.tree({
        data: ExampleData.example_data,
        autoOpen: 1,
        onCreateLi: function(node, $li) {
            // Append a link to the jqtree-element div.
            // The link has an url '#node-[id]' and a data property 'node-id'.
            $li.find('.jqtree-element').append(
                '<a href="#node-'+ node.id +'" class="edit" data-node-id="'+ node.id +'">edit</a>'
            );
        }
    });

    // Handle a click on the edit link
    $tree.on(
        'click', '.edit',
        function(e) {
            // Get the id from the 'node-id' data property
            var node_id = $(e.target).data('node-id');

            // Get the node from the tree
            var node = $tree.tree('getNodeById', node_id);

            if (node) {
                // Display the node name
                alert(node.name);
            }
        }
    );
});
