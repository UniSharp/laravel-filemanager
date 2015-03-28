---
title: dataFilter
name: options-datafilter
---

Process the tree data from the server.

{% highlight js %}
$('#tree1').tree({
    url: '/my/data/',
    dataFilter: function(data) {
        // Example:
        // the server puts the tree data in 'my_tree_data'
        return data['my_tree_data'];
    }
});
{% endhighlight %}
