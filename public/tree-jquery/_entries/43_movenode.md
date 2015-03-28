---
title: moveNode
name: functions-movenode
---

**function moveNode(node, target_node, position);**

Move a node. Position can be 'before', 'after' or 'inside'.

{% highlight js %}
var node = $tree.tree('getNodeById', 1);
var target_node = $tree.tree('getNodeById', 2);

$tree.tree('moveNode', node, target_node, 'after');
{% endhighlight %}
