---
title: tree.dblclick
name: event-tree-dblclick
---

The **tree.dblclick** is fired when a tree node is double-clicked. The event contains the following properties:

* **node**: the node that is clicked on
* **click_event**: the original click event

{% highlight js %}
$('#tree1').bind(
    'tree.dblclick',
    function(event) {
        // event.node is the clicked node
        console.log(event.node);
    }
);
{% endhighlight %}
