$(function() {
    var data = [
        {
            label: 'node1', id: 1,
            children: [
                { label: 'child1', id: 2 },
                { label: 'child2', id: 3 }
            ]
        },
        {
            label: 'node2', id: 4,
            children: [
                { label: 'child3', id: 5 }
            ]
        }
    ];

    $('#tree1').tree({
        data: data
    });
});
