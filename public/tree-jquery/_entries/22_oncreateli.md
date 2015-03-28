---
title: onCreateLi
name: options-oncreateli
---

The function is called for each created node. You can use this to define extra html.

{% highlight js %}
$('#tree1).tree({
    data: data,
    onCreateLi: function(node, $li) {
        // Add 'icon' span before title
        $li.find('.jqtree-title').before('<span class="icon"></span>');
    }
});
{% endhighlight %}
