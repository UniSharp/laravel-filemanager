---
title: tree.click
name: event-tree-click
---

Triggered when a tree node is clicked. The event contains the following properties:

* **node**: the node that is clicked on
* **click_event**: the original click event

{% highlight js %}
// create tree
$('#tree1').tree({
    data: data
});

// bind 'tree.click' event
$('#tree1').bind(
    'tree.click',
    function(event) {
        // The clicked node is 'event.node'
        var node = event.node;
        alert(node.name);
    }
);
{% endhighlight %}

The default action is to select the node. You can prevent the selection by calling **preventDefault**:

{% highlight js %}
$('#tree1').bind(
    'tree.click',
    function(event) {
        event.preventDefault();
    }
);
{% endhighlight %}
