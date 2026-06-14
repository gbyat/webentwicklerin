function removeAnchorFromURL() {
    setTimeout(function () {
        var hash = window.location.hash;
        if (!hash) {
            return;
        }
        if (hash === '#site-content' || hash === '#wp--skip-link--target' || hash === '#topofpage') {
            return;
        }
        var noHashURL = window.location.href.replace(hash, '');
        window.history.replaceState(null, null, noHashURL);
    }, 2000);
}

function parseCssLength(value) {
    if (!value) {
        return 0;
    }

    value = String(value).trim();

    if (value.endsWith('px')) {
        return parseFloat(value) || 0;
    }

    if (value.endsWith('rem')) {
        return (parseFloat(value) || 0) * parseFloat(getComputedStyle(document.documentElement).fontSize);
    }

    return parseFloat(value) || 0;
}

function getAdminBarHeight() {
    var rootStyles = getComputedStyle(document.documentElement);
    var cssHeight = parseCssLength(rootStyles.getPropertyValue('--wp-admin--admin-bar--height'));

    if (cssHeight) {
        return cssHeight;
    }

    var adminBar = document.getElementById('wpadminbar');

    return adminBar ? adminBar.offsetHeight : 0;
}

function getTopLevelStickyElements() {
    var siteBlocks = document.querySelector('.wp-site-blocks');
    var main = document.getElementById('site-content');

    if (!siteBlocks) {
        return [];
    }

    var stickies = [];

    siteBlocks.querySelectorAll('*').forEach(function (element) {
        if (window.getComputedStyle(element).position !== 'sticky') {
            return;
        }

        if (main && !(main.compareDocumentPosition(element) & Node.DOCUMENT_POSITION_PRECEDING)) {
            return;
        }

        var parent = element.parentElement;
        var nestedInSticky = false;

        while (parent && parent !== siteBlocks) {
            if (window.getComputedStyle(parent).position === 'sticky') {
                nestedInSticky = true;
                break;
            }

            parent = parent.parentElement;
        }

        if (!nestedInSticky) {
            stickies.push(element);
        }
    });

    return stickies;
}

function getStickyBarsHeight() {
    return getTopLevelStickyElements().reduce(function (total, element) {
        return total + element.getBoundingClientRect().height;
    }, 0);
}

function updateScrollPadding() {
    var total = getAdminBarHeight() + getStickyBarsHeight();

    document.documentElement.style.setProperty('--webentwicklerin-scroll-padding-top', total + 'px');
}

var scrollPaddingTimer;

function scheduleScrollPaddingUpdate() {
    clearTimeout(scrollPaddingTimer);
    scrollPaddingTimer = setTimeout(updateScrollPadding, 100);
}

function initScrollPadding() {
    updateScrollPadding();

    window.addEventListener('resize', scheduleScrollPaddingUpdate);
    window.addEventListener('load', scheduleScrollPaddingUpdate);

    if (typeof ResizeObserver === 'undefined') {
        return;
    }

    var resizeObserver = new ResizeObserver(scheduleScrollPaddingUpdate);
    var siteBlocks = document.querySelector('.wp-site-blocks');

    if (siteBlocks) {
        getTopLevelStickyElements().forEach(function (element) {
            resizeObserver.observe(element);
        });
    }

    var adminBar = document.getElementById('wpadminbar');

    if (adminBar) {
        resizeObserver.observe(adminBar);
    }
}

function prefersReducedMotion() {
    return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

function initScrollToTop() {
    var scrollToTopElements = document.querySelectorAll('.scroll-to-top');
    if (!scrollToTopElements.length) {
        return;
    }

    var offset = 130;
    var reduceMotion = prefersReducedMotion();

    scrollToTopElements.forEach(function (element) {
        element.classList.add('hidden');
    });

    window.addEventListener('scroll', function () {
        removeAnchorFromURL();

        scrollToTopElements.forEach(function (element) {
            if (window.scrollY > offset) {
                if (reduceMotion) {
                    element.classList.remove('hidden', 'fadeIn', 'fadeOut');
                } else {
                    element.classList.remove('hidden', 'fadeOut');
                    element.classList.add('fadeIn');
                }
            } else if (reduceMotion) {
                element.classList.add('hidden');
                element.classList.remove('fadeIn', 'fadeOut');
            } else {
                element.classList.remove('fadeIn');
                element.classList.add('fadeOut');
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', function () {
    resizeVideoFrame();
    initScrollPadding();
    initScrollToTop();
});

window.addEventListener('resize', function () {
    resizeVideoFrame();
});

function resizeVideoFrame() {
    var width = '';
    var iframes = document.querySelectorAll('iframe');
    for (var i = 0; i < iframes.length; i++) {
        var iframe = iframes[i];
        var src = iframe.getAttribute('src');
        if (src && (src.includes('vimeo.com') || src.includes('youtube.com') || src.includes('captivate.fm'))) {
            width = iframe.offsetWidth;
            iframe.style.height = (width * 3 / 5) + 'px';
        }
    }
}
