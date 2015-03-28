---
title: onCanSelectNode
name: options-oncanselectnode
---

You can set a function to override if a node can be selected. The function gets a node as parameter, and must return true or false.

For this to work, the option 'selectable' must be 'true'.

{% highlight js %}
// Example: nodes with children cannot be selected
$('#tree1').tree({
    data: data,
    selectable: true
    onCanSelectNode: function(node) {
        if (node.children.length == 0) {
            // Nodes without children can be selected
            return true;
        }
        else {
            // Nodes with children cannot be selected
            return false;
        }
    }
});
{% endhighlight %}
