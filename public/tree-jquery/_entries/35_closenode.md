---
title: closeNode
name: functions-closenode
---

**function closeNode(node);**

**function closeNode(node, slide);**

Close this node. The node must have child nodes.

Parameter **slide**: close the node using a slide animation (default is true).

{% highlight js %}
var node = $tree.tree('getNodeById', 123);
$tree.tree('closeNode', node);
{% endhighlight %}

To close the node without the slide animation, call with **slide** parameter is false.

{% highlight js %}
$tree.tree('closeNode', node, false);
{% endhighlight %}
