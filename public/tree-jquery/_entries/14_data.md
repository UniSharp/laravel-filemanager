---
title: data
name: options-data
---

Define the contents of the tree. The data is a nested array of objects. This option is required.

It looks like this:

{% highlight js %}
var data = [
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
$('#tree1').tree({data: data});
{% endhighlight %}

* label: label of a node (required)
* children: array of child nodes (optional)

You can also include other data in the objects. You can later access this data.

For example, to add an id:

{% highlight js %}
{
    label: 'node1',
    id: 1
}
{% endhighlight %}
