window.addEventListener('scroll', function () {
    var offset = 130;
    var duration = 500;
    removeAnchorFromURL();

    var gototopElement = document.getElementById('gototop');
    if (!gototopElement) return; // Exit if element doesn't exist yet

    if (window.scrollY > offset) {
        gototopElement.classList.remove('hidden', 'fadeOut');
        gototopElement.classList.add('fadeIn');
    } else {
        gototopElement.classList.remove('fadeIn');
        gototopElement.classList.add('fadeOut');
    }
});

function removeAnchorFromURL() {
    setTimeout(function () {
        if (window.location.hash) {
            var noHashURL = window.location.href.replace(window.location.hash, '');
            window.history.replaceState(null, null, noHashURL);
        }
    }, 2000);
}

document.addEventListener('DOMContentLoaded', function () {
    resizeVideoFrame();
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