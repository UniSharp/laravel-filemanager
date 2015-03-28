---
title: autoOpen
name: options-autoopen
---

Open nodes initially.

* **true**: open all nodes.
* **false (default)**: do nothing
* **n**: open n levels

Open all nodes initially:

{% highlight js %}
$('#tree1').tree({
    data: data,
    autoOpen: true
});
{% endhighlight %}

Open first level nodes:

{% highlight js %}
$('#tree1').tree({
    data: data,
    autoOpen: 0
});
{% endhighlight %}
