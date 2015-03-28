---
title: Tutorial
name: Tutorial
---

Include [jQuery](http://code.jquery.com/jquery.min.js)

{% highlight html %}
<script src="jquery.min.js"></script>
{% endhighlight %}

Include tree.jquery.js:

{% highlight html %}
<script src="tree.jquery.js"></script>
{% endhighlight %}

Include jqtree.css:

{% highlight html %}
<link rel="stylesheet" href="jqtree.css">
{% endhighlight %}

Optionally, for saveState include [jquery-cookie](https://github.com/carhartl/jquery-cookie):

{% highlight html %}
<script src="jquery.cookie.js"></script>
{% endhighlight %}

Create a div.

{% highlight html %}
<div id="tree1"></div>
{% endhighlight %}

Create tree data.

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
{% endhighlight %}

Create tree widget.

{% highlight js %}
$(function() {
    $('#tree1').tree({
        data: data
    });
});
{% endhighlight %}

Alternatively, get the data from the server.

{% highlight js %}
$.getJSON(
    '/some_url/',
    function(data) {
        $('#tree1').tree({
            data: data
        });
    }
);
{% endhighlight %}
