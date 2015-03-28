---
title: onCanMoveTo
name: options-oncanmoveto
---

You can override this function to determine if a node can be moved to a certain position.

{% highlight js %}
$('#tree1').tree({
    data: data,
    dragAndDrop: true,
    onCanMoveTo: function(moved_node, target_node, position) {
        if (target_node.is_menu) {
            // Example: can move inside menu, not before or after
            return (position == 'inside');
        }
        else {
            return true;
        }
    }
});
{% endhighlight %}
