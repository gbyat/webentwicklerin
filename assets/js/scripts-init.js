window.addEventListener('scroll', function() {
    var offset = 130;
    var duration = 500;
    if (window.scrollY > offset) {
        document.getElementById('gototop').classList.remove('hidden', 'fadeOut');
        document.getElementById('gototop').classList.add('fadeIn');
    } else {
        document.getElementById('gototop').classList.remove('fadeIn');
        document.getElementById('gototop').classList.add('fadeOut');
    }
});

document.addEventListener('DOMContentLoaded', function() {
   resizeVideoFrame();
});

window.addEventListener('resize', function() {
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