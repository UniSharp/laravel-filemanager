---
title: getLevel
name: node-functions-getlevel
---

Get the level of a node. The level is distance of a node to the root node.

{% highlight js %}
var node = $('#tree1').tree('getNodeById', 123);

// result is e.g. 2
var level = node.getLevel();
{% endhighlight %}
