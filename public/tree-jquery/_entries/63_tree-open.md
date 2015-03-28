---
title: tree.open
name: event-tree-open
---

Called when a node is opened.

{% highlight js %}
$('#tree1').bind(
    'tree.open',
    function(e) {
        console.log(e.node);
    }
);
{% endhighlight %}
