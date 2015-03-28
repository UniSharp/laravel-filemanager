---
title: onCanMove
name: options-oncanmove
---

You can override this function to determine if a node can be moved.

{% highlight js %}
$('#tree1').tree({
    data: data,
    dragAndDrop: true,
    onCanMove: function(node) {
        if (! node.parent.parent) {
            // Example: Cannot move root node
            return false;
        }
        else {
            return true;
        }
    }
});
{% endhighlight %}
