---
title: selectNode
name: functions-selectnode
---

**function selectNode(node);**

**function selectNode(null);**

Select this node.

You can deselect the current node by calling **selectNode(null)**.

{% highlight js %}
// create tree
var $tree = $('#tree1');
$tree.tree({
    data: data,
    selectable: true
});

var node = $tree.tree('getNodeById', 123);
$tree.tree('selectNode', node);
{% endhighlight %}
