---
title: appendNode
name: functions-appendnode
---

**function appendNode(new_node_info, parent_node);**

Add a node to this parent node. If **parent_node<** is empty, then the new node becomes a root node.

{% highlight js %}
var parent_node = $tree.tree('getNodeById', 123);

$tree.tree(
    'appendNode',
    {
        label: 'new_node',
        id: 456
    },
    parent_node
);
{% endhighlight %}

To add a root node, leave *parent_node* empty:

{% highlight js %}
$tree.tree(
    'appendNode',
    {
        label: 'new_node',
        id: 456
    }
);
{% endhighlight %}
