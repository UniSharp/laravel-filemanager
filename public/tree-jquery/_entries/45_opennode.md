---
title: openNode
name: functions-opennode
---

**function openNode(node);**

**function openNode(node, slide);**

Open this node. The node must have child nodes.

Parameter **slide**: open the node using a slide animation (default is true).

{% highlight js %}
// create tree
var $tree = $('#tree1');
$tree.tree({
    data: data
});

var node = $tree.tree('getNodeById', 123);
$tree.tree('openNode', node);
{% endhighlight %}

To open the node without the slide animation, call with **slide** parameter is false.

{% highlight js %}
$tree.tree('openNode', node, false);
{% endhighlight %}
