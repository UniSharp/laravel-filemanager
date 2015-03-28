$(function() {
    var $menu = $('#menu');
    var $body = $('body');
    var $h1 = $('#jqtree h1');

    // title
    $h1.html("<span class=\"first\">jq</span><span class=\"second\">Tree</span>");

    // menu
    $menu.affix({
        offset: {
            top: $menu.offset().top,
            bottom: 0
        }
    });

    $body.scrollspy({
        target: '#menu'
    });
    var scrollspy = $body.data('bs.scrollspy');

    // If no menu item is active, then activate first item
    if (! scrollspy.activeTarget) {
        scrollspy.activate('#general');
    }

    // demo tree
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

    var $tree1 = $('#tree1');

    $tree1.tree({
        data: data,
        autoOpen: true,
        dragAndDrop: true
    });
});
