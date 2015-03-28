###
Copyright 2013 Marco Braak

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
###

class SimpleWidget
    defaults: {}

    constructor: (el, options) ->
        @$el = $(el)
        @options = $.extend({}, @defaults, options)

    destroy: ->
        @_deinit()

    _init: ->
        null

    _deinit: ->
        null

    @register = (widget_class, widget_name) ->
        getDataKey = ->
            return "simple_widget_#{widget_name}"

        getWidgetData = (el, data_key) ->
            widget = $.data(el, data_key)

            if widget and (widget instanceof SimpleWidget)
                return widget
            else
                return null

        createWidget = ($el, options) ->
            data_key = getDataKey()

            for el in $el
                existing_widget = getWidgetData(el, data_key)

                if not existing_widget
                    widget = new widget_class(el, options)

                    if not $.data(el, data_key)
                        $.data(el, data_key, widget)

                    # Call init after setting data, so we can call methods
                    widget._init()

            return $el

        destroyWidget = ($el) ->
            data_key = getDataKey()

            for el in $el
                widget = getWidgetData(el, data_key)

                if widget
                    widget.destroy()

                $.removeData(el, data_key)

        callFunction = ($el, function_name, args) ->
            result = null

            for el in $el
                widget = $.data(el, getDataKey())

                if widget and (widget instanceof SimpleWidget)
                    widget_function = widget[function_name]

                    if widget_function and (typeof widget_function == 'function')
                        result = widget_function.apply(widget, args)

            return result

        $.fn[widget_name] = (argument1, args...) ->
            $el = this

            if argument1 is undefined or typeof argument1 == 'object'
                options = argument1
                return createWidget($el, options)
            else if typeof argument1 == 'string' and argument1[0] != '_'
                function_name = argument1

                if function_name == 'destroy'
                    return destroyWidget($el)
                else
                    return callFunction($el, function_name, args)


module.exports = SimpleWidget
