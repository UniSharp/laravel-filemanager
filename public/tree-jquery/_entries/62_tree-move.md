---
title: tree.move
name: event-tree-move
---

Triggered when the user moves a node.

Event.move_info contains:

* moved_node
* target_node
* position: (before, after or inside)
* previous_parent

{% highlight js %}
$('#tree1').tree({
    data: data,
    dragAndDrop: true
});

$('#tree1').bind(
    'tree.move',
    function(event) {
        console.log('moved_node', event.move_info.moved_node);
        console.log('target_node', event.move_info.target_node);
        console.log('position', event.move_info.position);
        console.log('previous_parent', event.move_info.previous_parent);
    }
);
{% endhighlight %}

You can prevent the move by calling **event.preventDefault()**

{% highlight js %}
$('#tree1').bind(
    'tree.move',
    function(event) {
        event.preventDefault();
    }
);
{% endhighlight %}

You can later call **event.move_info.move_info.do_move()** to move the node. This way you can ask the user before moving the node:

{% highlight js %}
$('#tree1').bind(
    'tree.move',
    function(event) {
        event.preventDefault();

        if (confirm('Really move?')) {
            event.move_info.do_move();
        }
    }
);
{% endhighlight %}

Note that if you want to serialise the tree, for example to POST back to a server, you need to let tree complete the move first:

{% highlight js %}
$('#tree1').bind(
    'tree.move',
    function(event)
    {
        event.preventDefault();
        // do the move first, and _then_ POST back.
        event.move_info.do_move();
        $.post('your_url', {tree: $(this).tree('toJson')});
    }
);
{% endhighlight %}
