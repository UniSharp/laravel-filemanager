---
title: loadData
name: functions-loaddata
---

**function loadData(data);**

**function loadData(data, parent_node);**

Load data in the tree. The data is array of nodes.

You can **replace the whole tree** or you can **load a subtree**.

{% highlight js %}
// Assuming the tree exists
var new_data = [
    {
        label: 'node1',
        children: [
            { label: 'child1' },
            { label: 'child2' }
        ]
    },
    {
        label: 'node2',
        children: [
            { label: 'child3' }
        ]
    }
];
$('#tree1').tree('loadData', new_data);
{% endhighlight %}

Load a subtree:

{% highlight js %}
// Get node by id (this assumes that the nodes have an id)
var node = $('#tree1').tree('getNodeById', 100);

// Add new nodes
var data = [
    { label: 'new node' },
    { label: 'another new node' }
];
$('#tree1').tree('loadData', data, node);
{% endhighlight %}
