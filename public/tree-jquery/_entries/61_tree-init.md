---
title: tree.init
name: event-tree-init
---

Called when the tree is initialized. This is particularly useful when the data is loaded from the server.

{% highlight js %}
$('#tree1').bind(
    'tree.init',
    function() {
        // initializing code
    }
);
{% endhighlight %}
