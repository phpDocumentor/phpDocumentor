(function ($) {
    var cache = {}, uuid = 0;
    /**
   * Filters elements that have not yet been processed.
   *
   * @param id
   *   If this is a string, it will be used in the class name applied to
   *   the elements for determining whether it has already been processed.
   *   The elements will get a class in the form of id-processed.
   *   Otherwise, this acts as a unique identifier; the id will be a generated
   *   number. If it's a function, it will additionally be called for each
   *   element using .each().
   * @param fn
   *   (Optional) If given, this function will be called for each element that
   *   has not yet been processed. The function should not return a value.
   *
   * @version 1.1
   * @see http://plugins.jquery.com/project/once
   *
   * Copyright (c) 2009 Konstantin Kaefer <mail@kkaefer.com>
   * Dual licensed under the MIT and GPL licenses.
   */
    $.fn.once = function (id, fn) {
        if (typeof id != 'string') {
            // Generate a numeric ID if the id passed can't be used as a CSS class.
            if (!(id in cache)) {
                cache[id] = ++uuid;
            }
            // When there is second parameter, we have to save it so that we can
            // call it later.
            if (!fn) {
                fn = id;
            }
            id = 'jquery-once-' + cache[id];
        }
        // Filter out elements that have been processed already.
        var name = id + '-processed',
        elements = this.not('.' + name).addClass(name);

        return $.isFunction(fn) ? elements.each(fn) : elements;
    };
})(jQuery);