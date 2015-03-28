class ScrollHandler
    constructor: (tree_widget) ->
        @tree_widget = tree_widget
        @previous_top = -1

        @_initScrollParent()

    _initScrollParent: ->
        getParentWithOverflow = =>
            css_values = ['overflow', 'overflow-y']

            hasOverFlow = (el) ->
                for css_value in css_values
                    if $.css(el, css_value) in ['auto', 'scroll']
                        return true

                return false

            if hasOverFlow(@tree_widget.$el[0])
                return @tree_widget.$el

            for el in @tree_widget.$el.parents()
                if hasOverFlow(el)
                    return $(el)

            return null

        setDocumentAsScrollParent = =>
            @scroll_parent_top = 0
            @$scroll_parent = null

        if @tree_widget.$el.css('position') == 'fixed'
            setDocumentAsScrollParent()

        $scroll_parent = getParentWithOverflow()

        if $scroll_parent and $scroll_parent.length and $scroll_parent[0].tagName != 'HTML'
            @$scroll_parent = $scroll_parent
            @scroll_parent_top = @$scroll_parent.offset().top
        else
            setDocumentAsScrollParent()

    checkScrolling: ->
        hovered_area = @tree_widget.dnd_handler.hovered_area

        if hovered_area and hovered_area.top != @previous_top
            @previous_top = hovered_area.top

            if @$scroll_parent
                @_handleScrollingWithScrollParent(hovered_area)
            else
                @_handleScrollingWithDocument(hovered_area)

    _handleScrollingWithScrollParent: (area) ->
        distance_bottom = @scroll_parent_top + @$scroll_parent[0].offsetHeight - area.bottom

        if distance_bottom < 20
            @$scroll_parent[0].scrollTop += 20
            @tree_widget.refreshHitAreas()
            @previous_top = -1
        else if (area.top - @scroll_parent_top) < 20
            @$scroll_parent[0].scrollTop -= 20
            @tree_widget.refreshHitAreas()
            @previous_top = -1

    _handleScrollingWithDocument: (area) ->
        distance_top = area.top - $(document).scrollTop()

        if distance_top < 20
            $(document).scrollTop($(document).scrollTop() - 20)
        else if $(window).height() - (area.bottom - $(document).scrollTop()) < 20
            $(document).scrollTop($(document).scrollTop() + 20)

    scrollTo: (top) ->
        if @$scroll_parent
            @$scroll_parent[0].scrollTop = top
        else
            tree_top = @tree_widget.$el.offset().top
            $(document).scrollTop(top + tree_top)

    isScrolledIntoView: (element) ->
        $element = $(element)

        if @$scroll_parent
            view_top = 0
            view_bottom = @$scroll_parent.height()

            element_top = $element.offset().top - @scroll_parent_top
            element_bottom = element_top + $element.height()
        else
            view_top = $(window).scrollTop()
            view_bottom = view_top + $(window).height()

            element_top = $element.offset().top
            element_bottom = element_top + $element.height()

        return (element_bottom <= view_bottom) and (element_top >= view_top)


module.exports = ScrollHandler
