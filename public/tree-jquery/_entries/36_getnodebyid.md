---
title: getNodeById
name: functions-getnodebyid
---

**function getNodeById(id);**

Get a tree node by node-id. This assumes that you have given the nodes in the data a unique id.

{% highlight js %}
var $tree = $('#tree1');
var data = [
    { id: 10, name: 'n1' },
    { id: 11, name: 'n2' }
];

$tree.tree({
    data: data
});
var node = $tree.tree('getNodeById', 10);
{% endhighlight %}
