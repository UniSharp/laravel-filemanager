---
title: tree.contextmenu
name: event-tree-contextmenu
---

Triggered when the user right-clicks a tree node. The event contains the following properties:

* **node**: the node that is clicked on
* **click_event**: the original click event

{% highlight js %}
// bind 'tree.contextmenu' event
$('#tree1').bind(
    'tree.contextmenu',
    function(event) {
        // The clicked node is 'event.node'
        var node = event.node;
        alert(node.name);
    }
);
{% endhighlight %}
