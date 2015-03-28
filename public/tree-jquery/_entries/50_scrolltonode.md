---
title: scrollToNode
name: functions-scrolltonode
---

**function scrollToNode(node);**

Scroll to this node. This is useful if the tree is in a container div and is scrollable.

{% highlight js %}
var node = $tree.tree('getNodeById', 1);
$tree.tree('scrollToNode', node);
{% endhighlight %}
