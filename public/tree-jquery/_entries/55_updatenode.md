---
title: updateNode
name: functions-updatenode
---

**function updateNode(node, label);**

**function updateNode(node, data);**

Update the title of a node. You can also update the data.

Update the label:

{% highlight js %}
var node = $tree.tree('getNodeById', 123);

$tree.tree('updateNode', node, 'new label');
{% endhighlight %}

Update the data (including the label)

{% highlight js %}
var node = $tree.tree('getNodeById', 123);

$tree.tree(
    'updateNode',
    node,
    {
        label: 'new label',
        other_property: 'abc'
    }
);
{% endhighlight %}
