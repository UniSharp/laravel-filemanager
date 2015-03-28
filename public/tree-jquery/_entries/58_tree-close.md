---
title: tree.close
name: event-tree-close
---

Called when a node is closed.

{% highlight js %}
$('#tree1').bind(
    'tree.close',
    function(e) {
        console.log(e.node);
    }
);
{% endhighlight %}
