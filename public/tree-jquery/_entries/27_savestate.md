---
title: saveState
name: options-savestate
---

Save and restore the state of the tree automatically. Saves in a cookie which nodes are opened and selected.

The state is saved in localstorage. In browsers that do not support localstorage, the state is saved in a cookie.
For this to work, please include [jquery-cookie](https://github.com/carhartl/jquery-cookie).

For this to work, you should give each node in the tree data an id field:

{% highlight js %}
{
    label: 'node1',
    id: 123,
    childen: [
        label: 'child1',
        id: 124
    ]
}
{% endhighlight %}

* **true**: save and restore state in a cookie
* **false (default)**: do nothing
* **string**: save state and use this name to store in a cookie

{% highlight js %}
$('#tree1').tree({
    data: data,
    saveState: true
});
{% endhighlight %}

Example: save state in key 'tree1':

{% highlight js %}
$('#tree1').tree({
    data: data,
    saveState: 'tree1'
});
{% endhighlight %}
