---
title: dataUrl
name: options-data-url
---

Load the node data from this url.

{% highlight js %}
$('#tree1').tree({
   dataUrl: '/example_data.json' 
});
{% endhighlight %}

You can also set the **data-url** attribute on the dom element:

{% highlight html %}
<div id="tree1" data-url="/example_data.json"></div>
<script>
    $('#tree1').tree();
</script>
{% endhighlight %}
