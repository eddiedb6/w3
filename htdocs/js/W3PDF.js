(function($) {
    "use strict";

    var _baseID = null;
    var _pdf = null;
    var _pageNum = 1;
    var _canvas = null;
    var _context = null;
    var _pageRendering = false;
    var _pageNumPending = null;
    
    function onPrevPage() {
        if (_pageNum <= 1) {
            return;
        }
        --_pageNum;
        queueRenderPage(_pageNum);
    }

    function onNextPage() {
        if (_pageNum >= _pdf.numPages) {
            return;
        }
        ++_pageNum;
        queueRenderPage(_pageNum);
    }

    function queueRenderPage(num) {
        if (_pageRendering) {
            _pageNumPending = num;
        } else {
            renderPage(num);
        }
    }

    function adjustPageControls(num) {
        $("#" + _baseID + "Num").text(num);

        if (num <= 1) {
            $("#" + _baseID + "Pre").attr("disabled", true);
        } else {
            $("#" + _baseID + "Pre").attr("disabled", false);
        }

        if (num >= _pdf.numPages) {
            $("#" + _baseID + "Next").attr("disabled", true);
        } else {
            $("#" + _baseID + "Next").attr("disabled", false);
        }
    }
    
    function renderPage(num) {
        _pageRendering = true;
        _pdf.getPage(num).then(function(page) {
            W3LogDebug("Page rendering: " + num);
            
            var scale = 1.0;
            var viewport = page.getViewport({scale: scale});

            _canvas.height = viewport.height;
            _canvas.width = viewport.width;

            var renderContext = {
                canvasContext: _context,
                viewport: viewport
            };
            var renderTask = page.render(renderContext);
            renderTask.promise.then(function () {
                W3LogDebug("Page rendered: " + num);
                
                _pageRendering = false;
                adjustPageControls(num);
                
                if (_pageNumPending !== null) {
                    renderPage(_pageNumPending);
                    _pageNumPending = null;
                }
            });
        });
    }
    
    function InitPDFUIImpl()
    {
        var thisElement = $(this);
        var thisHtml = thisElement.get(0).outerHTML;
        _baseID = thisElement.attr("id");

        var canvasPattern = new RegExp("^<canvas.*canvas>$");
        if (!canvasPattern.test(thisHtml)) {
            W3LogError("Init PDF on wrong element");
            return;
        }

        thisHtml =
            "<div id='" + _baseID + "WrapperPanel'>" +
            " <canvas id='" + _baseID + "Canvas'></canvas>" +
            " <table><tbody><tr>" +
            "  <td><button id='" + _baseID + "Pre' onclick='' type='button' disabled='disabled'>&lt;</button></td>" +
            "  <td><label id='" + _baseID + "Num'>0</label></td>" +
            "  <td><label> of </label></td>" +
            "  <td><label id='" + _baseID + "TotalNum'>0</label></td>" +
            "  <td><button id='" + _baseID + "Next' onclick='' type='button' disabled='disabled'>&gt;</button></td>" +
            " </tr></tbody></table>" +
            " <textarea id='" + _baseID + "' style='display:none'></textarea>" +
            "</div>";
        thisElement.replaceWith(thisHtml);

        var nextBtn = document.getElementById(_baseID + "Next");
        if (nextBtn != null) {
            nextBtn.addEventListener('click', onNextPage);
        } else {
            W3LogWarning("PDF next button is not found");
        }

        var preBtn = document.getElementById(_baseID + "Pre");
        if (nextBtn != null) {
            preBtn.addEventListener('click', onPrevPage);
        } else {
            W3LogWarning("PDF previous button is not found");
        }
    }

    function CreatePDFImpl(noteID)
    {
        var url = "tmp/" + noteID + ".pdf";
        var canvasID = _baseID + "Canvas";
        _canvas = document.getElementById(canvasID);
        if (_canvas == null) {
            W3LogError("No PDF canvas found");
            return;
        }

        _context = _canvas.getContext('2d');
        var pdfjsLib = window['pdfjs-dist/build/pdf'];
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'pdf/build/pdf.worker.js';

        // Asynchronous download of PDF
        var loadingTask = pdfjsLib.getDocument(url);
        loadingTask.promise.then(
            function(pdf) {
                W3LogDebug('PDF loaded');

                _pdf = pdf;
                $("#" + _baseID + "TotalNum").text(_pdf.numPages);
                renderPage(_pageNum);
            },
            function (reason) {
                W3LogError(reason);
            }
        );
    }
    
    $.fn.extend({
        W3CreatePDF: CreatePDFImpl,
        W3PDF: InitPDFUIImpl
    });
    
} (jQuery));
