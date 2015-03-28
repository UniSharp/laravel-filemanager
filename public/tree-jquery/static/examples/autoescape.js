$(function() {
    var data = [
        {
            label: 'examples',
            children: [
                { label: '<a href="example1.html">Example 1</a>' },
                { label: '<a href="example2.html">Example 2</a>' },
                '<a href="example3.html">Example 3</a>'
            ]
        }
    ];

    // set autoEscape to false
    $('#tree1').tree({
        data: data,
        autoEscape: false,
        autoOpen: true
    });
});
