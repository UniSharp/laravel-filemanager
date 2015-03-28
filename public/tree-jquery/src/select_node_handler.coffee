class SelectNodeHandler
    constructor: (tree_widget) ->
        @tree_widget = tree_widget
        @clear()

    getSelectedNode: ->
        selected_nodes = @getSelectedNodes()

        if selected_nodes.length
            return selected_nodes[0]
        else
            return  false

    getSelectedNodes: ->
        if @selected_single_node
            return [@selected_single_node]
        else
            selected_nodes = []

            for id of @selected_nodes
                node = @tree_widget.getNodeById(id)
                if node
                    selected_nodes.push(node)

            return selected_nodes

    getSelectedNodesUnder: (parent) ->
        if @selected_single_node
            if parent.isParentOf(@selected_single_node)
                return [@selected_single_node]
            else
                return []
        else
            selected_nodes = []

            for id of @selected_nodes
                node = @tree_widget.getNodeById(id)
                if node and parent.isParentOf(node)
                    selected_nodes.push(node)

            return selected_nodes

    isNodeSelected: (node) ->
        if node.id
            return @selected_nodes[node.id]
        else if @selected_single_node
            return @selected_single_node.element == node.element
        else
            return false

    clear: ->
        @selected_nodes = {}
        @selected_single_node = null

    removeFromSelection: (node, include_children=false) ->
        if not node.id
            if @selected_single_node && node.element == @selected_single_node.element
                @selected_single_node = null
        else
            delete @selected_nodes[node.id]

            if include_children
                node.iterate(
                    (n) =>
                        delete @selected_nodes[node.id]
                        return true
                )

    addToSelection: (node) ->
        if node.id
            @selected_nodes[node.id] = true
        else
            @selected_single_node = node


module.exports = SelectNodeHandler
