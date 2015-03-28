---
title: getState
name: functions-getstate
---

Get the state of tree: which nodes are open and which one is selected?

Returns a javascript object that contains the ids of open nodes and selected nodes:

{% highlight js %}
{
    open_nodes: [1, 2, 3],
    selected_node: [4, 5, 6]
}
{% endhighlight %}

If you want to use this function, then your tree data should include an **id** property for each node.

You can use this function in combination with [setState](#functions-setstate) to save and restore the tree state.
