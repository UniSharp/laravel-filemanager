---
title: onIsMoveHandle
name: options-onismovehandle
---

You can override this function to determine if a dom element can be used to move a node.

{% highlight js %}
$('#tree1').tree({
    data: data,
    onIsMoveHandle: function($element) {
        // Only dom elements with 'jqtree-title' class can be used
        // as move handle.
        return ($element.is('.jqtree-title'));
    }
});
{% endhighlight %}
