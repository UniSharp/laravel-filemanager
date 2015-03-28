util = require './util'

indexOf = util.indexOf
isInt = util.isInt


class SaveStateHandler
    constructor: (tree_widget) ->
        @tree_widget = tree_widget

    saveState: ->
        state = JSON.stringify(@getState())

        if @tree_widget.options.onSetStateFromStorage
            @tree_widget.options.onSetStateFromStorage(state)
        else if @supportsLocalStorage()
            localStorage.setItem(
                @getCookieName(),
                state
            )
        else if $.cookie
            $.cookie.raw = true
            $.cookie(
                @getCookieName(),
                state,
                {path: '/'}
            )

    getStateFromStorage: ->
        json_data = @_loadFromStorage()

        if json_data
            return @_parseState(json_data)
        else
            return null

    _parseState: (json_data) ->
        state = $.parseJSON(json_data)

        # Check if selected_node is an int (instead of an array)
        if state and state.selected_node and isInt(state.selected_node)
            # Convert to array
            state.selected_node = [state.selected_node]

        return state

    _loadFromStorage: ->
        if @tree_widget.options.onGetStateFromStorage
            return @tree_widget.options.onGetStateFromStorage()
        else if @supportsLocalStorage()
            return localStorage.getItem(
                @getCookieName()
            )
        else if $.cookie
            $.cookie.raw = true
            return $.cookie(@getCookieName())
        else
            return null

    getState: ->
        getOpenNodeIds = =>
            open_nodes = []

            @tree_widget.tree.iterate((node) =>
                if (
                    node.is_open and
                    node.id and
                    node.hasChildren()
                )
                    open_nodes.push(node.id)
                return true
            )

            return open_nodes

        getSelectedNodeIds = =>
            return (n.id for n in @tree_widget.getSelectedNodes())

        return {
            open_nodes: getOpenNodeIds(),
            selected_node: getSelectedNodeIds()
        }

    # Set initial state
    # Don't handle nodes that are loaded on demand
    #
    # result: must load on demand
    setInitialState: (state) ->
        if not state
            return false
        else
            must_load_on_demand = @_openInitialNodes(state.open_nodes)

            @_selectInitialNodes(state.selected_node)

            return must_load_on_demand

    _openInitialNodes: (node_ids) ->
        must_load_on_demand = false

        for node_id in node_ids
            node = @tree_widget.getNodeById(node_id)

            if node
                if not node.load_on_demand
                    node.is_open = true
                else
                    must_load_on_demand = true

        return must_load_on_demand

    _selectInitialNodes: (node_ids) ->
        select_count = 0

        for node_id in node_ids
            node = @tree_widget.getNodeById(node_id)

            if node
                select_count += 1

                @tree_widget.select_node_handler.addToSelection(node)

        return select_count != 0

    setInitialStateOnDemand: (state) ->
        if state
            @_setInitialStateOnDemand(state.open_nodes, state.selected_node)

    _setInitialStateOnDemand: (node_ids, selected_nodes) ->
        openNodes = =>
            new_nodes_ids = []

            for node_id in node_ids
                node = @tree_widget.getNodeById(node_id)

                if not node
                    new_nodes_ids.push(node_id)
                else
                    if not node.is_loading
                        if node.load_on_demand
                            loadAndOpenNode(node)
                        else
                            @tree_widget._openNode(node, false)

            node_ids = new_nodes_ids

            if @_selectInitialNodes(selected_nodes)
                @tree_widget._refreshElements()

        loadAndOpenNode = (node) =>
            @tree_widget._openNode(node, false, openNodes)

        openNodes()

    getCookieName: ->
        if typeof @tree_widget.options.saveState is 'string'
            return @tree_widget.options.saveState
        else
            return 'tree'

    supportsLocalStorage: ->
        testSupport = ->
            # Is local storage supported?
            if not localStorage?
                return false
            else
                # Check if it's possible to store an item. Safari does not allow this in private browsing mode.
                try
                    key = '_storage_test'
                    sessionStorage.setItem(key, true);
                    sessionStorage.removeItem(key)
                catch error
                    return false

                return true

        if not @_supportsLocalStorage?
            @_supportsLocalStorage = testSupport()

        return @_supportsLocalStorage

    getNodeIdToBeSelected: ->
        state = @getStateFromStorage()

        if state and state.selected_node
            return state.selected_node[0]
        else
            return null


module.exports = SaveStateHandler
