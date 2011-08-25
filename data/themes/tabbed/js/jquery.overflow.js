/*!
 * jQuery Text Overflow v0.7
 *
 * Licensed under the new BSD License.
 * Copyright 2009-2010, Bram Stein
 * All rights reserved.
 */
/*global jQuery, document, setInterval*/
(function ($) {
    var style = document.documentElement.style,
    hasTextOverflow = ('textOverflow' in style || 'OTextOverflow' in style),

    domSplit = function (root, maxIndex) {
        var index = 0, result = [],
        domSplitAux = function (nodes) {
            var i = 0, tmp;

            if (index > maxIndex) {
                return;
            }

            for (i = 0; i < nodes.length; i += 1) {
                if (nodes[i].nodeType === 1) {
                    tmp = nodes[i].cloneNode(false);
                    result[result.length - 1].appendChild(tmp);
                    result.push(tmp);
                    domSplitAux(nodes[i].childNodes);
                    result.pop();
                } else if (nodes[i].nodeType === 3) {
                    if (index + nodes[i].length < maxIndex) {
                        result[result.length - 1].appendChild(nodes[i].cloneNode(false));
                    } else {
                        tmp = nodes[i].cloneNode(false);
                        tmp.textContent = $.trim(tmp.textContent.substring(0, maxIndex - index));
                        result[result.length - 1].appendChild(tmp);	
                    }
                    index += nodes[i].length;
                } else {
                    result.appendChild(nodes[i].cloneNode(false));
                }
            }
        };
        result.push(root.cloneNode(false));
        domSplitAux(root.childNodes);
        return $(result.pop().childNodes);
    };

    $.extend($.fn, {
        textOverflow: function (str, autoUpdate) {
            var more = str || '&#x2026;';
            
            console.log(hasTextOverflow);
            console.log(this);
            
            if (!hasTextOverflow) {
                return this.each(function () {
                    var element = $(this),

                    // the clone element we modify to measure the width 
                    clone = element.clone(),

                    // we save a copy so we can restore it if necessary
                    originalElement = element.clone(),
                    originalText = element.text(),
                    originalWidth = element.width(),
                    low = 0, mid = 0,
                    high = originalText.length,
                    reflow = function () {
                        if (originalWidth !== element.width()) {
                            element.replaceWith(originalElement);
                            element = originalElement;
                            originalElement = element.clone();
                            element.textOverflow(str, false);
                            originalWidth = element.width();								
                        }
                    };

                    element.after(clone.hide().css({
                        'position': 'absolute',
                        'width': 'auto',
                        'overflow': 'visible',
                        'max-width': 'inherit'
                    }));	

                    if (clone.width() > originalWidth) {
                        while (low < high) {
                            mid = Math.floor(low + ((high - low) / 2));
                            clone.empty().append(domSplit(originalElement.get(0), mid)).append(more);
                            if (clone.width() < originalWidth) {
                                low = mid + 1;
                            } else {
                                high = mid;
                            }
                        }

                        if (low < originalText.length) {
                            element.empty().append(domSplit(originalElement.get(0), low - 1)).append(more);
                        }
                    }
                    clone.remove();
                    
                    if (autoUpdate) {    
                        setInterval(reflow, 200);
                    }
                });
            } else {
                return this;
            }
        }
    });
})(jQuery);
