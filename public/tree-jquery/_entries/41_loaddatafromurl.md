---
title: loadDataFromUrl
name: functions-loaddatafromurl
---

**function loadDataFromUrl(url);**

**function loadDataFromUrl(url, parent_node);**

**function loadDataFromUrl(parent_node);**

Load data in the tree from an url using ajax. You can **replace the whole tree** or you can **load a subtree**.

{% highlight js %}
$('#tree1').tree('loadDataFromUrl', '/category/tree/');
{% endhighlight %}

Load a subtree:

{% highlight js %}
var node = $('#tree1').tree('getNodeById', 123);
$('#tree1').tree('loadDataFromUrl', '/category/tree/123', node);
{% endhighlight %}

You can also omit the url. In this case jqTree will generate a url for you. This is very useful if you use the load-on-demand feature:

{% highlight js %}
var $tree = $('#tree1');

$tree.tree({
    dataUrl: '/my_data/'
});

var node = $tree.tree('getNodeById', 456);

// jqTree will load data from /my_data/?node=456
$tree.tree('loadDataFromUrl', node);
{% endhighlight %}

You can also add an **on_finished** callback parameter that will be called when the data is loaded:

**function loadDataFromUrl(url, parent_node, on_finished);**

**function loadDataFromUrl(parent_node, on_finished);**

{% highlight js %}
$('#tree1').tree(
    'loadDataFromUrl',
    '/category/tree/123',
    null,
    function() {
        alert('data is loaded');
    }
);
{% endhighlight %}
