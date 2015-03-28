Position =
    getName: (position) ->
        return Position.strings[position - 1]

    nameToIndex: (name) ->
        for i in [1..Position.strings.length]
            if Position.strings[i - 1] == name
                return i
        return 0

Position.BEFORE = 1
Position.AFTER = 2
Position.INSIDE = 3
Position.NONE = 4

Position.strings = ['before', 'after', 'inside', 'none']

class Node
    constructor: (o, is_root=false, node_class=Node) ->
        @setData(o)

        @children = []
        @parent = null

        if is_root
            @id_mapping = {}
            @tree = this
            @node_class = node_class

    setData: (o) ->
        if typeof o != 'object'
            @name = o
        else
            for key, value of o
                if key == 'label'
                    # todo: node property is 'name', but we use 'label' here
                    @name = value
                else
                    @[key] = value

    # Init Node from data without making it the root of the tree
    initFromData: (data) ->
        addNode = (node_data) =>
            @setData(node_data)

            if node_data.children
                addChildren(node_data.children)

        addChildren = (children_data) =>
            for child in children_data
                node = new @tree.node_class('')
                node.initFromData(child)
                @addChild(node)
            return null

        addNode(data)
        return null

    ###
    Create tree from data.

    Structure of data is:
    [
        {
            label: 'node1',
            children: [
                { label: 'child1' },
                { label: 'child2' }
            ]
        },
        {
            label: 'node2'
        }
    ]
    ###
    loadFromData: (data) ->
        @removeChildren()

        for o in data
            node = new @tree.node_class(o)
            @addChild(node)

            if typeof o == 'object' and o.children
                node.loadFromData(o.children)

        return null

    ###
    Add child.

    tree.addChild(
        new Node('child1')
    );
    ###
    addChild: (node) ->
        @children.push(node)
        node._setParent(this)

    ###
    Add child at position. Index starts at 0.

    tree.addChildAtPosition(
        new Node('abc'),
        1
    );
    ###
    addChildAtPosition: (node, index) ->
        @children.splice(index, 0, node)
        node._setParent(this)

    _setParent: (parent) ->
        @parent = parent
        @tree = parent.tree
        @tree.addNodeToIndex(this)

    ###
    Remove child. This also removes the children of the node.

    tree.removeChild(tree.children[0]);
    ###
    removeChild: (node) ->
        # remove children from the index
        node.removeChildren()

        @_removeChild(node)

    _removeChild: (node) ->
        @children.splice(
            @getChildIndex(node),
            1
        )
        @tree.removeNodeFromIndex(node)        

    ###
    Get child index.

    var index = getChildIndex(node);
    ###
    getChildIndex: (node) ->
        return $.inArray(node, @children)

    ###
    Does the tree have children?

    if (tree.hasChildren()) {
        //
    }
    ###
    hasChildren: ->
        return @children.length != 0

    isFolder: ->
        return @hasChildren() or @load_on_demand

    ###
    Iterate over all the nodes in the tree.

    Calls callback with (node, level).

    The callback must return true to continue the iteration on current node.

    tree.iterate(
        function(node, level) {
           console.log(node.name);

           // stop iteration after level 2
           return (level <= 2);
        }
    );

    ###
    iterate: (callback) ->
        _iterate = (node, level) ->
            if node.children
                for child in node.children
                    result = callback(child, level)

                    if result and child.hasChildren()
                        _iterate(child, level + 1)
                return null

        _iterate(this, 0)
        return null

    ###
    Move node relative to another node.

    Argument position: Position.BEFORE, Position.AFTER or Position.Inside

    // move node1 after node2
    tree.moveNode(node1, node2, Position.AFTER);
    ###
    moveNode: (moved_node, target_node, position) ->
        if moved_node.isParentOf(target_node)
            # Node is parent of target node. This is an illegal move
            return

        moved_node.parent._removeChild(moved_node)
        if position == Position.AFTER
            target_node.parent.addChildAtPosition(
                moved_node,
                target_node.parent.getChildIndex(target_node) + 1
            )
        else if position == Position.BEFORE
            target_node.parent.addChildAtPosition(
                moved_node,
                target_node.parent.getChildIndex(target_node)
            )
        else if position == Position.INSIDE
            # move inside as first child
            target_node.addChildAtPosition(moved_node, 0)

    ###
    Get the tree as data.
    ###
    getData: ->
        getDataFromNodes = (nodes) =>
            data = []

            for node in nodes
                tmp_node = {}

                for k, v of node
                    if (
                        k not in ['parent', 'children', 'element', 'tree'] and
                        Object.prototype.hasOwnProperty.call(node, k)
                    )
                        tmp_node[k] = v

                if node.hasChildren()
                    tmp_node.children = getDataFromNodes(node.children)

                data.push(tmp_node)

            return data

        return getDataFromNodes(@children)

    getNodeByName: (name) ->
        result = null

        @iterate(
            (node) ->
                if node.name == name
                    result = node
                    return false
                else
                    return true
        )

        return result

    addAfter: (node_info) ->
        if not @parent
            return null
        else
            node = new @tree.node_class(node_info)

            child_index = @parent.getChildIndex(this)
            @parent.addChildAtPosition(node, child_index + 1)
            return node

    addBefore: (node_info) ->
        if not @parent
            return null
        else
            node = new @tree.node_class(node_info)

            child_index = @parent.getChildIndex(this)
            @parent.addChildAtPosition(node, child_index)
            return node

    addParent: (node_info) ->
        if not @parent
            return null
        else
            new_parent = new @tree.node_class(node_info)
            new_parent._setParent(@tree)
            original_parent = @parent

            for child in original_parent.children
                new_parent.addChild(child)

            original_parent.children = []
            original_parent.addChild(new_parent)
            return new_parent

    remove: ->
        if @parent
            @parent.removeChild(this)
            @parent = null

    append: (node_info) ->
        node = new @tree.node_class(node_info)
        @addChild(node)
        return node

    prepend: (node_info) ->
        node = new @tree.node_class(node_info)
        @addChildAtPosition(node, 0)
        return node

    isParentOf: (node) ->
        parent = node.parent

        while parent
            if parent == this
                return true

            parent = parent.parent

        return false

    getLevel: ->
        level = 0
        node = this

        while node.parent
            level += 1
            node = node.parent

        return level

    getNodeById: (node_id) ->
        return @id_mapping[node_id]

    addNodeToIndex: (node) ->
        if node.id?
            @id_mapping[node.id] = node

    removeNodeFromIndex: (node) ->
        if node.id?
            delete @id_mapping[node.id]

    removeChildren: ->
        @iterate(
            (child) =>
                @tree.removeNodeFromIndex(child)
                return true
        )

        @children = []

    getPreviousSibling: ->
        if not @parent
            return null
        else
            previous_index = @parent.getChildIndex(this) - 1
            if previous_index >= 0
                return @parent.children[previous_index]
            else
                return null

    getNextSibling: ->
        if not @parent
            return null
        else
            next_index = @parent.getChildIndex(this) + 1
            if next_index < @parent.children.length
                return @parent.children[next_index]
            else
                return null                

    getNodesByProperty: (key, value) ->
        return @filter(
            (node) ->
                return node[key] == value
        )

    filter: (f) ->
        result = []

        @iterate(
            (node) ->
                if f(node)
                    result.push(node)

                return true
        )

        return result

    getNextNode: (include_children=true) ->
        if include_children and @hasChildren() and @is_open
            # First child
            return @children[0]
        else
            if not @parent
                return null
            else
                next_sibling = @getNextSibling()
                if next_sibling
                    # Next sibling
                    return next_sibling
                else
                    # Next node of parent
                    return @parent.getNextNode(false)

    getPreviousNode: ->
        if not @parent
            return null
        else
            previous_sibling = @getPreviousSibling()
            if previous_sibling
                if not previous_sibling.hasChildren() or not previous_sibling.is_open
                    # Previous sibling
                    return previous_sibling
                else
                    # Last child of previous sibling
                    return previous_sibling.getLastChild()
            else
                # Parent
                if @parent.parent
                    return @parent
                else
                    return null

    getLastChild: ->
        if not @hasChildren()
            return null
        else
            last_child = @children[@children.length - 1]
            if not last_child.hasChildren() or not last_child.is_open
                return last_child
            else
                return last_child.getLastChild()


module.exports =
    Node: Node
    Position: Position
