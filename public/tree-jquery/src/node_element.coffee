node = require './node'
Position = node.Position


class NodeElement
    constructor: (node, tree_widget) ->
        @init(node, tree_widget)

    init: (node, tree_widget) ->
        @node = node
        @tree_widget = tree_widget

        if not node.element
            node.element = @tree_widget.element

        @$element = $(node.element)

    getUl: ->
        return @$element.children('ul:first')

    getSpan: ->
        return @$element.children('.jqtree-element').find('span.jqtree-title')

    getLi: ->
        return @$element

    addDropHint: (position) ->
        if position == Position.INSIDE
            return new BorderDropHint(@$element)
        else
            return new GhostDropHint(@node, @$element, position)

    select: ->
        @getLi().addClass('jqtree-selected')

    deselect: ->
        @getLi().removeClass('jqtree-selected')


class FolderElement extends NodeElement
    open: (on_finished, slide=true) ->
        if not @node.is_open
            @node.is_open = true
            $button = @getButton()
            $button.removeClass('jqtree-closed')
            $button.html('')
            $button.append(@tree_widget.renderer.opened_icon_element.cloneNode(false))

            doOpen = =>
                @getLi().removeClass('jqtree-closed')
                if on_finished
                    on_finished()

                @tree_widget._triggerEvent('tree.open', node: @node)

            if slide
                @getUl().slideDown('fast', doOpen)
            else
                @getUl().show()
                doOpen()

    close: (slide=true) ->
        if @node.is_open
            @node.is_open = false
            $button = @getButton()
            $button.addClass('jqtree-closed')
            $button.html('')
            $button.append(@tree_widget.renderer.closed_icon_element.cloneNode(false))

            doClose = =>
                @getLi().addClass('jqtree-closed')

                @tree_widget._triggerEvent('tree.close', node: @node)

            if slide
                @getUl().slideUp('fast', doClose)
            else
                @getUl().hide()
                doClose()

    getButton: ->
        return @$element.children('.jqtree-element').find('a.jqtree-toggler')

    addDropHint: (position) ->
        if not @node.is_open and position == Position.INSIDE
            return new BorderDropHint(@$element)
        else
            return new GhostDropHint(@node, @$element, position)


class BorderDropHint
    constructor: ($element) ->
        $div = $element.children('.jqtree-element')
        width = $element.width() - 4

        @$hint = $('<span class="jqtree-border"></span>')
        $div.append(@$hint)

        @$hint.css(
            width: width
            height: $div.outerHeight() - 4
        )

    remove: ->
        @$hint.remove()


class GhostDropHint
    constructor: (node, $element, position) ->
        @$element = $element

        @node = node
        @$ghost = $('<li class="jqtree_common jqtree-ghost"><span class="jqtree_common jqtree-circle"></span><span class="jqtree_common jqtree-line"></span></li>')

        if position == Position.AFTER
            @moveAfter()
        else if position == Position.BEFORE
            @moveBefore()
        else if position == Position.INSIDE
            if node.isFolder() and node.is_open
                @moveInsideOpenFolder()
            else
                @moveInside()

    remove: ->
        @$ghost.remove()

    moveAfter: ->
        @$element.after(@$ghost)

    moveBefore: ->
        @$element.before(@$ghost)

    moveInsideOpenFolder: ->
        $(@node.children[0].element).before(@$ghost)

    moveInside: ->
        @$element.after(@$ghost)
        @$ghost.addClass('jqtree-inside')


module.exports =
    FolderElement: FolderElement
    NodeElement: NodeElement
